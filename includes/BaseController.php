<?php
// Base Controller Class
class BaseController {
    protected $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Render view with layout
    protected function render($view, $data = []) {
        extract($data);
        // Compute base path for use in views and layout
        $BASE_PATH = defined('BASE_PATH') ? BASE_PATH : (rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/') ?: '');

        // Start output buffering
        ob_start();
        include __DIR__ . "/../views/{$view}.php";
    $content = ob_get_clean();

    // Normalize absolute links in the rendered content to current BASE_PATH
    $base = defined('BASE_PATH') ? BASE_PATH : '/' . basename(dirname(__DIR__));
    $content = str_replace(['/KJ/', '/ZAHANATI/'], rtrim($base, '/') . '/', $content);

        // Include layout
        $BASE_PATH = defined('BASE_PATH') ? BASE_PATH : (rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/') ?: '');
        include __DIR__ . '/../views/layouts/main.php';
    }

    // Redirect to another page
    protected function redirect($url) {
    $base = defined('BASE_PATH') ? BASE_PATH : '/' . basename(dirname(__DIR__));
    header("Location: {$base}/{$url}");
        exit;
    }

    // Check if user is logged in
    protected function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
    }

    // Check user role
    protected function requireRole($role) {
        $this->requireLogin();
        if ($_SESSION['user_role'] !== $role) {
            $this->redirect('auth/login');
        }
    }

    // Sanitize input
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    // Validate CSRF token
    protected function validateCSRF($token = null) {
        // Allow passing token explicitly or default to POST
        if ($token === null && !isset($_POST['csrf_token'])) {
            error_log('CSRF validation failed: No CSRF token in POST data');
            die('CSRF token validation failed: Missing token');
        }

        if (!isset($_SESSION['csrf_token'])) {
            error_log('CSRF validation failed: No CSRF token in session');
            die('CSRF token validation failed: Session token missing');
        }

        $provided = $token !== null ? $token : $_POST['csrf_token'];
        if ($provided !== $_SESSION['csrf_token']) {
            error_log('CSRF validation failed: Token mismatch');
            error_log('Provided token: ' . $provided);
            error_log('Session token: ' . $_SESSION['csrf_token']);
            die('CSRF token validation failed: Token mismatch');
        }
    }

    // Generate CSRF token
    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Check workflow payment status
    protected function checkWorkflowAccess($patient_id, $required_step) {
        $stmt = $this->pdo->prepare("SELECT * FROM workflow_status WHERE patient_id = ?");
        $stmt->execute([$patient_id]);
        $workflow = $stmt->fetch();

        if (!$workflow) {
            return ['access' => false, 'message' => 'Patient workflow not initialized'];
        }

        $step_requirements = [
            'consultation' => 'consultation_registration_paid',
            'lab_tests' => 'consultation_registration_paid',
            'results_review' => 'lab_tests_paid',
            'medicine' => 'consultation_registration_paid'
        ];

        if (isset($step_requirements[$required_step])) {
            $payment_field = $step_requirements[$required_step];
            if (!$workflow[$payment_field]) {
                return [
                    'access' => false,
                    'message' => "Payment required for " . str_replace('_', ' ', $required_step) . " step",
                    'step' => $required_step
                ];
            }
        }

        return ['access' => true];
    }

    // Get workflow status for patient
    protected function getWorkflowStatus($patient_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM workflow_status WHERE patient_id = ?");
        $stmt->execute([$patient_id]);
        return $stmt->fetch();
    }

    // Initialize workflow for new patient
    protected function initializeWorkflow($patient_id) {
        // Do not insert an explicit current_step here because the database
        // enum does not include 'registered'. Let the table default take effect
        // (default is 'consultation_registration'). This avoids enum truncation warnings.
        $stmt = $this->pdo->prepare("INSERT INTO workflow_status (patient_id) VALUES (?)");
        $stmt->execute([$patient_id]);
        return $this->pdo->lastInsertId();
    }

    // Update workflow status
    protected function updateWorkflowStatus($patient_id, $new_status, $additional_updates = []) {
        $stmt = $this->pdo->prepare("UPDATE workflow_status SET current_step = ?, updated_at = NOW() WHERE patient_id = ?");
        $stmt->execute([$new_status, $patient_id]);

        // Handle additional status updates
        if (!empty($additional_updates)) {
            $update_parts = [];
            $params = [];
            foreach ($additional_updates as $field => $value) {
                $update_parts[] = "$field = ?";
                $params[] = $value;
            }
            $params[] = $patient_id;

            $stmt = $this->pdo->prepare("UPDATE workflow_status SET " . implode(', ', $update_parts) . ", updated_at = NOW() WHERE patient_id = ?");
            $stmt->execute($params);
        }

        return $stmt->rowCount() > 0;
    }

    // Get complete patient journey
    protected function getPatientJourney($patient_id) {
        // Get workflow status
        $stmt = $this->pdo->prepare("SELECT * FROM workflow_status WHERE patient_id = ?");
        $stmt->execute([$patient_id]);
        $workflow = $stmt->fetch();

        // Get all consultations
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.first_name as doctor_first, u.last_name as doctor_last
            FROM consultations c
            JOIN users u ON c.doctor_id = u.id
            WHERE c.patient_id = ?
            ORDER BY c.appointment_date DESC
        ");
        $stmt->execute([$patient_id]);
        $consultations = $stmt->fetchAll();

        // Get lab results
        $stmt = $this->pdo->prepare("
            SELECT lr.*, t.name as test_name, u.first_name as technician_first, u.last_name as technician_last
            FROM lab_results lr
            JOIN tests t ON lr.test_id = t.id
            JOIN consultations c ON lr.consultation_id = c.id
            JOIN users u ON lr.technician_id = u.id
            WHERE c.patient_id = ?
            ORDER BY lr.created_at DESC
        ");
        $stmt->execute([$patient_id]);
        $lab_results = $stmt->fetchAll();

        // Get payments
        $stmt = $this->pdo->prepare("
            SELECT sp.*, u.first_name as processed_by_first, u.last_name as processed_by_last,
                   sp.payment_date as payment_date, sp.amount as amount, sp.payment_method as payment_method, sp.status as status
            FROM step_payments sp
            JOIN users u ON sp.processed_by = u.id
            WHERE sp.workflow_id = (SELECT id FROM workflow_status WHERE patient_id = ?)
            ORDER BY sp.payment_date DESC
        ");
        $stmt->execute([$patient_id]);
        $payments = $stmt->fetchAll();

        return [
            'workflow' => $workflow,
            'consultations' => $consultations,
            'lab_results' => $lab_results,
            'payments' => $payments
        ];
    }

    // Process step payment
    protected function processStepPayment($workflow_id, $step, $amount, $payment_method, $processed_by, $transaction_id = null, $notes = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO step_payments (workflow_id, step, amount, payment_method, transaction_id, processed_by, status, notes)
            VALUES (?, ?, ?, ?, ?, ?, 'completed', ?)
        ");
        $stmt->execute([$workflow_id, $step, $amount, $payment_method, $transaction_id, $processed_by, $notes]);

        // Update workflow status to mark payment as paid
        $payment_field = $step . '_paid';
        $stmt = $this->pdo->prepare("UPDATE workflow_status SET $payment_field = TRUE, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$workflow_id]);

        return $this->pdo->lastInsertId();
    }
}
?>
