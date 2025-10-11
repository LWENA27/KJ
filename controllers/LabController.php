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
        $technician_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("
         SELECT lr.*, lto.id as order_id, lto.status as order_status, lt.test_name as test_name, lt.test_code as test_code, lt.category_id as category, p.first_name, p.last_name, c.created_at as consultation_created_at,
             COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv WHERE pv.patient_id = p.id ORDER BY pv.created_at DESC LIMIT 1) AND pay.payment_type = 'registration' AND pay.payment_status = 'paid'),1,0)) AS consultation_registration_paid,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay2 WHERE pay2.visit_id = (SELECT id FROM patient_visits pv2 WHERE pv2.patient_id = p.id ORDER BY pv2.created_at DESC LIMIT 1) AND pay2.payment_type = 'lab_test' AND pay2.payment_status = 'paid'),1,0)) AS lab_tests_paid,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay3 WHERE pay3.visit_id = (SELECT id FROM patient_visits pv3 WHERE pv3.patient_id = p.id ORDER BY pv3.created_at DESC LIMIT 1) AND pay3.payment_type = 'lab_test' AND pay3.payment_status = 'paid'),1,0)) AS results_review_paid
            FROM lab_results lr
            JOIN lab_test_orders lto ON lr.order_id = lto.id
            JOIN lab_tests lt ON lr.test_id = lt.id
            JOIN consultations c ON lto.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            WHERE lr.technician_id = ?
            ORDER BY lto.status ASC, lr.completed_at DESC
        ");
        $stmt->execute([$technician_id]);
        $tests = $stmt->fetchAll();

        $this->render('lab/tests', [
            'tests' => $tests
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
            $stmt = $this->pdo->prepare("
                UPDATE lab_test_orders lto
                JOIN lab_results lr ON lto.id = lr.order_id
                SET lto.status = 'in_progress'
                WHERE lr.id = ? AND lr.technician_id = ? AND lto.status = 'pending'
            ");
            $stmt->execute([$test_id, $_SESSION['user_id']]);

            if ($stmt->rowCount() > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Test started successfully']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Test not found or already started']);
            }

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to start test: ' . $e->getMessage()]);
        }
        exit;
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
}
?>