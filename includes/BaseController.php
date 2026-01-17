<?php
// Base Controller Class
class BaseController {
    protected $pdo;
    protected $layout = 'layouts/main';

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Set custom layout
    protected function layout($layout) {
        $this->layout = $layout;
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
        include __DIR__ . '/../views/' . $this->layout . '.php';
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

    // Check user role - supports single role or array of roles
    // If user has any of the specified roles, access is granted
    protected function requireRole($roles) {
        $this->requireLogin();
        
        // Convert single role to array
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        // Check primary role from session
        $userPrimaryRole = $_SESSION['user_role'] ?? '';
        
        // Check if primary role matches any allowed role
        if (in_array($userPrimaryRole, $roles)) {
            return true;
        }
        
        // Check additional roles from user_roles table (multi-role support)
        if ($this->hasAnyRole($roles)) {
            return true;
        }
        
        // No matching role found - redirect to login
        $this->redirect('auth/login');
    }
    
    // Check if user has any of the specified roles (multi-role support)
    protected function hasAnyRole($roles) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Convert single role to array
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        // First check session cache
        if (isset($_SESSION['user_roles']) && is_array($_SESSION['user_roles'])) {
            foreach ($roles as $role) {
                if (in_array($role, $_SESSION['user_roles'])) {
                    return true;
                }
            }
            return false;
        }
        
        // Load from database and cache in session
        try {
            $placeholders = implode(',', array_fill(0, count($roles), '?'));
            $params = array_merge([$_SESSION['user_id']], $roles);
            
            $stmt = $this->pdo->prepare("
                SELECT role FROM user_roles 
                WHERE user_id = ? AND role IN ($placeholders) AND is_active = 1
                LIMIT 1
            ");
            $stmt->execute($params);
            
            if ($stmt->fetch()) {
                return true;
            }
        } catch (PDOException $e) {
            // Table might not exist yet, fall back to primary role check
            error_log('Multi-role check failed: ' . $e->getMessage());
        }
        
        return false;
    }
    
    // Load all roles for current user into session
    protected function loadUserRoles() {
        if (!isset($_SESSION['user_id'])) {
            return [];
        }
        
        // Return cached roles if available
        if (isset($_SESSION['user_roles']) && is_array($_SESSION['user_roles'])) {
            return $_SESSION['user_roles'];
        }
        
        $roles = [$_SESSION['user_role']]; // Start with primary role
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT role FROM user_roles 
                WHERE user_id = ? AND is_active = 1
            ");
            $stmt->execute([$_SESSION['user_id']]);
            
            while ($row = $stmt->fetch()) {
                if (!in_array($row['role'], $roles)) {
                    $roles[] = $row['role'];
                }
            }
        } catch (PDOException $e) {
            // Table might not exist yet
            error_log('Loading user roles failed: ' . $e->getMessage());
        }
        
        $_SESSION['user_roles'] = $roles;
        return $roles;
    }
    
    // Check if user has a specific permission
    protected function hasPermission($permission) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Admin has all permissions
        if ($_SESSION['user_role'] === 'admin' || $this->hasAnyRole(['admin'])) {
            return true;
        }
        
        $roles = $this->loadUserRoles();
        
