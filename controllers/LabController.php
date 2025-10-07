<?php
require_once __DIR__ . '/../includes/BaseController.php';

class LabController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireRole('lab_technician');
    }

   public function dashboard() {
    $technician_id = $_SESSION['user_id'];

    // Fetch all patients ready for lab testing: visit_type = 'lab_test' and lab_tests_paid = 1 (paid and waiting)
    // Universal: no specific technician assignment check
    $stmt = $this->pdo->prepare("
        SELECT p.id, p.first_name, p.last_name, p.registration_number, p.current_step
        FROM patients p
        WHERE p.visit_type = 'lab_test' 
          
        ORDER BY p.created_at ASC
    ");
    $stmt->execute();
    $ready_patients = $stmt->fetchAll();

    // Map ready_patients to pending_tests format for backward compatibility (add dummy fields)
    $pending_tests = [];
    foreach ($ready_patients as $patient) {
        $pending_tests[] = [
            'id' => $patient['id'],  // Use patient_id as test_id placeholder
            'registration_number' => $patient['registration_number'],
            'first_name' => $patient['first_name'],
            'last_name' => $patient['last_name'],
            'patient_id' => $patient['id'],
            'test_name' => 'Lab Test Visit',  // Placeholder; can be customized per patient if needed
            'priority' => 'routine',  // Default; can add logic for urgent based on other fields
            'notes' => '',  // Optional notes from patient
            'created_at' => date('Y-m-d H:i:s'),  // Current time or from patient created_at
            // 'lab_tests_paid' => $patient['lab_tests_paid']
        ];
    }

    // Today's completed tests - this query is fine, no changes needed
    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) as completed_today
        FROM lab_results
        WHERE technician_id = ? AND DATE(result_date) = CURDATE() AND status = 'completed'
    ");
    $stmt->execute([$technician_id]);
    $completed_today = $stmt->fetch()['completed_today'];

    // Work statistics - technician-specific historical stats (do not add global ready_count to avoid inflation)
    $stmt = $this->pdo->prepare("
        SELECT
            COUNT(lr.id) as total_tests,
            COUNT(CASE WHEN lr.status = 'completed' THEN 1 END) as completed_tests,
            COUNT(CASE WHEN lr.status = 'pending' THEN 1 END) as pending_tests
        FROM lab_results lr
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

        // Fix the tests query
        $stmt = $this->pdo->prepare("
            SELECT lr.*, lt.test_name, lt.category, 
                   p.first_name, p.last_name, c.appointment_date,
                   p.consultation_registration_paid, p.lab_tests_paid
            FROM lab_results lr
            JOIN lab_tests lt ON lr.test_id = lt.id
            JOIN consultations c ON lr.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            WHERE lr.technician_id = ?
            ORDER BY lr.status ASC, lr.created_at DESC
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
            SELECT lr.*, lt.test_name, lt.category, lt.normal_range, 
                   p.first_name, p.last_name,
                   p.consultation_registration_paid, p.lab_tests_paid
            FROM lab_results lr
            JOIN lab_tests lt ON lr.test_id = lt.id
            JOIN consultations c ON lr.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
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
            SELECT lr.*, t.name as test_name, t.normal_range, t.unit, p.first_name, p.last_name
            FROM lab_results lr
            JOIN tests t ON lr.test_id = t.id
            JOIN consultations c ON lr.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            WHERE lr.technician_id = ? AND lr.status = 'pending'
            ORDER BY lr.created_at ASC
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

            // Update lab result
            $stmt = $this->pdo->prepare("
                UPDATE lab_results
                SET result_value = ?, result_text = ?, status = 'completed', result_date = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$result_value, $result_text, $test_id]);

            // Get patient ID for workflow update
            $stmt = $this->pdo->prepare("
                SELECT c.patient_id
                FROM lab_results lr
                JOIN consultations c ON lr.consultation_id = c.id
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
                SELECT lr.*, t.name as test_name
                FROM lab_results lr
                JOIN tests t ON lr.test_id = t.id
                WHERE lr.id = ? AND lr.technician_id = ?
            ");
            $stmt->execute([$test_id, $_SESSION['user_id']]);
            $test = $stmt->fetch();

            if (!$test) {
                throw new Exception('Test not found or access denied');
            }

            if ($test['status'] === 'completed') {
                throw new Exception('Test results have already been recorded');
            }

            // Update lab result
            $stmt = $this->pdo->prepare("
                UPDATE lab_results
                SET result_value = ?, result_text = ?, status = 'completed', result_date = NOW()
                WHERE id = ? AND technician_id = ?
            ");
            $stmt->execute([$result_value, $result_text, $test_id, $_SESSION['user_id']]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Failed to update test result');
            }

            // Get patient ID for workflow update
            $stmt = $this->pdo->prepare("
                SELECT c.patient_id
                FROM lab_results lr
                JOIN consultations c ON lr.consultation_id = c.id
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
                UPDATE lab_results
                SET status = 'processing'
                WHERE id = ? AND technician_id = ? AND status = 'pending'
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

        // Get samples to be collected
        $stmt = $this->pdo->prepare("
            SELECT lr.*, t.name as test_name, t.category, p.first_name, p.last_name, c.appointment_date,
                   ws.consultation_registration_paid, ws.lab_tests_paid
            FROM lab_results lr
            JOIN tests t ON lr.test_id = t.id
            JOIN consultations c ON lr.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            LEFT JOIN workflow_status ws ON p.id = ws.patient_id
            WHERE lr.technician_id = ? AND lr.status IN ('pending', 'sample_collected')
            ORDER BY lr.status ASC, lr.created_at ASC
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
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_tests,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_tests,
                COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_tests,
                COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as week_tests,
                COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as month_tests
            FROM lab_results 
            WHERE technician_id = ?
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