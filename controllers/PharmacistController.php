<?php
require_once __DIR__ . '/../includes/BaseController.php';

/**
 * PharmacistController
 * Handles all medicine/pharmacy related functions
 * Separated from ReceptionistController for role-based access control
 */
class PharmacistController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        // Allow access for pharmacist role or admin
        $this->requireRole(['pharmacist', 'admin']);
    }

    public function dashboard()
    {
        // Get pending prescriptions count
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT pr.patient_id) as pending_patients,
                   COUNT(pr.id) as pending_prescriptions
            FROM prescriptions pr
            WHERE pr.status IN ('pending', 'partial')
        ");
        $stmt->execute();
        $pending_stats = $stmt->fetch();

        // Get today's dispensed count
        $stmt = $this->pdo->prepare("
            SELECT COUNT(pr.id) as dispensed_today,
                   COUNT(DISTINCT pr.patient_id) as patients_served
            FROM prescriptions pr
            WHERE pr.status = 'dispensed' AND DATE(pr.dispensed_at) = CURDATE()
        ");
        $stmt->execute();
        $today_stats = $stmt->fetch();

        // Get low stock medicines (using medicine_batches)
        $stmt = $this->pdo->prepare("
            SELECT m.id, m.name, m.generic_name,
                   COALESCE(SUM(mb.quantity_remaining), 0) as total_stock,
                   MIN(mb.expiry_date) as nearest_expiry
            FROM medicines m
            LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
            GROUP BY m.id, m.name, m.generic_name
            HAVING total_stock <= 10 OR nearest_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ORDER BY total_stock ASC
            LIMIT 10
        ");
        $stmt->execute();
        $low_stock_medicines = $stmt->fetchAll();

        // Get expiring medicines
        $stmt = $this->pdo->prepare("
            SELECT m.id, m.name, mb.batch_number, mb.quantity_remaining, mb.expiry_date,
                   DATEDIFF(mb.expiry_date, CURDATE()) as days_until_expiry
            FROM medicine_batches mb
            JOIN medicines m ON mb.medicine_id = m.id
            WHERE mb.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)
              AND mb.quantity_remaining > 0
            ORDER BY mb.expiry_date ASC
            LIMIT 10
        ");
        $stmt->execute();
        $expiring_medicines = $stmt->fetchAll();

        // Get recent dispensing activity
        $stmt = $this->pdo->prepare("
            SELECT pr.*, 
                   CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                   m.name as medicine_name,
                   u.first_name as dispensed_by_name
            FROM prescriptions pr
            JOIN patients p ON pr.patient_id = p.id
            JOIN medicines m ON pr.medicine_id = m.id
            LEFT JOIN users u ON pr.dispensed_by = u.id
            WHERE pr.status = 'dispensed'
            ORDER BY pr.dispensed_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $recent_dispensing = $stmt->fetchAll();

        $this->render('pharmacist/dashboard', [
            'pending_stats' => $pending_stats,
            'today_stats' => $today_stats,
            'low_stock_medicines' => $low_stock_medicines,
            'expiring_medicines' => $expiring_medicines,
            'recent_dispensing' => $recent_dispensing,
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    public function prescriptions()
    {
        // Get pending prescriptions grouped by patient
        $stmt = $this->pdo->prepare("
            SELECT 
                pr.id as prescription_id,
                pr.patient_id,
                pr.visit_id,
                pr.quantity_prescribed,
                pr.quantity_dispensed,
                pr.status,
                pr.dosage,
                pr.frequency,
                pr.duration,
                pr.instructions,
                pr.created_at,
                p.first_name,
                p.last_name,
                p.registration_number,
                m.id as medicine_id,
                m.name as medicine_name,
                m.generic_name,
                m.unit_price,
                COALESCE(SUM(mb.quantity_remaining), 0) as stock_available,
                (pr.quantity_prescribed * m.unit_price) as prescription_cost,
                -- Check if payment has been made
                CASE WHEN EXISTS (
                    SELECT 1 FROM payments 
                    WHERE visit_id = pr.visit_id 
                    AND payment_type = 'medicine' 
                    AND payment_status = 'paid'
                ) THEN 1 ELSE 0 END as is_paid
            FROM prescriptions pr
            JOIN patients p ON pr.patient_id = p.id
            JOIN medicines m ON pr.medicine_id = m.id
            LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id AND mb.quantity_remaining > 0
            WHERE pr.status IN ('pending', 'partial')
            GROUP BY pr.id, pr.patient_id, pr.visit_id, pr.quantity_prescribed, pr.quantity_dispensed,
                     pr.status, pr.dosage, pr.frequency, pr.duration, pr.instructions, pr.created_at,
                     p.first_name, p.last_name, p.registration_number,
                     m.id, m.name, m.generic_name, m.unit_price
            ORDER BY pr.created_at ASC
        ");
        $stmt->execute();
        $raw_prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group prescriptions by patient
        $pending_patients = [];
        foreach ($raw_prescriptions as $prescription) {
            $patient_id = $prescription['patient_id'];
            if (!isset($pending_patients[$patient_id])) {
                $pending_patients[$patient_id] = [
                    'patient_id' => $patient_id,
                    'first_name' => $prescription['first_name'],
                    'last_name' => $prescription['last_name'],
                    'registration_number' => $prescription['registration_number'],
                    'visit_id' => $prescription['visit_id'],
                    'prescriptions' => [],
                    'total_cost' => 0,
                    'medicine_count' => 0,
                    'is_paid' => $prescription['is_paid']
                ];
            }
            $pending_patients[$patient_id]['prescriptions'][] = $prescription;
            $pending_patients[$patient_id]['total_cost'] += $prescription['prescription_cost'];
            $pending_patients[$patient_id]['medicine_count']++;
        }

        $this->render('pharmacist/prescriptions', [
            'pending_patients' => array_values($pending_patients),
            'csrf_token' => $this->generateCSRF(),
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    public function dispense()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pharmacist/prescriptions');
            return;
        }

        $this->validateCSRF();

        $patient_id = $_POST['patient_id'] ?? null;
        $dispensed_items = $_POST['dispensed_items'] ?? [];

        if (!$patient_id || empty($dispensed_items)) {
            $_SESSION['error'] = 'Invalid patient data or no medicines selected for dispensing.';
            $this->redirect('pharmacist/prescriptions');
            return;
        }

        try {
            $this->pdo->beginTransaction();

            $visit_id = null;

            foreach ($dispensed_items as $prescription_id => $quantity_to_dispense) {
                $quantity_to_dispense = intval($quantity_to_dispense);
                if ($quantity_to_dispense <= 0) continue;

                // Get prescription details
                $stmt = $this->pdo->prepare("
                    SELECT pr.*, m.name as medicine_name, m.id as medicine_id
                    FROM prescriptions pr
                    JOIN medicines m ON pr.medicine_id = m.id
                    WHERE pr.id = ? AND pr.patient_id = ?
                ");
                $stmt->execute([$prescription_id, $patient_id]);
                $prescription = $stmt->fetch();

                if (!$prescription) continue;

                $visit_id = $prescription['visit_id'];
                $remaining_to_dispense = $prescription['quantity_prescribed'] - $prescription['quantity_dispensed'];
                $actual_dispense = min($quantity_to_dispense, $remaining_to_dispense);

                if ($actual_dispense <= 0) continue;

                // Dispense from batches (FEFO - First Expiry First Out)
                $dispensed_from_batches = 0;
                $stmt = $this->pdo->prepare("
                    SELECT id, batch_number, quantity_remaining, expiry_date
                    FROM medicine_batches
                    WHERE medicine_id = ? AND quantity_remaining > 0
                    ORDER BY expiry_date ASC
                ");
                $stmt->execute([$prescription['medicine_id']]);
                $batches = $stmt->fetchAll();

                foreach ($batches as $batch) {
                    if ($dispensed_from_batches >= $actual_dispense) break;

                    $dispense_from_this_batch = min(
                        $actual_dispense - $dispensed_from_batches,
                        $batch['quantity_remaining']
                    );

                    // Update batch quantity
                    $update_batch = $this->pdo->prepare("
                        UPDATE medicine_batches 
                        SET quantity_remaining = quantity_remaining - ?
                        WHERE id = ?
                    ");
                    $update_batch->execute([$dispense_from_this_batch, $batch['id']]);

                    $dispensed_from_batches += $dispense_from_this_batch;
                }

                // Update prescription
                $new_dispensed = $prescription['quantity_dispensed'] + $actual_dispense;
                $new_status = ($new_dispensed >= $prescription['quantity_prescribed']) ? 'dispensed' : 'partial';

                $update_prescription = $this->pdo->prepare("
                    UPDATE prescriptions 
                    SET quantity_dispensed = ?,
                        status = ?,
                        dispensed_at = CASE WHEN ? = 'dispensed' THEN NOW() ELSE dispensed_at END,
                        dispensed_by = ?
                    WHERE id = ?
                ");
                $update_prescription->execute([
                    $new_dispensed,
                    $new_status,
                    $new_status,
                    $_SESSION['user_id'],
                    $prescription_id
                ]);
            }

            // Check if all prescriptions for this visit are dispensed
            if ($visit_id) {
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) FROM prescriptions 
                    WHERE visit_id = ? AND status IN ('pending', 'partial')
                ");
                $stmt->execute([$visit_id]);
                $pending_count = $stmt->fetchColumn();

                if ($pending_count == 0) {
                    // Update workflow status
                    $this->updateWorkflowStatus($patient_id, 'completed', ['medicine_dispensed' => true]);
                }
            }

            $this->pdo->commit();
            $_SESSION['success'] = 'Medicines dispensed successfully.';

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error in medicine dispensing: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to dispense medicines: ' . $e->getMessage();
        }

        $this->redirect('pharmacist/prescriptions');
    }

    public function inventory()
    {
        // Get all medicines with stock info
        $stmt = $this->pdo->prepare("
            SELECT m.id, m.name, m.generic_name, 
                   m.unit AS category, 
                   m.unit_price,
                   m.strength,
                   m.description,
                   COALESCE(SUM(mb.quantity_remaining), 0) as stock_quantity,
                   MIN(mb.expiry_date) as nearest_expiry,
                   MAX(mb.expiry_date) as latest_expiry,
                   COUNT(DISTINCT mb.id) as batch_count
            FROM medicines m
            LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id AND mb.quantity_remaining > 0
            GROUP BY m.id, m.name, m.generic_name, m.unit, m.unit_price, m.strength, m.description
            ORDER BY m.name
        ");
        $stmt->execute();
        $medicines = $stmt->fetchAll();

        // Get categories
        $categories = $this->pdo->query("
            SELECT DISTINCT unit FROM medicines WHERE unit IS NOT NULL ORDER BY unit
        ")->fetchAll(PDO::FETCH_COLUMN);

        // Build notifications for low stock and expiring
        $notifications = [];
        $today = new \DateTime('today');
        foreach ($medicines as $med) {
            if ($med['stock_quantity'] <= 10) {
                $notifications[] = [
                    'type' => 'low_stock',
                    'medicine_id' => $med['id'],
                    'medicine_name' => $med['name'],
                    'stock' => $med['stock_quantity'],
                    'message' => "Low stock: {$med['name']} ({$med['stock_quantity']} remaining)"
                ];
            }
            if (!empty($med['nearest_expiry'])) {
                $expiry = new \DateTime($med['nearest_expiry']);
                $diff = $today->diff($expiry);
                if ($expiry < $today) {
                    $notifications[] = [
                        'type' => 'expired',
                        'medicine_id' => $med['id'],
                        'medicine_name' => $med['name'],
                        'expiry_date' => $med['nearest_expiry'],
                        'message' => "EXPIRED: {$med['name']} (expired {$diff->days} days ago)"
                    ];
                } elseif ($diff->days <= 60) {
                    $notifications[] = [
                        'type' => 'expiring_soon',
                        'medicine_id' => $med['id'],
                        'medicine_name' => $med['name'],
                        'expiry_date' => $med['nearest_expiry'],
                        'message' => "Expiring soon: {$med['name']} ({$diff->days} days)"
                    ];
                }
            }
        }

        $this->render('pharmacist/inventory', [
            'medicines' => $medicines,
            'categories' => $categories,
            'notifications' => $notifications,
            'csrf_token' => $this->generateCSRF(),
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    public function update_stock()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pharmacist/inventory');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);

        try {
            $medicine_id = intval($_POST['medicine_id']);
            $quantity = intval($_POST['quantity']);
            $action = $_POST['action'] ?? 'add'; // 'add' or 'set'
            $batch_number = trim($_POST['batch_number'] ?? '');
            $expiry_date = trim($_POST['expiry_date'] ?? '');
            $supplier = trim($_POST['supplier'] ?? '');
            $cost_price = floatval($_POST['cost_price'] ?? 0);

            if ($medicine_id <= 0 || $quantity < 0) {
                throw new Exception('Invalid medicine or quantity');
            }

            // Get medicine info
            $stmt = $this->pdo->prepare("SELECT id, name, unit_price FROM medicines WHERE id = ?");
            $stmt->execute([$medicine_id]);
            $medicine = $stmt->fetch();

            if (!$medicine) {
                throw new Exception('Medicine not found');
            }

            // Generate batch number if not provided
            if (empty($batch_number)) {
                $batch_number = 'BATCH-' . date('Ymd') . '-' . $medicine_id . '-' . rand(100, 999);
            }

            if ($action === 'add' && $quantity > 0) {
                // Add new batch
                $stmt = $this->pdo->prepare("
                    INSERT INTO medicine_batches 
                    (medicine_id, batch_number, quantity, quantity_remaining, cost_price, expiry_date, supplier, received_by, received_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $medicine_id,
                    $batch_number,
                    $quantity,
                    $quantity,
                    $cost_price > 0 ? $cost_price : $medicine['unit_price'],
                    !empty($expiry_date) ? $expiry_date : null,
                    !empty($supplier) ? $supplier : null,
                    $_SESSION['user_id']
                ]);
            }

            $_SESSION['success'] = 'Stock updated successfully';

        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update stock: ' . $e->getMessage();
        }

        $this->redirect('pharmacist/inventory');
    }

    public function batches($medicine_id = null)
    {
        if (!$medicine_id) {
            $medicine_id = filter_input(INPUT_GET, 'medicine_id', FILTER_VALIDATE_INT);
        }

        if (!$medicine_id) {
            $this->redirect('pharmacist/inventory');
            return;
        }

        // Get medicine info
        $stmt = $this->pdo->prepare("SELECT * FROM medicines WHERE id = ?");
        $stmt->execute([$medicine_id]);
        $medicine = $stmt->fetch();

        if (!$medicine) {
            $_SESSION['error'] = 'Medicine not found';
            $this->redirect('pharmacist/inventory');
            return;
        }

        // Get all batches
        $stmt = $this->pdo->prepare("
            SELECT mb.*, u.first_name as received_by_name
            FROM medicine_batches mb
            LEFT JOIN users u ON mb.received_by = u.id
            WHERE mb.medicine_id = ?
            ORDER BY mb.expiry_date ASC, mb.received_at DESC
        ");
        $stmt->execute([$medicine_id]);
        $batches = $stmt->fetchAll();

        $this->render('pharmacist/batches', [
            'medicine' => $medicine,
            'batches' => $batches,
            'csrf_token' => $this->generateCSRF(),
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    public function prescription_details()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;
        if (!$patient_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing patient_id']);
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    pr.id as prescription_id,
                    m.name as medicine_name,
                    pr.quantity_prescribed,
                    pr.quantity_dispensed,
                    (pr.quantity_prescribed * m.unit_price) as medicine_cost,
                    pr.dosage,
                    pr.frequency,
                    pr.duration,
                    pr.instructions
                FROM prescriptions pr
                JOIN medicines m ON pr.medicine_id = m.id
                WHERE pr.patient_id = ?
                AND pr.status IN ('pending', 'partial')
                ORDER BY pr.created_at DESC
            ");
            
            $stmt->execute([$patient_id]);
            $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $total_cost = array_sum(array_column($medicines, 'medicine_cost'));

            header('Content-Type: application/json');
            echo json_encode([
                'medicines' => $medicines,
                'total_cost' => $total_cost
            ]);
            exit;

        } catch (Exception $e) {
            error_log("Error in prescription_details: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
            exit;
        }
    }

    private function getSidebarData()
    {
        // Get pending prescriptions count
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT patient_id) 
            FROM prescriptions 
            WHERE status IN ('pending', 'partial')
        ");
        $stmt->execute();
        $pending_patients = $stmt->fetchColumn();

        // Get low stock count
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT m.id)
            FROM medicines m
            LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
            GROUP BY m.id
            HAVING COALESCE(SUM(mb.quantity_remaining), 0) <= 10
        ");
        $stmt->execute();
        $low_stock_count = $stmt->rowCount();

        // Get expiring soon count
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT medicine_id)
            FROM medicine_batches
            WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)
              AND quantity_remaining > 0
        ");
        $stmt->execute();
        $expiring_count = $stmt->fetchColumn();

        return [
            'pending_prescriptions' => $pending_patients ?: 0,
            'low_stock_medicines' => $low_stock_count ?: 0,
            'expiring_medicines' => $expiring_count ?: 0
        ];
    }
}