        try {
            $placeholders = implode(',', array_fill(0, count($roles), '?'));
            $params = array_merge($roles, [$permission]);
            
            $stmt = $this->pdo->prepare("
                SELECT id FROM role_permissions 
                WHERE role IN ($placeholders) AND permission = ?
                LIMIT 1
            ");
            $stmt->execute($params);
            
            return (bool)$stmt->fetch();
        } catch (PDOException $e) {
            // Table might not exist, fall back to role-based check
            error_log('Permission check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    // Require a specific permission
    protected function requirePermission($permission) {
        $this->requireLogin();
        
        if (!$this->hasPermission($permission)) {
            $_SESSION['error'] = 'You do not have permission to access this feature.';
            $this->redirect('auth/login');
        }
    }

    // Sanitize input
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        // Normalize nulls to empty string to avoid trim(null) deprecation warnings
        if ($data === null) {
            $data = '';
        }
        return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
    }

    // Validate CSRF token
    protected function validateCSRF($token = null) {
        // Allow passing token explicitly or default to POST
        if ($token === null && !isset($_POST['csrf_token'])) {
            // No token provided in POST
            throw new Exception('CSRF token validation failed: Missing token');
        }

        if (!isset($_SESSION['csrf_token'])) {
            // No token in session
            throw new Exception('CSRF token validation failed: Session token missing');
        }

        $provided = $token !== null ? $token : $_POST['csrf_token'];
        if ($provided !== $_SESSION['csrf_token']) {
            // Token mismatch
            throw new Exception('CSRF token validation failed: Token mismatch');
        }
    }

    // Generate CSRF token
    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Check workflow payment status for the latest visit
    protected function checkWorkflowAccess($patient_id, $required_step) {
        $stmt = $this->pdo->prepare("SELECT * FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$patient_id]);
        $visit = $stmt->fetch();

        if (!$visit) {
            return ['access' => false, 'message' => 'No visit found for patient'];
        }

        $visit_id = $visit['id'];

        $step_requirements = [
            'consultation' => 'consultation',  // Only check for 'consultation' payment type (not 'registration')
            'lab_tests' => 'lab_test',
            'results_review' => 'lab_test',
            'medicine' => 'medicine',
            'ipd' => 'service'
        ];

        if (isset($step_requirements[$required_step])) {
            $payment_types = $step_requirements[$required_step];
            
            // Handle both single string and array of payment types
            if (!is_array($payment_types)) {
                $payment_types = [$payment_types];
            }
            
            // Check if any of the payment types are paid
            $placeholders = implode(',', array_fill(0, count($payment_types), '?'));
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE visit_id = ? AND payment_type IN ({$placeholders}) AND payment_status = 'paid'");
            
            $params = [$visit_id];
            $params = array_merge($params, $payment_types);
            $stmt->execute($params);
            
            $count = (int)$stmt->fetchColumn();
            if ($count === 0) {
                return ['access' => false, 'message' => 'Payment required for ' . str_replace('_', ' ', $required_step), 'step' => $required_step];
            }
        }

        return ['access' => true];
    }

    // Get workflow status derived from the latest visit
    protected function getWorkflowStatus($patient_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$patient_id]);
        $visit = $stmt->fetch();
        if (!$visit) return null;

        $visit_id = $visit['id'];

        // Payments - Check for 'consultation' payment type only (not 'registration')
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE visit_id = ? AND payment_type = 'consultation' AND payment_status = 'paid'");
        $stmt->execute([$visit_id]);
        $consultation_registration_paid = (bool)$stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE visit_id = ? AND payment_type = 'lab_test' AND payment_status = 'paid'");
        $stmt->execute([$visit_id]);
        $lab_tests_paid = (bool)$stmt->fetchColumn();

        // Prescriptions
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM prescriptions WHERE visit_id = ? AND status = 'pending'");
        $stmt->execute([$visit_id]);
        $medicine_prescribed = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM prescriptions WHERE visit_id = ? AND status = 'dispensed'");
        $stmt->execute([$visit_id]);
        $medicine_dispensed = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        // Lab orders
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM lab_test_orders WHERE visit_id = ? AND status IN ('pending','sample_collected','in_progress')");
        $stmt->execute([$visit_id]);
        $lab_tests_required = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        // (Service checks computed above) -- avoid duplicate queries

        // Service orders (e.g., nursing, wound dressing, IPD procedures)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM service_orders WHERE visit_id = ? AND status IN ('pending','in_progress')");
        $stmt->execute([$visit_id]);
        $service_orders_required = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        // Check if any service payments have been made for this visit
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE visit_id = ? AND payment_type = 'service' AND payment_status = 'paid'");
        $stmt->execute([$visit_id]);
        $service_paid = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        // Determine current_step
        if ($visit['status'] === 'active') {
            if (!$consultation_registration_paid) $current_step = 'consultation_registration';
            elseif ($lab_tests_required && !$lab_tests_paid) $current_step = 'lab_tests';
            elseif ($service_orders_required && !$service_paid) $current_step = 'ipd';
            elseif ($medicine_prescribed && !$medicine_dispensed) $current_step = 'medicine_dispensing';
            else $current_step = 'in_progress';
        } else {
            $current_step = 'completed';
        }

        return [
            'visit_id' => $visit_id,
            'current_step' => $current_step,
            'consultation_registration_paid' => $consultation_registration_paid ? 1 : 0,
            'lab_tests_paid' => $lab_tests_paid ? 1 : 0,
            'results_review_paid' => $lab_tests_paid ? 1 : 0,
            'service_orders_required' => $service_orders_required,
            'service_paid' => $service_paid,
            'medicine_prescribed' => $medicine_prescribed,
            'medicine_dispensed' => $medicine_dispensed,
            'final_payment_collected' => 0,
            'lab_tests_required' => $lab_tests_required
        ];
    }

    // Get visit-level status by visit_id
    protected function getVisitStatus($visit_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM patient_visits WHERE id = ? LIMIT 1");
        $stmt->execute([$visit_id]);
        $visit = $stmt->fetch();
        if (!$visit) return null;

        $patient_id = $visit['patient_id'];

        // Payments - check for 'consultation' payment type (changed from 'registration')
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE visit_id = ? AND payment_type = 'consultation' AND payment_status = 'paid'");
        $stmt->execute([$visit_id]);
        $consultation_registration_paid = (bool)$stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE visit_id = ? AND payment_type = 'lab_test' AND payment_status = 'paid'");
        $stmt->execute([$visit_id]);
        $lab_tests_paid = (bool)$stmt->fetchColumn();

        // Prescriptions
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM prescriptions WHERE visit_id = ? AND status = 'pending'");
        $stmt->execute([$visit_id]);
        $medicine_prescribed = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM prescriptions WHERE visit_id = ? AND status = 'dispensed'");
        $stmt->execute([$visit_id]);
        $medicine_dispensed = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        // Lab orders
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM lab_test_orders WHERE visit_id = ? AND status IN ('pending','sample_collected','in_progress')");
        $stmt->execute([$visit_id]);
        $lab_tests_required = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        // Service orders (e.g., nursing, wound dressing, IPD procedures)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM service_orders WHERE visit_id = ? AND status IN ('pending','in_progress')");
        $stmt->execute([$visit_id]);
        $service_orders_required = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        // Check if any service payments have been made for this visit
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE visit_id = ? AND payment_type = 'service' AND payment_status = 'paid'");
        $stmt->execute([$visit_id]);
        $service_paid = (int)$stmt->fetchColumn() > 0 ? 1 : 0;

        // Determine current_step
        if ($visit['status'] === 'active') {
            if (!$consultation_registration_paid) $current_step = 'consultation_registration';
            elseif ($lab_tests_required && !$lab_tests_paid) $current_step = 'lab_tests';
            elseif ($service_orders_required && !$service_paid) $current_step = 'ipd';
            elseif ($medicine_prescribed && !$medicine_dispensed) $current_step = 'medicine_dispensing';
            else $current_step = 'in_progress';
        } else {
            $current_step = 'completed';
        }

        return [
            'visit_id' => $visit_id,
            'patient_id' => $patient_id,
            'current_step' => $current_step,
            'consultation_registration_paid' => $consultation_registration_paid ? 1 : 0,
            'lab_tests_paid' => $lab_tests_paid ? 1 : 0,
            'results_review_paid' => $lab_tests_paid ? 1 : 0,
            'service_orders_required' => $service_orders_required,
            'service_paid' => $service_paid,
            'medicine_prescribed' => $medicine_prescribed,
            'medicine_dispensed' => $medicine_dispensed,
            'final_payment_collected' => 0,
            'lab_tests_required' => $lab_tests_required
        ];
    }

    // Can this visit be attended by a doctor? Returns ['ok'=>bool,'reason'=>string]
    protected function canAttend($visit_id, $requirePayment = true) {
        $status = $this->getVisitStatus($visit_id);
        if (empty($status)) return ['ok' => false, 'reason' => 'visit_not_found'];
        if ($status['current_step'] === 'completed') return ['ok' => false, 'reason' => 'visit_completed'];

        // Check if a consultation already exists for this visit
        $stmt = $this->pdo->prepare("SELECT id, status FROM consultations WHERE visit_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$visit_id]);
        $c = $stmt->fetch();
        
        // Allow if consultation is 'in_progress' (doctor is currently attending)
        // Block only if consultation is already 'completed'
        if ($c && $c['status'] === 'completed') {
            return ['ok' => false, 'reason' => 'consultation_already_completed'];
        }

        if ($requirePayment && intval($status['consultation_registration_paid']) <= 0) {
            return ['ok' => false, 'reason' => 'payment_required'];
        }

        return ['ok' => true, 'reason' => 'ready'];
    }

    // Start or resume a consultation for a visit. Returns ['ok'=>bool,'consultation_id'=>int] or error.
    protected function startConsultation($visit_id, $doctor_id, $data = []) {
        // debugging: log POST data coming from attend_patient form
        error_log('=== POST DATA ===');
        error_log('next_step: ' . ($_POST['next_step'] ?? 'MISSING'));
        error_log('selected_tests: ' . ($_POST['selected_tests'] ?? 'EMPTY'));
        error_log('selected_medicines: ' . ($_POST['selected_medicines'] ?? 'EMPTY'));
        error_log('selected_allocations: ' . ($_POST['selected_allocations'] ?? 'EMPTY'));

        // ensure service-related flags exist to avoid undefined variable notices
        $service_orders_required = false;
        $service_paid = false;

        $this->pdo->beginTransaction();
        try {
            // fetch visit and patient
            $stmt = $this->pdo->prepare("SELECT * FROM patient_visits WHERE id = ? LIMIT 1");
            $stmt->execute([$visit_id]);
            $visit = $stmt->fetch();
            if (!$visit) throw new Exception('Visit not found');

            $patient_id = $visit['patient_id'];

            // check whether a consultation exists for this visit
            $stmt = $this->pdo->prepare("SELECT id, status FROM consultations WHERE visit_id = ? LIMIT 1");
            $stmt->execute([$visit_id]);
            $existing = $stmt->fetch();

            if ($existing) {
                $consultation_id = $existing['id'];
                // update to in_progress if not already
                $stmt = $this->pdo->prepare("UPDATE consultations SET doctor_id = ?, status = 'in_progress', started_at = NOW(), updated_at = NOW() WHERE id = ?");
                $stmt->execute([$doctor_id, $consultation_id]);
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO consultations (visit_id, patient_id, doctor_id, consultation_type, status, started_at, created_at, updated_at) VALUES (?, ?, ?, 'new', 'in_progress', NOW(), NOW(), NOW())");
                $stmt->execute([$visit_id, $patient_id, $doctor_id]);
                $consultation_id = $this->pdo->lastInsertId();
            }

            // ensure visit remains active
            $stmt = $this->pdo->prepare("UPDATE patient_visits SET status = 'active', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$visit_id]);

            $this->pdo->commit();
            return ['ok' => true, 'consultation_id' => $consultation_id];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['ok' => false, 'reason' => 'db_error', 'message' => $e->getMessage()];
        }
    }

    // Initialize a visit for a new patient
    protected function initializeWorkflow($patient_id) {
        $stmt = $this->pdo->prepare("INSERT INTO patient_visits (patient_id, visit_date, visit_type, registered_by, status, created_at, updated_at) VALUES (?, CURDATE(), 'consultation', ?, 'active', NOW(), NOW())");
        $stmt->execute([$patient_id, $_SESSION['user_id'] ?? 0]);
        return $this->pdo->lastInsertId();
    }

    // Update workflow status by changing the visit status
    protected function updateWorkflowStatus($patient_id, $new_status, $additional_updates = []) {
        $stmt = $this->pdo->prepare("SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$patient_id]);
        $visit = $stmt->fetch();
        if (!$visit) return false;

        $visit_id = $visit['id'];
        $visit_status = $new_status === 'completed' ? 'completed' : ($new_status === 'cancelled' ? 'cancelled' : 'active');
        $stmt = $this->pdo->prepare("UPDATE patient_visits SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$visit_status, $visit_id]);
        return $stmt->rowCount() > 0;
    }

    // Get complete patient journey: workflow, consultations, lab results, payments
    protected function getPatientJourney($patient_id) {
        $workflow = $this->getWorkflowStatus($patient_id);

        $stmt = $this->pdo->prepare("SELECT c.*, u.first_name as doctor_first, u.last_name as doctor_last FROM consultations c LEFT JOIN users u ON c.doctor_id = u.id WHERE c.patient_id = ? ORDER BY c.created_at DESC");
        $stmt->execute([$patient_id]);
        $consultations = $stmt->fetchAll();

        $stmt = $this->pdo->prepare("SELECT lr.*, t.test_name as test_name, t.test_code as test_code, u.first_name as technician_first, u.last_name as technician_last FROM lab_results lr LEFT JOIN lab_tests t ON lr.test_id = t.id LEFT JOIN users u ON lr.technician_id = u.id WHERE lr.patient_id = ? ORDER BY lr.completed_at DESC");
        $stmt->execute([$patient_id]);
        $lab_results = $stmt->fetchAll();

        $payments = [];
        $visit_id = $workflow['visit_id'] ?? null;
        if ($visit_id) {
            $stmt = $this->pdo->prepare("SELECT p.*, u.first_name as processed_by_first, u.last_name as processed_by_last FROM payments p LEFT JOIN users u ON p.collected_by = u.id WHERE p.visit_id = ? ORDER BY p.payment_date DESC");
            $stmt->execute([$visit_id]);
            $payments = $stmt->fetchAll();
        }

        return [
            'workflow' => $workflow,
            'consultations' => $consultations,
            'lab_results' => $lab_results,
            'payments' => $payments
        ];
    }

    // Process a payment for a visit
    protected function processStepPayment($visit_id, $step, $amount, $payment_method, $processed_by, $transaction_id = null, $notes = null) {
        $step_to_payment = [
            'consultation_registration' => 'registration',
            'lab_tests' => 'lab_test',
            'results_review' => 'lab_test',
            'medicine' => 'medicine',
            'ipd' => 'service'
        ];
        $payment_type = $step_to_payment[$step] ?? 'registration';

        $stmt = $this->pdo->prepare("SELECT patient_id FROM patient_visits WHERE id = ?");
        $stmt->execute([$visit_id]);
        $row = $stmt->fetch();
        $patient_id = $row['patient_id'] ?? null;

        $stmt = $this->pdo->prepare("INSERT INTO payments (visit_id, patient_id, payment_type, amount, payment_method, payment_status, reference_number, collected_by, payment_date, notes) VALUES (?, ?, ?, ?, ?, 'paid', ?, ?, NOW(), ?)");
        $stmt->execute([$visit_id, $patient_id, $payment_type, $amount, $payment_method, $transaction_id, $processed_by, $notes]);

        return $this->pdo->lastInsertId();
    }

    // Format currency in Tanzanian Shillings
    protected function formatCurrency($amount) {
        return 'Tsh ' . number_format((float)$amount, 0, '.', ',');
    }

    // Format currency without prefix (for inputs)
    protected function formatAmount($amount) {
        return number_format((float)$amount, 0, '.', ',');
    }

    // ========== CENTRALIZED DATA FETCHERS (Eliminate duplicate queries) ==========

    /**
     * Get patient by ID with basic info
     * Eliminates duplicate: SELECT * FROM patients WHERE id = ?
     */
    protected function getPatientById($patient_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        return $stmt->fetch();
    }

    /**
     * Get latest visit for a patient
     * Eliminates duplicate: SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1
     */
    protected function getLatestVisit($patient_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM patient_visits 
            WHERE patient_id = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$patient_id]);
        return $stmt->fetch();
    }

    /**
     * Get latest visit ID (shorthand)
     */
    protected function getLatestVisitId($patient_id) {
        $visit = $this->getLatestVisit($patient_id);
        return $visit ? $visit['id'] : null;
    }

    /**
     * Get aggregated medicine stock for a medicine ID
     * Eliminates duplicate: SUM(medicine_batches.quantity_remaining)
     */
    protected function getMedicineStock($medicine_id) {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(quantity_remaining), 0) as total_stock
            FROM medicine_batches
            WHERE medicine_id = ?
        ");
        $stmt->execute([$medicine_id]);
        $row = $stmt->fetch();
        return $row ? (int)$row['total_stock'] : 0;
    }

    /**
     * Deduct medicine stock using FEFO (First-Expiry-First-Out)
     * Eliminates duplicate FEFO logic across dispense functions
     */
    protected function deductMedicineStock($medicine_id, $quantity) {
        $remaining = $quantity;
        
        // Fetch batches ordered by expiry (FEFO)
        $batch_stmt = $this->pdo->prepare("
            SELECT id, quantity_remaining 
            FROM medicine_batches 
            WHERE medicine_id = ? AND quantity_remaining > 0
            ORDER BY expiry_date ASC, created_at ASC
        ");
        $batch_stmt->execute([$medicine_id]);
        $batches = $batch_stmt->fetchAll();

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            
            $deduct = min($remaining, $batch['quantity_remaining']);
            $update_stmt = $this->pdo->prepare("
                UPDATE medicine_batches 
                SET quantity_remaining = quantity_remaining - ? 
                WHERE id = ?
            ");
            $update_stmt->execute([$deduct, $batch['id']]);
            $remaining -= $deduct;
        }

        return ($remaining == 0); // true if fully deducted
    }

    /**
     * Generate payment workflow flags SQL fragment
     * Eliminates massive duplicate subqueries in patient listings
     */
    protected function getPaymentFlagsSQL($visit_id_column = 'lv.visit_id') {
        return "
            (SELECT IF(EXISTS(
                SELECT 1 FROM payments pay 
                WHERE pay.visit_id = {$visit_id_column}
                AND pay.payment_type = 'registration' 
                AND pay.payment_status = 'paid'
            ),1,0)) AS consultation_registration_paid,
            
            (SELECT IF(EXISTS(
                SELECT 1 FROM payments pay2 
                WHERE pay2.visit_id = {$visit_id_column}
                AND pay2.payment_type = 'lab_test' 
                AND pay2.payment_status = 'paid'
            ),1,0)) AS lab_tests_paid,
            
            (SELECT IF(EXISTS(
                SELECT 1 FROM payments pay3 
                WHERE pay3.visit_id = {$visit_id_column}
                AND pay3.payment_type = 'lab_test' 
                AND pay3.payment_status = 'paid'
            ),1,0)) AS results_review_paid,
            
            (SELECT IF(EXISTS(
                SELECT 1 FROM prescriptions pr 
                WHERE pr.visit_id = {$visit_id_column}
                AND pr.status = 'pending'
            ),1,0)) AS medicine_prescribed,
            
            (SELECT IF(EXISTS(
                SELECT 1 FROM prescriptions pr2 
                WHERE pr2.visit_id = {$visit_id_column}
                AND pr2.status = 'dispensed'
            ),1,0)) AS medicine_dispensed,
            
            (SELECT IF(EXISTS(
                SELECT 1 FROM lab_test_orders lto 
                WHERE lto.visit_id = {$visit_id_column}
                AND lto.status IN ('pending','sample_collected','in_progress')
            ),1,0)) AS lab_tests_required
        ";
    }

    /**
     * Get aggregated medicine stock SQL fragment for SELECT queries
     * Eliminates duplicate: LEFT JOIN medicine_batches + SUM(quantity_remaining)
     */
    protected function getMedicineStockSQL($alias = 'm') {
        return "COALESCE(
            (SELECT SUM(quantity_remaining) 
             FROM medicine_batches mb 
             WHERE mb.medicine_id = {$alias}.id), 
            0
        ) as stock_quantity";
    }

    /**
     * Get earliest expiry date SQL fragment
     */
    protected function getMedicineExpirySQL($alias = 'm') {
        return "(SELECT MIN(expiry_date) 
                FROM medicine_batches mb 
                WHERE mb.medicine_id = {$alias}.id) as expiry_date";
    }
}
?>
