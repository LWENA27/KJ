<?php
require_once __DIR__ . '/../includes/BaseController.php';

class ReceptionistController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireRole('receptionist');
    }

    public function dashboard() {
        // Get today's appointments
        $stmt = $this->pdo->prepare("
            SELECT c.*, p.first_name, p.last_name, u.first_name as doctor_first, u.last_name as doctor_last
            FROM consultations c
            JOIN patients p ON c.patient_id = p.id
            JOIN users u ON c.doctor_id = u.id
            WHERE DATE(c.appointment_date) = CURDATE()
            ORDER BY c.appointment_date
        ");
        $stmt->execute();
        $appointments = $stmt->fetchAll();

        // Get recent patients
        $recent_patients = $this->pdo->query("
            SELECT id, first_name, last_name, phone, created_at
            FROM patients
            ORDER BY created_at DESC
            LIMIT 5
        ")->fetchAll();

        $this->render('receptionist/dashboard', [
            'appointments' => $appointments,
            'recent_patients' => $recent_patients
        ]);
    }

    public function patients() {
        $patients = $this->pdo->query("
            SELECT p.*, ws.current_step as workflow_status, ws.current_step, ws.consultation_registration_paid, ws.lab_tests_paid,
                   ws.results_review_paid, ws.medicine_prescribed, ws.medicine_dispensed, ws.final_payment_collected,
                   ws.lab_tests_required
            FROM patients p
            LEFT JOIN workflow_status ws ON p.id = ws.patient_id
            ORDER BY
                CASE
                    WHEN ws.current_step = 'completed' THEN 1
                    WHEN ws.medicine_dispensed = false AND ws.medicine_prescribed = true THEN 2
                    WHEN ws.final_payment_collected = false AND (ws.medicine_prescribed = true OR ws.lab_tests_required = true) THEN 3
                    ELSE 4
                END,
                p.created_at DESC
        ")->fetchAll();

        $this->render('receptionist/patients', [
            'patients' => $patients,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function register_patient() {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();

            $patient_data = [
                'first_name' => $this->sanitize($_POST['first_name'] ?? ''),
                'last_name' => $this->sanitize($_POST['last_name'] ?? ''),
                'date_of_birth' => $_POST['date_of_birth'] ?? '',
                'gender' => $_POST['gender'] ?? '',
                'phone' => $this->sanitize($_POST['phone'] ?? ''),
                'email' => $this->sanitize($_POST['email'] ?? ''),
                'address' => $this->sanitize($_POST['address'] ?? ''),
                'emergency_contact_name' => $this->sanitize($_POST['emergency_contact_name'] ?? ''),
                'emergency_contact_phone' => $this->sanitize($_POST['emergency_contact_phone'] ?? ''),
                'occupation' => $this->sanitize($_POST['occupation'] ?? ''),
                'temperature' => floatval($_POST['temperature'] ?? 0),
                'blood_pressure' => $this->sanitize($_POST['blood_pressure'] ?? ''),
                'pulse_rate' => intval($_POST['pulse_rate'] ?? 0),
                'body_weight' => floatval($_POST['body_weight'] ?? 0),
                'height' => floatval($_POST['height'] ?? 0),
                'consultation_fee' => floatval($_POST['consultation_fee'] ?? 0),
                'payment_method' => $_POST['payment_method'] ?? 'cash'
            ];

            if (empty($patient_data['first_name']) || empty($patient_data['last_name'])) {
                $error = 'First name and last name are required';
            } else {
                try {
                    $this->pdo->beginTransaction();

                    // Insert patient
                    $stmt = $this->pdo->prepare("
                        INSERT INTO patients (first_name, last_name, date_of_birth, gender, phone, email, address,
                                           occupation, emergency_contact_name, emergency_contact_phone, temperature,
                                           blood_pressure, pulse_rate, body_weight, height)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $patient_data['first_name'], $patient_data['last_name'], $patient_data['date_of_birth'],
                        $patient_data['gender'], $patient_data['phone'], $patient_data['email'],
                        $patient_data['address'], $patient_data['occupation'], $patient_data['emergency_contact_name'],
                        $patient_data['emergency_contact_phone'], $patient_data['temperature'],
                        $patient_data['blood_pressure'], $patient_data['pulse_rate'], $patient_data['body_weight'],
                        $patient_data['height']
                    ]);

                    $patient_id = $this->pdo->lastInsertId();

                    // Initialize workflow
                    $workflow_id = $this->initializeWorkflow($patient_id);

                    // Process consultation/registration payment
                    if ($patient_data['consultation_fee'] > 0) {
                        $this->processStepPayment($workflow_id, 'consultation_registration', $patient_data['consultation_fee'],
                                                $patient_data['payment_method'], $_SESSION['user_id']);
                    }

                    $this->pdo->commit();
                    // Set flash message and redirect to patients list for better UX
                    $_SESSION['success'] = 'Patient registered successfully!';
                    // Store the last registered patient id for quick access
                    $_SESSION['last_registered_patient_id'] = $patient_id;
                    $this->redirect('receptionist/patients');

                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    $error = 'Registration failed: ' . $e->getMessage();
                    $_SESSION['error'] = $error;
                }
            }
        }

        $this->render('receptionist/register_patient', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function appointments() {
        $stmt = $this->pdo->prepare("
            SELECT c.*, p.first_name, p.last_name, u.first_name as doctor_first, u.last_name as doctor_last
            FROM consultations c
            JOIN patients p ON c.patient_id = p.id
            JOIN users u ON c.doctor_id = u.id
            ORDER BY c.appointment_date DESC
        ");
        $stmt->execute();
        $appointments = $stmt->fetchAll();

        $this->render('receptionist/appointments', [
            'appointments' => $appointments
        ]);
    }

    public function payments() {
        $stmt = $this->pdo->prepare("
            SELECT p.*, pt.first_name, pt.last_name, c.appointment_date
            FROM payments p
            JOIN patients pt ON p.patient_id = pt.id
            LEFT JOIN consultations c ON p.consultation_id = c.id
            ORDER BY p.payment_date DESC
        ");
        $stmt->execute();
        $payments = $stmt->fetchAll();

        $this->render('receptionist/payments', [
            'payments' => $payments
        ]);
    }

    public function process_final_payment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('receptionist/patients');
        }

        $this->validateCSRF();

        $patient_id = $_POST['patient_id'];
        $amount = floatval($_POST['amount']);
        $payment_method = $_POST['payment_method'];

        if (!$patient_id || $amount <= 0) {
            $_SESSION['error'] = 'Invalid payment details';
            $this->redirect('receptionist/patients');
        }

        try {
            $this->pdo->beginTransaction();

            // Get workflow status
            $stmt = $this->pdo->prepare("SELECT * FROM workflow_status WHERE patient_id = ?");
            $stmt->execute([$patient_id]);
            $workflow = $stmt->fetch();

            if (!$workflow) {
                throw new Exception('Patient workflow not found');
            }

            // Insert payment record
            $stmt = $this->pdo->prepare("
                INSERT INTO payments (patient_id, amount, payment_method, status, created_by)
                VALUES (?, ?, ?, 'completed', ?)
            ");
            $stmt->execute([$patient_id, $amount, $payment_method, $_SESSION['user_id']]);

            // Update workflow status to completed
            $this->updateWorkflowStatus($patient_id, 'completed', ['final_payment_collected' => true]);

            $this->pdo->commit();
            $_SESSION['success'] = 'Final payment processed successfully';

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to process payment: ' . $e->getMessage();
        }

        $this->redirect('receptionist/patients');
    }

    public function medicine() {
        // Get patients waiting for medicine dispensing (prescribed and paid)
        $stmt = $this->pdo->prepare("
            SELECT p.id AS patient_id, p.first_name, p.last_name,
                   mp.id AS prescription_id, mp.total_amount, mp.payment_status, mp.created_at as prescribed_at,
                   COUNT(DISTINCT ma.id) as medicine_count
            FROM patients p
            JOIN medicine_prescriptions mp ON p.id = mp.patient_id
            LEFT JOIN consultations c ON p.id = c.patient_id
            LEFT JOIN medicine_allocations ma ON c.id = ma.consultation_id
            WHERE mp.payment_status = 'paid' AND mp.is_fully_dispensed = 0
            GROUP BY p.id, mp.id, mp.total_amount, mp.payment_status, mp.created_at
            ORDER BY mp.created_at DESC
        ");
        $stmt->execute();
        $pending_patients = $stmt->fetchAll();

        // Get all medicines for inventory management (include expiry)
        $stmt = $this->pdo->prepare("
            SELECT m.id, m.name, m.generic_name, m.supplier AS category, m.unit_price, m.stock_quantity,
                   m.expiry_date,
                   COALESCE(SUM(ma.quantity), 0) as total_prescribed
            FROM medicines m
            LEFT JOIN medicine_allocations ma ON m.id = ma.medicine_id
            GROUP BY m.id, m.name, m.generic_name, m.supplier, m.unit_price, m.stock_quantity, m.expiry_date
            ORDER BY m.name
        ");
        $stmt->execute();
        $medicines = $stmt->fetchAll();

        // Build expiry notifications (expired or within 60 days)
        $notifications = [];
        $today = new DateTime('today');
        foreach ($medicines as $med) {
            if (!empty($med['expiry_date'])) {
                try {
                    $exp = new DateTime($med['expiry_date']);
                    $diff = (int)$today->diff($exp)->format('%r%a'); // negative if expired
                    if ($diff < 0) {
                        $notifications[] = [
                            'type' => 'error',
                            'icon' => 'fa-skull-crossbones',
                            'title' => 'Medicine expired',
                            'message' => $med['name'] . ' expired ' . abs($diff) . ' day(s) ago',
                        ];
                    } elseif ($diff <= 60) {
                        // Near expiry tiers
                        $tier = $diff <= 7 ? 'warning' : ($diff <= 30 ? 'warning' : 'info');
                        $notifications[] = [
                            'type' => $tier,
                            'icon' => 'fa-exclamation-triangle',
                            'title' => 'Medicine near expiry',
                            'message' => $med['name'] . ' expires in ' . $diff . ' day(s)',
                        ];
                    }
                } catch (Exception $e) {
                    // ignore bad date
                }
            }
        }

        // Get medicine categories
    $categories = $this->pdo->query("SELECT DISTINCT supplier FROM medicines ORDER BY supplier")->fetchAll(PDO::FETCH_COLUMN);

        // Get recent medicine transactions (simplified)
        $stmt = $this->pdo->prepare("
            SELECT p.first_name, p.last_name,
                   CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                   mp.total_amount as total_cost, mp.dispensed_at,
                   u.first_name as dispensed_by
            FROM medicine_prescriptions mp
            JOIN patients p ON mp.patient_id = p.id
            LEFT JOIN users u ON mp.dispensed_by = u.id
            WHERE mp.is_fully_dispensed = 1 AND mp.dispensed_at IS NOT NULL
            ORDER BY mp.dispensed_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $recent_transactions = $stmt->fetchAll();

        $this->render('receptionist/medicine', [
            'pending_patients' => $pending_patients,
            'medicines' => $medicines,
            'categories' => $categories,
            'recent_transactions' => $recent_transactions,
            'csrf_token' => $this->generateCSRF(),
            'notifications' => $notifications
        ]);
    }

    public function process_medicine_dispensing() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('receptionist/dispense_medicines');
        }

        $this->validateCSRF();

        $patient_id = $_POST['patient_id'];
        $consultation_id = $_POST['consultation_id'];
        $dispensed_medicines = $_POST['dispensed_medicines'] ?? [];

        // Debug logging
        error_log("Processing medicine dispensing for patient: $patient_id, consultation: $consultation_id");
        error_log("Dispensed medicines: " . print_r($dispensed_medicines, true));

        if (!$patient_id) {
            $_SESSION['error'] = 'Invalid patient data';
            $this->redirect('receptionist/dispense_medicines');
        }

        try {
            $this->pdo->beginTransaction();

            // Check if this is a "mark as completed" action (no consultation_id or empty dispensed_medicines)
            if (empty($consultation_id) || $consultation_id === '0' || empty($dispensed_medicines)) {
                error_log("Marking patient as completed without dispensing");
                // Just mark as completed without dispensing medicines
                $this->updateWorkflowStatus($patient_id, 'completed', [
                    'medicine_dispensed' => true,
                    'medicine_dispensed_by' => $_SESSION['user_id'],
                    'medicine_dispensed_at' => date('Y-m-d H:i:s')
                ]);

                $this->pdo->commit();
                $_SESSION['success'] = 'Patient marked as completed without medicine dispensing';
                $this->redirect('receptionist/dispense_medicines');
                return;
            }

            // Get medicine allocations for this consultation
            $stmt = $this->pdo->prepare("
                SELECT ma.*, m.name, m.generic_name, m.stock_quantity
                FROM medicine_allocations ma
                JOIN medicines m ON ma.medicine_id = m.id
                WHERE ma.consultation_id = ?
            ");
            $stmt->execute([$consultation_id]);
            $allocations = $stmt->fetchAll();

            error_log("Found " . count($allocations) . " medicine allocations");

            // Process each dispensed medicine
            foreach ($allocations as $allocation) {
                $dispensed_quantity = intval($dispensed_medicines[$allocation['id']] ?? 0);

                error_log("Processing allocation ID {$allocation['id']}: prescribed {$allocation['quantity']}, dispensing $dispensed_quantity");

                if ($dispensed_quantity > 0) {
                    if ($dispensed_quantity > $allocation['quantity']) {
                        throw new Exception("Cannot dispense more than prescribed quantity for {$allocation['name']}");
                    }

                    if ($dispensed_quantity > $allocation['stock_quantity']) {
                        throw new Exception("Insufficient stock for {$allocation['name']}");
                    }

                    // Update medicine stock
                    $stmt = $this->pdo->prepare("
                        UPDATE medicines SET stock_quantity = stock_quantity - ? WHERE id = ?
                    ");
                    $stmt->execute([$dispensed_quantity, $allocation['medicine_id']]);
                    error_log("Updated stock for medicine ID {$allocation['medicine_id']}");
                }
            }

            // Update workflow status
            $this->updateWorkflowStatus($patient_id, 'completed', [
                'medicine_dispensed' => true,
                'medicine_dispensed_by' => $_SESSION['user_id'],
                'medicine_dispensed_at' => date('Y-m-d H:i:s')
            ]);

            $this->pdo->commit();
            $_SESSION['success'] = 'Medicines dispensed successfully';
            error_log("Medicine dispensing completed successfully for patient $patient_id");

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error in medicine dispensing: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to dispense medicines: ' . $e->getMessage();
        }

        $this->redirect('receptionist/dispense_medicines');
    }

    public function force_complete_medicine($patient_id) {
        if (!$patient_id) {
            $this->redirect('receptionist/dispense_medicines');
        }

        try {
            $this->updateWorkflowStatus($patient_id, 'completed', [
                'medicine_dispensed' => true,
                'medicine_dispensed_by' => $_SESSION['user_id'],
                'medicine_dispensed_at' => date('Y-m-d H:i:s')
            ]);

            $_SESSION['success'] = 'Patient medicine dispensing completed (forced)';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to complete medicine dispensing: ' . $e->getMessage();
        }

        $this->redirect('receptionist/medicine');
    }

    public function add_medicine() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('receptionist/medicine');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);

        try {
            $name = trim($_POST['name']);
            $generic_name = trim($_POST['generic_name'] ?? '');
            $category = trim($_POST['category']); // UI category; we map to supplier field
            $supplier = trim($_POST['supplier'] ?? $category);
            $expiry_date = trim($_POST['expiry_date'] ?? '');
            $unit_price = floatval($_POST['unit_price']);
            $stock_quantity = intval($_POST['stock_quantity']);
            $description = trim($_POST['description'] ?? '');

            if (empty($name) || empty($category) || $unit_price <= 0 || $stock_quantity < 0) {
                throw new Exception('Please fill all required fields with valid values');
            }

            // Validate expiry date format (YYYY-MM-DD) or allow empty
            if ($expiry_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry_date)) {
                throw new Exception('Invalid expiry date format');
            }

            // Schema uses supplier and expiry_date; category is displayed from supplier
            $stmt = $this->pdo->prepare("
                INSERT INTO medicines (name, generic_name, description, stock_quantity, unit_price, expiry_date, supplier, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $generic_name, $description, $stock_quantity, $unit_price, ($expiry_date ?: null), ($supplier ?: null)]);

            $_SESSION['success'] = 'Medicine added successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to add medicine: ' . $e->getMessage();
        }

        $this->redirect('receptionist/medicine');
    }

    public function update_medicine_stock() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('receptionist/medicine');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);

        try {
            $medicine_id = intval($_POST['medicine_id']);
            $new_quantity = intval($_POST['new_quantity']);
            $action = $_POST['action']; // 'add' or 'set'

            if ($medicine_id <= 0 || $new_quantity < 0) {
                throw new Exception('Invalid medicine ID or quantity');
            }

            if ($action === 'add') {
                $stmt = $this->pdo->prepare("
                    UPDATE medicines SET stock_quantity = stock_quantity + ? WHERE id = ?
                ");
            } else {
                $stmt = $this->pdo->prepare("
                    UPDATE medicines SET stock_quantity = ? WHERE id = ?
                ");
            }
            $stmt->execute([$new_quantity, $medicine_id]);

            $_SESSION['success'] = 'Medicine stock updated successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update stock: ' . $e->getMessage();
        }

        $this->redirect('receptionist/medicine');
    }

    public function dispense_patient_medicine() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('receptionist/medicine');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);

        try {
            $patient_id = intval($_POST['patient_id']);
            
            if ($patient_id <= 0) {
                throw new Exception('Invalid patient ID');
            }

            $this->pdo->beginTransaction();

            // Get the medicine prescription for this patient
            $stmt = $this->pdo->prepare("
                SELECT mp.*
                FROM medicine_prescriptions mp
                WHERE mp.patient_id = ? AND mp.payment_status = 'paid' AND mp.is_fully_dispensed = 0
                LIMIT 1
            ");
            $stmt->execute([$patient_id]);
            $prescription = $stmt->fetch();

            if (!$prescription) {
                throw new Exception('No paid prescription found for this patient');
            }

            // Get prescribed medicines through consultations
            $stmt = $this->pdo->prepare("
                SELECT ma.*, m.stock_quantity, m.name as medicine_name, c.id as consultation_id
                FROM consultations c
                JOIN medicine_allocations ma ON c.id = ma.consultation_id
                JOIN medicines m ON ma.medicine_id = m.id
                WHERE c.patient_id = ?
            ");
            $stmt->execute([$patient_id]);
            $allocations = $stmt->fetchAll();

            if (empty($allocations)) {
                throw new Exception('No medicine allocations found for this patient');
            }

            // Check stock and dispense
            foreach ($allocations as $allocation) {
                if ($allocation['quantity'] > $allocation['stock_quantity']) {
                    throw new Exception("Insufficient stock for " . $allocation['medicine_name']);
                }

                // Update stock
                $stmt = $this->pdo->prepare("
                    UPDATE medicines SET stock_quantity = stock_quantity - ? WHERE id = ?
                ");
                $stmt->execute([$allocation['quantity'], $allocation['medicine_id']]);
            }

            // Update prescription as fully dispensed
            $stmt = $this->pdo->prepare("
                UPDATE medicine_prescriptions 
                SET is_fully_dispensed = 1, 
                    dispensed_by = ?, 
                    dispensed_at = NOW(),
                    dispensed_amount = total_amount
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $prescription['id']]);

            // Update workflow status
            $stmt = $this->pdo->prepare("
                UPDATE workflow_status 
                SET medicine_dispensed = 1, 
                    medicine_dispensed_by = ?, 
                    medicine_dispensed_at = NOW(),
                    current_step = 'completed'
                WHERE patient_id = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $patient_id]);

            $this->pdo->commit();
            $_SESSION['success'] = 'Medicine dispensed successfully';

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to dispense medicine: ' . $e->getMessage();
        }

        $this->redirect('receptionist/medicine');
    }
}
?>
