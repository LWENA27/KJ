<?php
require_once __DIR__ . '/../includes/BaseController.php';

class LabController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireRole('lab_technician');
    }

    public function dashboard() {
    $technician_id = $_SESSION['user_id'];

    // Fetch patients ready for lab testing (compatible with both technician-assigned lab_results and patient-level lab visits)
    $stmt = $this->pdo->prepare("
        SELECT lr.*, lto.id as order_id, lto.status as order_status, lto.created_at, lt.test_name as test_name, lt.test_code as test_code, lt.category_id as category, p.first_name, p.last_name,
               COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date
        FROM lab_results lr
        JOIN lab_test_orders lto ON lr.order_id = lto.id
        JOIN lab_tests lt ON lr.test_id = lt.id
        JOIN consultations c ON lto.consultation_id = c.id
        JOIN patients p ON c.patient_id = p.id
        LEFT JOIN patient_visits pv ON c.visit_id = pv.id
        WHERE (lr.technician_id = ? OR lr.technician_id IS NULL)
          AND lto.status = 'pending'
        ORDER BY lto.created_at ASC
    ");
    $stmt->execute([$technician_id]);
    $pending_tests = $stmt->fetchAll();

    // Today's completed tests
    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) as completed_today
        FROM lab_results lr
        JOIN lab_test_orders lto ON lr.order_id = lto.id
        WHERE lr.technician_id = ? AND DATE(lr.completed_at) = CURDATE() AND lto.status = 'completed'
    ");
    $stmt->execute([$technician_id]);
    $completed_today = $stmt->fetch()['completed_today'];

    // Work statistics - technician-specific historical stats (do not add global ready_count to avoid inflation)
    $stmt = $this->pdo->prepare("
        SELECT
            COUNT(lr.id) as total_tests,
            COUNT(CASE WHEN lto.status = 'completed' THEN 1 END) as completed_tests,
            COUNT(CASE WHEN lto.status = 'pending' THEN 1 END) as pending_tests
        FROM lab_results lr
        JOIN lab_test_orders lto ON lr.order_id = lto.id
        WHERE lr.technician_id = ?
    ");
    $stmt->execute([$technician_id]);
    $stats = $stmt->fetch();
    $stats['pending_tests'] += count($pending_tests);  // Add current ready patients to pending for dashboard display

    $this->render('lab/dashboard', [
        'pending_tests' => $pending_tests,
        'completed_today' => $completed_today,
        'stats' => $stats
    ]);
}
    public function tests() {
        // Modify query to join with payments table to check payment status
        $stmt = $this->pdo->prepare("
        SELECT 
            lto.id,
            lto.visit_id,
            lto.patient_id,
            lto.test_id,
            lto.status,
            lto.priority,
            lto.created_at,
            p.first_name,
            p.last_name,
            p.registration_number,
            lt.test_name,
            lt.category_id,
            lt.price,
            u.first_name as doctor_first_name,
            u.last_name as doctor_last_name,
            ltc.category_name,
            COALESCE(pay.payment_status, 'pending') as payment_status,
            pay.amount as paid_amount
        FROM lab_test_orders lto
        JOIN patients p ON lto.patient_id = p.id
        JOIN lab_tests lt ON lto.test_id = lt.id
        JOIN users u ON lto.ordered_by = u.id
        JOIN lab_test_categories ltc ON lt.category_id = ltc.id
        LEFT JOIN payments pay ON lto.visit_id = pay.visit_id 
            AND pay.payment_type = 'lab_test'
            AND pay.item_type = 'lab_order'
            AND pay.item_id = lto.id
        WHERE pay.payment_status = 'paid'
        ORDER BY 
            CASE 
                WHEN lto.priority = 'urgent' THEN 1
                WHEN lto.priority = 'high' THEN 2
                ELSE 3
            END,
            lto.created_at DESC
    ");
    
    $stmt->execute();
    $tests = $stmt->fetchAll();

    $this->render('lab/tests', [
        'tests' => $tests,
        'csrf_token' => $this->generateCSRF()
    ]);
    }

    public function view_test($test_id) {
        if (!$test_id) {
            $this->redirect('lab/tests');
        }

     // Fix test details query
     $stmt = $this->pdo->prepare("
         SELECT lr.*, lto.id as order_id, lto.status as order_status, lt.test_name as test_name, lt.test_code as test_code, lt.category_id as category, lt.normal_range, p.first_name, p.last_name,
             (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv WHERE pv.patient_id = p.id ORDER BY pv.created_at DESC LIMIT 1) AND pay.payment_type = 'registration' AND pay.payment_status = 'paid'),1,0)) AS consultation_registration_paid,
             (SELECT IF(EXISTS(SELECT 1 FROM payments pay2 WHERE pay2.visit_id = (SELECT id FROM patient_visits pv2 WHERE pv2.patient_id = p.id ORDER BY pv2.created_at DESC LIMIT 1) AND pay2.payment_type = 'lab_test' AND pay2.payment_status = 'paid'),1,0)) AS lab_tests_paid
         FROM lab_results lr
         JOIN lab_test_orders lto ON lr.order_id = lto.id
         JOIN lab_tests lt ON lr.test_id = lt.id
         JOIN consultations c ON lto.consultation_id = c.id
         JOIN patients p ON c.patient_id = p.id
         LEFT JOIN patient_visits pv ON c.visit_id = pv.id
         WHERE lr.id = ?
     ");
        $stmt->execute([$test_id]);
        $test = $stmt->fetch();

        if (!$test) {
            $this->redirect('lab/tests');
        }

        $this->render('lab/view_test', [
            'test' => $test,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function process_lab_payment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('lab/dashboard');
        }

        $this->validateCSRF();

        $patient_id = intval($_POST['patient_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        $payment_method = $_POST['payment_method'] ?? 'cash';

        if ($patient_id <= 0 || $amount <= 0) {
            $_SESSION['error'] = 'Invalid payment details';
            $this->redirect('lab/tests');
        }

        // Get workflow
        $workflow = $this->getWorkflowStatus($patient_id);
        if (!$workflow) {
            $_SESSION['error'] = 'Patient workflow not found';
            $this->redirect('lab/tests');
        }

        // Process payment
        $success = $this->processStepPayment($workflow['id'], 'lab_tests', $amount, $payment_method, $_SESSION['user_id']);

        if ($success) {
            $_SESSION['success'] = 'Lab test payment processed successfully!';
        } else {
            $_SESSION['error'] = 'Payment processing failed';
        }

        $this->redirect('lab/tests');
    }

    public function results() {
        $technician_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("
            SELECT lr.*, lto.id as order_id, lto.status as order_status, lt.test_name as test_name, lt.test_code as test_code, lt.normal_range, lt.unit, p.first_name, p.last_name
            FROM lab_results lr
            JOIN lab_test_orders lto ON lr.order_id = lto.id
            JOIN lab_tests lt ON lr.test_id = lt.id
            JOIN consultations c ON lto.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            WHERE lr.technician_id = ? AND lto.status = 'pending'
            ORDER BY lr.completed_at ASC
        ");
        $stmt->execute([$technician_id]);
        $pending_results = $stmt->fetchAll();

        $this->render('lab/results', [
            'pending_results' => $pending_results,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function complete_test() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('lab/tests');
        }

        $this->validateCSRF();

        $test_id = $_POST['test_id'];
        $result_value = $_POST['result_value'];
        $result_text = $_POST['result_text'] ?? '';

        if (!$test_id) {
            $_SESSION['error'] = 'Test ID is required';
            $this->redirect('lab/tests');
        }

        try {
            $this->pdo->beginTransaction();

            // Update lab result and order status
            $stmt = $this->pdo->prepare("
                UPDATE lab_results lr
                JOIN lab_test_orders lto ON lr.order_id = lto.id
                SET lr.result_value = ?, lr.result_text = ?, lr.completed_at = NOW(), lto.status = 'completed'
                WHERE lr.id = ?
            ");
            $stmt->execute([$result_value, $result_text, $test_id]);

            // Get patient ID for workflow update
            $stmt = $this->pdo->prepare("
                SELECT c.patient_id
                FROM lab_results lr
                JOIN lab_test_orders lto ON lr.order_id = lto.id
                JOIN consultations c ON lto.consultation_id = c.id
                WHERE lr.id = ?
            ");
            $stmt->execute([$test_id]);
            $patient_id = $stmt->fetch()['patient_id'];

            // Update workflow status to results_review
            $this->updateWorkflowStatus($patient_id, 'results_review');

            $this->pdo->commit();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to complete test: ' . $e->getMessage();
        }

        $this->redirect('lab/tests');
    }

    public function record_result() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('lab/tests');
        }

        // Debug: Log CSRF tokens
        error_log('CSRF Debug - POST token: ' . ($_POST['csrf_token'] ?? 'NOT SET'));
        error_log('CSRF Debug - Session token: ' . ($_SESSION['csrf_token'] ?? 'NOT SET'));

        $this->validateCSRF();

        $test_id = intval($_POST['test_id'] ?? 0);
        $result_value = trim($_POST['result_value'] ?? '');
        $result_text = trim($_POST['result_text'] ?? '');

        if (!$test_id) {
            $_SESSION['error'] = 'Test ID is required';
            $this->redirect('lab/view_test/' . $test_id);
        }

        if (empty($result_value)) {
            $_SESSION['error'] = 'Result value is required';
            $this->redirect('lab/view_test/' . $test_id);
        }

        try {
            $this->pdo->beginTransaction();

            // First check if test exists and belongs to current technician
            $stmt = $this->pdo->prepare("
                SELECT lr.*, lto.status as order_status, t.test_name as test_name, t.test_code as test_code
                FROM lab_results lr
                JOIN lab_test_orders lto ON lr.order_id = lto.id
                JOIN lab_tests t ON lr.test_id = t.id
                WHERE lr.id = ? AND lr.technician_id = ?
            ");
            $stmt->execute([$test_id, $_SESSION['user_id']]);
            $test = $stmt->fetch();

            if (!$test) {
                throw new Exception('Test not found or access denied');
            }

            if ($test['order_status'] === 'completed') {
                throw new Exception('Test results have already been recorded');
            }

            // Update lab result and order status
            $stmt = $this->pdo->prepare("
                UPDATE lab_results lr
                JOIN lab_test_orders lto ON lr.order_id = lto.id
                SET lr.result_value = ?, lr.result_text = ?, lr.completed_at = NOW(), lto.status = 'completed'
                WHERE lr.id = ? AND lr.technician_id = ?
            ");
            $stmt->execute([$result_value, $result_text, $test_id, $_SESSION['user_id']]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Failed to update test result');
            }

            // Get patient ID for workflow update
            $stmt = $this->pdo->prepare("
                SELECT c.patient_id
                FROM lab_results lr
                JOIN lab_test_orders lto ON lr.order_id = lto.id
                JOIN consultations c ON lto.consultation_id = c.id
                WHERE lr.id = ?
            ");
            $stmt->execute([$test_id]);
            $patient_id = $stmt->fetch()['patient_id'];

            // Update workflow status to results_review
            $this->updateWorkflowStatus($patient_id, 'results_review');

            $this->pdo->commit();

            // Regenerate CSRF token for security
            unset($_SESSION['csrf_token']);

            $_SESSION['success'] = 'Test result recorded successfully for ' . $test['test_name'];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Record result error: ' . $e->getMessage());
            $_SESSION['error'] = 'Failed to record result: ' . $e->getMessage();
        }

        $this->redirect('lab/view_test/' . $test_id);
    }

    public function start_test() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $this->validateCSRF();

        $test_id = $_POST['test_id'] ?? 0;

        if (!$test_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Test ID is required']);
            exit;
        }

        try {
            // Check if test has been paid for
            $stmt = $this->pdo->prepare("
                SELECT lto.*, p.payment_status
                FROM lab_test_orders lto
                LEFT JOIN payments p ON lto.visit_id = p.visit_id 
                    AND p.payment_type = 'lab_test'
                    AND p.item_type = 'lab_order'
                    AND p.item_id = lto.id
                WHERE lto.id = ?
            ");
            $stmt->execute([$test_id]);
            $test = $stmt->fetch();

            if (!$test) {
                throw new Exception('Test not found');
            }

            if ($test['payment_status'] !== 'paid') {
                throw new Exception('Test payment pending');
            }

            $stmt = $this->pdo->prepare("
                UPDATE lab_test_orders 
                SET status = 'in_progress',
                    assigned_to = ?,
                    sample_collected_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([$_SESSION['user_id'], $test_id]);
            
            $this->jsonResponse(['success' => true, 'message' => 'Test started successfully']);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function collect_sample() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $this->validateCSRF();

        $test_id = $_POST['test_id'] ?? 0;

        if (!$test_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Test ID is required']);
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("
                UPDATE lab_results
                SET sample_date = NOW()
                WHERE id = ? AND technician_id = ?
            ");
            $stmt->execute([$test_id, $_SESSION['user_id']]);

            if ($stmt->rowCount() > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Sample collected successfully']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Test not found']);
            }

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to collect sample: ' . $e->getMessage()]);
        }
        exit;
    }

    public function samples() {
        $technician_id = $_SESSION['user_id'];

        // Get samples to be collected — derive payment flags from payments / patient_visits
        $stmt = $this->pdo->prepare("
            SELECT lr.*, lto.id as order_id, lto.status as order_status, t.test_name as test_name, t.test_code as test_code, t.category_id as category, p.first_name, p.last_name, c.created_at as consultation_created_at,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv WHERE pv.id = c.visit_id LIMIT 1) AND pay.payment_type = 'registration' AND pay.payment_status = 'paid'),1,0)) AS consultation_registration_paid,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay2 WHERE pay2.visit_id = (SELECT id FROM patient_visits pv2 WHERE pv2.id = c.visit_id LIMIT 1) AND pay2.payment_type = 'lab_test' AND pay2.payment_status = 'paid'),1,0)) AS lab_tests_paid
            FROM lab_results lr
            JOIN lab_test_orders lto ON lr.order_id = lto.id
            JOIN lab_tests t ON lr.test_id = t.id
            JOIN consultations c ON lto.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            WHERE lr.technician_id = ? AND lto.status IN ('pending', 'sample_collected')
            ORDER BY lto.status ASC, lr.completed_at ASC
        ");
        $stmt->execute([$technician_id]);
        $samples = $stmt->fetchAll();

        $this->render('lab/samples', [
            'samples' => $samples,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function equipment() {
        $this->render('lab/equipment', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function inventory() {
        $this->render('lab/inventory', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function quality() {
        $this->render('lab/quality', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function reports() {
        $technician_id = $_SESSION['user_id'];

        // Get report statistics
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_tests,
                COUNT(CASE WHEN lto.status = 'completed' THEN 1 END) as completed_tests,
                COUNT(CASE WHEN lto.status = 'pending' THEN 1 END) as pending_tests,
                COUNT(CASE WHEN DATE(lto.created_at) = CURDATE() THEN 1 END) as today_tests,
                COUNT(CASE WHEN DATE(lto.created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as week_tests,
                COUNT(CASE WHEN DATE(lto.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as month_tests
            FROM lab_results lr
            JOIN lab_test_orders lto ON lr.order_id = lto.id
            WHERE lr.technician_id = ?
        ");
        $stmt->execute([$technician_id]);
        $stats = $stmt->fetch();

        $this->render('lab/reports', [
            'stats' => $stats,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    // Helper method to send JSON response and exit
    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function check_payment_status() {
        $test_id = $_GET['test_id'] ?? null;
        
        if (!$test_id) {
            $this->jsonResponse(['is_paid' => false]);
            return;
        }

        $stmt = $this->pdo->prepare("
            SELECT p.payment_status 
            FROM lab_test_orders lto
            LEFT JOIN payments p ON lto.visit_id = p.visit_id 
                AND p.payment_type = 'lab_test_fee'
                AND p.item_id = lto.id
            WHERE lto.id = ?
            ORDER BY p.payment_date DESC 
            LIMIT 1
        ");
        
        $stmt->execute([$test_id]);
        $payment = $stmt->fetch();
        
        $this->jsonResponse([
            'is_paid' => ($payment && $payment['payment_status'] === 'paid')
        ]);
    }

    public function take_sample() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method';
            $this->redirect('lab/tests');
            return;
        }

        $this->validateCSRF();

        try {
            $test_order_id = $_POST['test_order_id'];
            $sample_notes = $_POST['sample_notes'] ?? '';
            $collection_time = $_POST['collection_time'];
            $technician_id = $_SESSION['user_id'];

            // Update the lab test order status to 'sample_taken'
            $stmt = $this->pdo->prepare("
                UPDATE lab_test_orders 
                SET status = 'sample_taken', 
                    sample_collected_at = ?, 
                    sample_notes = ?,
                    updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$collection_time, $sample_notes, $test_order_id]);

            // Create a lab_results entry with sample_taken status
            $stmt = $this->pdo->prepare("
                SELECT patient_id, test_id, visit_id FROM lab_test_orders WHERE id = ?
            ");
            $stmt->execute([$test_order_id]);
            $order = $stmt->fetch();

            if ($order) {
                // Insert or update lab_results record
                $stmt = $this->pdo->prepare("
                    INSERT INTO lab_results 
                    (order_id, patient_id, test_id, visit_id, technician_id, status, sample_collected_at, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, 'sample_taken', ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                    status = 'sample_taken',
                    sample_collected_at = VALUES(sample_collected_at),
                    updated_at = NOW()
                ");
                $stmt->execute([
                    $test_order_id,
                    $order['patient_id'],
                    $order['test_id'],
                    $order['visit_id'],
                    $technician_id,
                    $collection_time
                ]);
            }

            $_SESSION['success'] = 'Sample collection recorded successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to record sample collection: ' . $e->getMessage();
        }

        $this->redirect('lab/view_test/' . $test_order_id);
    }

    public function add_result() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method';
            $this->redirect('lab/tests');
            return;
        }

        $this->validateCSRF();

        try {
            $test_order_id = $_POST['test_order_id'];
            $result_value = $_POST['result_value'];
            $unit = $_POST['unit'] ?? '';
            $result_status = $_POST['result_status'] ?? 'normal';
            $result_notes = $_POST['result_notes'] ?? '';
            $completion_time = $_POST['completion_time'];
            $technician_id = $_SESSION['user_id'];

            // Get order details
            $stmt = $this->pdo->prepare("
                SELECT patient_id, test_id, visit_id FROM lab_test_orders WHERE id = ?
            ");
            $stmt->execute([$test_order_id]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new Exception('Test order not found');
            }

            $this->pdo->beginTransaction();

            // Update lab test order status to completed
            $stmt = $this->pdo->prepare("
                UPDATE lab_test_orders 
                SET status = 'completed', 
                    completed_at = ?,
                    updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$completion_time, $test_order_id]);

            // Insert or update lab_results record
            $stmt = $this->pdo->prepare("
                INSERT INTO lab_results 
                (order_id, patient_id, test_id, visit_id, technician_id, result_value, unit, 
                 result_text, result_status, status, completed_at, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                result_value = VALUES(result_value),
                unit = VALUES(unit),
                result_text = VALUES(result_text),
                result_status = VALUES(result_status),
                status = 'completed',
                completed_at = VALUES(completed_at),
                updated_at = NOW()
            ");
            $stmt->execute([
                $test_order_id,
                $order['patient_id'],
                $order['test_id'],
                $order['visit_id'],
                $technician_id,
                $result_value,
                $unit,
                $result_notes,
                $result_status,
                $completion_time
            ]);

            // Update patient workflow status if all tests for this visit are completed
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as pending_tests
                FROM lab_test_orders 
                WHERE visit_id = ? AND status != 'completed'
            ");
            $stmt->execute([$order['visit_id']]);
            $pending = $stmt->fetch();

            if ($pending['pending_tests'] == 0) {
                // All tests completed, update patient workflow
                $this->updateWorkflowStatus($order['patient_id'], 'results_ready');
            }

            $this->pdo->commit();
            $_SESSION['success'] = 'Test result saved successfully';
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to save test result: ' . $e->getMessage();
        }

        $this->redirect('lab/view_test/' . $test_order_id);
    }
}
?>