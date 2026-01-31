<?php
require_once __DIR__ . '/../includes/BaseController.php';

class AdminController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireRole('admin');
    }

    public function dashboard() {
        // Get statistics
        $stats = $this->getDashboardStats();
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity();

        $this->render('admin/dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity
        ]);
    }

    public function users() {
        $users = $this->pdo->query("SELECT id, username, email, role, first_name, last_name, phone, is_active, created_at FROM users ORDER BY created_at DESC")->fetchAll();

        $this->render('admin/users', [
            'users' => $users
        ]);
    }

    public function add_user() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF($_POST['csrf_token']);

            try {
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $first_name = trim($_POST['first_name']);
                $last_name = trim($_POST['last_name']);
                $phone = trim($_POST['phone']);
                $roles = $_POST['roles'] ?? []; // Multiple roles support
                $primary_role = trim($_POST['primary_role'] ?? '');
                $password = trim($_POST['password']);

                // Validation
                if (empty($username) || empty($email) || empty($first_name) || empty($last_name) || empty($password)) {
                    throw new Exception('Please fill all required fields');
                }

                if (empty($roles) || !is_array($roles)) {
                    throw new Exception('Please select at least one role');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Please enter a valid email address');
                }

                $allowed_roles = ['admin', 'doctor', 'receptionist', 'accountant', 'pharmacist', 'lab_technician', 'radiologist', 'nurse'];
                foreach ($roles as $role) {
                    if (!in_array($role, $allowed_roles)) {
                        throw new Exception('Invalid role selected: ' . $role);
                    }
                }

                // If no primary role specified, use first selected role
                if (empty($primary_role) || !in_array($primary_role, $roles)) {
                    $primary_role = $roles[0];
                }

                if (strlen($password) < 6) {
                    throw new Exception('Password must be at least 6 characters long');
                }

                // Check if username or email already exists
                $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                if ($stmt->fetch()) {
                    throw new Exception('Username or email already exists');
                }

                $this->pdo->beginTransaction();

                // Create user with primary role
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->pdo->prepare("
                    INSERT INTO users (username, email, first_name, last_name, phone, role, password_hash, is_active, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())
                ");
                $stmt->execute([$username, $email, $first_name, $last_name, $phone, $primary_role, $password_hash]);
                $user_id = $this->pdo->lastInsertId();

                // Insert all roles into user_roles table
                $stmt = $this->pdo->prepare("
                    INSERT INTO user_roles (user_id, role, is_primary, granted_by, granted_at, is_active)
                    VALUES (?, ?, ?, ?, NOW(), 1)
                ");
                
                foreach ($roles as $role) {
                    $is_primary = ($role === $primary_role) ? 1 : 0;
                    $stmt->execute([$user_id, $role, $is_primary, $_SESSION['user_id']]);
                }

                $this->pdo->commit();

                $_SESSION['success'] = 'User created successfully with ' . count($roles) . ' role(s)';
                $this->redirect('admin/users');

            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                $_SESSION['error'] = 'Failed to create user: ' . $e->getMessage();
            }
        }

        $this->render('admin/add_user', [
            'csrf_token' => $this->generateCSRF(),
            'available_roles' => ['admin', 'doctor', 'receptionist', 'accountant', 'pharmacist', 'lab_technician', 'radiologist', 'nurse']
        ]);
    }

    public function edit_user() {
        $user_id = $_GET['id'] ?? null;
        if (!$user_id) {
            $_SESSION['error'] = 'User ID is required';
            $this->redirect('admin/users');
            return;
        }

        // Get user data
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['error'] = 'User not found';
            $this->redirect('admin/users');
            return;
        }

        // Get user's current roles from user_roles table
        $stmt = $this->pdo->prepare("
            SELECT role, is_primary 
            FROM user_roles 
            WHERE user_id = ? AND is_active = 1
        ");
        $stmt->execute([$user_id]);
        $user_roles_data = $stmt->fetchAll();
        
        $user_roles = [];
        $primary_role = $user['role']; // fallback to users table role
        foreach ($user_roles_data as $role_data) {
            $user_roles[] = $role_data['role'];
            if ($role_data['is_primary']) {
                $primary_role = $role_data['role'];
            }
        }
        
        // If no roles in user_roles table, use the role from users table
        if (empty($user_roles)) {
            $user_roles = [$user['role']];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF($_POST['csrf_token']);

            try {
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $first_name = trim($_POST['first_name']);
                $last_name = trim($_POST['last_name']);
                $phone = trim($_POST['phone']);
                $roles = $_POST['roles'] ?? []; // Multiple roles support
                $primary_role_new = trim($_POST['primary_role'] ?? '');
                $is_active = isset($_POST['is_active']) ? 1 : 0;

                // Validation
                if (empty($username) || empty($email) || empty($first_name) || empty($last_name)) {
                    throw new Exception('Please fill all required fields');
                }

                if (empty($roles) || !is_array($roles)) {
                    throw new Exception('Please select at least one role');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Please enter a valid email address');
                }

                $allowed_roles = ['admin', 'doctor', 'receptionist', 'accountant', 'pharmacist', 'lab_technician', 'radiologist', 'nurse'];
                foreach ($roles as $role) {
                    if (!in_array($role, $allowed_roles)) {
                        throw new Exception('Invalid role selected: ' . $role);
                    }
                }

                // If no primary role specified, use first selected role
                if (empty($primary_role_new) || !in_array($primary_role_new, $roles)) {
                    $primary_role_new = $roles[0];
                }

                // Check if username or email already exists (excluding current user)
                $stmt = $this->pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
                $stmt->execute([$username, $email, $user_id]);
                if ($stmt->fetch()) {
                    throw new Exception('Username or email already exists');
                }

                $this->pdo->beginTransaction();

                // Update user with primary role
                $stmt = $this->pdo->prepare("
                    UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, 
                                   phone = ?, role = ?, is_active = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$username, $email, $first_name, $last_name, $phone, $primary_role_new, $is_active, $user_id]);

                // Update password if provided
                if (!empty($_POST['password'])) {
                    $password = trim($_POST['password']);
                    if (strlen($password) < 6) {
                        throw new Exception('Password must be at least 6 characters long');
                    }
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$password_hash, $user_id]);
                }

                // Update user_roles table - deactivate old roles
                $stmt = $this->pdo->prepare("UPDATE user_roles SET is_active = 0 WHERE user_id = ?");
                $stmt->execute([$user_id]);

                // Insert new roles
                $stmt = $this->pdo->prepare("
                    INSERT INTO user_roles (user_id, role, is_primary, granted_by, granted_at, is_active)
                    VALUES (?, ?, ?, ?, NOW(), 1)
                    ON DUPLICATE KEY UPDATE is_primary = VALUES(is_primary), is_active = 1, granted_at = NOW()
                ");
                
                foreach ($roles as $role) {
                    $is_primary = ($role === $primary_role_new) ? 1 : 0;
                    $stmt->execute([$user_id, $role, $is_primary, $_SESSION['user_id']]);
                }

                $this->pdo->commit();

                $_SESSION['success'] = 'User updated successfully with ' . count($roles) . ' role(s)';
                $this->redirect('admin/users');

            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                $_SESSION['error'] = 'Failed to update user: ' . $e->getMessage();
            }
        }

        $this->render('admin/edit_user', [
            'user' => $user,
            'user_roles' => $user_roles,
            'primary_role' => $primary_role,
            'available_roles' => ['admin', 'doctor', 'receptionist', 'accountant', 'pharmacist', 'lab_technician', 'radiologist', 'nurse'],
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function delete_user() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/users');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);

        try {
            $user_id = intval($_POST['user_id']);
            
            if ($user_id <= 0) {
                throw new Exception('Invalid user ID');
            }

            // Prevent deleting yourself
            if ($user_id == $_SESSION['user_id']) {
                throw new Exception('You cannot delete your own account');
            }

            // Check if user exists
            $stmt = $this->pdo->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception('User not found');
            }

            // Delete user
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);

            $_SESSION['success'] = 'User deleted successfully';

        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete user: ' . $e->getMessage();
        }

        $this->redirect('admin/users');
    }

    public function patients() {
        $patients = $this->pdo->query("SELECT id, first_name, last_name, date_of_birth, gender, phone, email, created_at FROM patients ORDER BY created_at DESC")->fetchAll();

        $this->render('admin/patients', [
            'patients' => $patients
        ]);
    }

    /**
     * Manual backup trigger and list backups
     */
    public function backup_database() {
        // Only admin allowed (constructor enforces role)
        $backupDir = __DIR__ . '/../../storage/backups';
        $backupFiles = [];
        // Ensure backups directory exists and is writable. If creation fails, show a clear error to admin.
        if (!is_dir($backupDir)) {
            $parent = dirname($backupDir);
            if (!is_writable($parent)) {
                error_log("mkdir(): parent directory not writable: {$parent}");
                $_SESSION['error'] = 'Backup directory cannot be created: parent folder is not writable. Please run: sudo mkdir -p ' . escapeshellarg($backupDir) . ' && sudo chown -R www-data:www-data ' . escapeshellarg(dirname(__DIR__ . '/../../storage')) . ' && sudo chmod -R 775 ' . escapeshellarg(dirname(__DIR__ . '/../../storage')) . '\n(or adjust ownership to your webserver user)';
            } else {
                try {
                    if (!mkdir($backupDir, 0755, true) && !is_dir($backupDir)) {
                        throw new Exception('Failed to create backup directory');
                    }
                } catch (Exception $e) {
                    error_log('mkdir(): ' . $e->getMessage());
                    $_SESSION['error'] = 'Failed to create backup directory: ' . $e->getMessage();
                }
            }
        } else {
            if (!is_writable($backupDir)) {
                // Directory exists but not writable
                error_log("backup dir not writable: {$backupDir}");
                $_SESSION['error'] = 'Backup directory exists but is not writable. Please run: sudo chown -R www-data:www-data ' . escapeshellarg($backupDir) . ' && sudo chmod -R 775 ' . escapeshellarg($backupDir);
            }
        }

        // Handle POST request to trigger backup
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation: catch errors and show friendly message
            try {
                $this->validateCSRF($_POST['csrf_token'] ?? '');
            } catch (Exception $e) {
                error_log('CSRF validation failed during backup: ' . $e->getMessage());
                $_SESSION['error'] = 'CSRF token validation failed. Please refresh the page and try again.';
                $this->redirect('admin/backup_database');
                return;
            }

            // Ensure backup directory is writable before attempting backup
            if (!is_dir($backupDir) || !is_writable($backupDir)) {
                $_SESSION['error'] = $_SESSION['error'] ?? 'Backup directory is not writable. Please fix permissions on storage/backups.';
                $this->redirect('admin/backup_database');
                return;
            }

            // Run the CLI backup script (use current PHP binary)
            $script = __DIR__ . '/../../tools/backup_database.php';
            $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
            $cmd = escapeshellarg($php) . ' ' . escapeshellarg($script) . ' 2>&1';
            exec($cmd, $output, $returnVar);
            if ($returnVar === 0) {
                $_SESSION['success'] = 'Backup completed successfully';
            } else {
                error_log('Backup script failed: ' . implode("\n", $output));
                $_SESSION['error'] = 'Backup failed. See logs for details.';
            }
            $this->redirect('admin/backup_database');
            return;
        }

        // List backups for display
        $files = glob($backupDir . '/*.sql.gz');
        if ($files) {
            usort($files, function($a, $b){ return filemtime($b) - filemtime($a); });
            foreach ($files as $f) {
                $backupFiles[] = [
                    'name' => basename($f),
                    'path' => $f,
                    'size' => filesize($f),
                    'mtime' => filemtime($f)
                ];
            }
        }

        $this->render('admin/backup', [
            'backups' => $backupFiles,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function delete_backup() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $file = $_GET['file'] ?? '';
            $file = basename($file);
            $backupPath = __DIR__ . '/../../storage/backups/' . $file;
            if ($file && is_file($backupPath)) {
                @unlink($backupPath);
                $_SESSION['success'] = 'Backup deleted: ' . $file;
            } else {
                $_SESSION['error'] = 'Backup not found';
            }
        }
        $this->redirect('admin/backup_database');
    }

    public function medicines() {
        $medicines = $this->pdo->query("
            SELECT m.id, m.name, m.generic_name, m.unit_price,
                   COALESCE(SUM(mb.quantity_remaining), 0) as stock_quantity,
                   MIN(mb.expiry_date) as expiry_date
            FROM medicines m
            LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
            GROUP BY m.id, m.name, m.generic_name, m.unit_price
            ORDER BY m.name
        ")->fetchAll();

        $this->render('admin/medicines', [
            'medicines' => $medicines
        ]);
    }

    public function tests() {
        // lab_tests table uses `test_name`, `test_code`, `category_id`, `price`
        $tests = $this->pdo->query("
            SELECT lt.id, lt.test_name as name, lt.test_code as code, 
                   lt.category_id, lt.price, lt.normal_range, lt.unit,
                   ltc.category_name as category
            FROM lab_tests lt
            LEFT JOIN lab_test_categories ltc ON lt.category_id = ltc.id
            ORDER BY lt.test_name
        ")->fetchAll();

        $this->render('admin/tests', [
            'tests' => $tests
        ]);
    }

    public function view_patient($patient_id = null) {
        // Accept either path param or ?id= fallback
        if ($patient_id === null) {
            $patient_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
        }
        if (!$patient_id) {
            $this->redirect('admin/patients');
        }

        // Get patient details
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch();

        if (!$patient) {
            $this->redirect('admin/patients');
        }

        // Get existing consultations (basic info for admin)
        $stmt = $this->pdo->prepare("
            SELECT c.*, pv.visit_date, u.first_name as doctor_first, u.last_name as doctor_last
            FROM consultations c
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            LEFT JOIN users u ON c.doctor_id = u.id
            WHERE c.patient_id = ? 
            ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at) DESC
            LIMIT 5
        ");
        $stmt->execute([$patient_id]);
        $consultations = $stmt->fetchAll();

        // Get all vital signs for this patient (for each consultation/visit)
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT vs.*, 
                   pv.visit_date, 
                   c.id as consultation_id,
                   vs.visit_id
            FROM vital_signs vs
            LEFT JOIN patient_visits pv ON vs.visit_id = pv.id
            LEFT JOIN consultations c ON (
                (vs.visit_id = c.visit_id AND vs.patient_id = c.patient_id) OR
                (vs.patient_id = c.patient_id AND DATE(vs.recorded_at) = DATE(c.created_at))
            )
            WHERE vs.patient_id = ?
            ORDER BY vs.recorded_at DESC
        ");
        $stmt->execute([$patient_id]);
        $vital_signs = $stmt->fetchAll();

        // Get payment history for this patient
        $stmt = $this->pdo->prepare("
            SELECT p.*, pv.visit_date
            FROM payments p
            LEFT JOIN patient_visits pv ON p.visit_id = pv.id
            WHERE p.patient_id = ?
            ORDER BY p.payment_date DESC
            LIMIT 10
        ");
        $stmt->execute([$patient_id]);
        $payments = $stmt->fetchAll();

        // Get lab test orders for this patient (for real-time tracking)
        $stmt = $this->pdo->prepare("
            SELECT lto.*, lt.test_name, lt.test_code, lr.result_value, lr.result_text, lr.completed_at as result_completed_at
            FROM lab_test_orders lto
            JOIN lab_tests lt ON lto.test_id = lt.id
            LEFT JOIN lab_results lr ON lto.id = lr.order_id
            WHERE lto.patient_id = ?
            ORDER BY lto.created_at DESC
        ");
        $stmt->execute([$patient_id]);
        $lab_orders = $stmt->fetchAll();

        $this->render('admin/view_patient', [
            'patient' => $patient,
            'consultations' => $consultations,
            'vital_signs' => $vital_signs,
            'payments' => $payments,
            'lab_orders' => $lab_orders,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    private function getDashboardStats() {
        $stats = [];

        // =================== BASIC METRICS ===================
        // Total users
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM users");
        $stats['total_users'] = $stmt->fetch()['total'];

        // Total patients
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM patients");
        $stats['total_patients'] = $stmt->fetch()['total'];

        // Today's consultations
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM consultations c LEFT JOIN patient_visits pv ON c.visit_id = pv.id WHERE DATE(COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) = CURDATE()");
        $stmt->execute();
        $stats['today_consultations'] = $stmt->fetch()['total'];

        // Total medicines
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM medicines");
        $stats['total_medicines'] = $stmt->fetch()['total'];

        // Low stock medicines (using medicine_batches with reorder level)
        $stmt = $this->pdo->query("
            SELECT COUNT(*) as total
            FROM (
                SELECT m.id
                FROM medicines m
                LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
                GROUP BY m.id, m.reorder_level
                HAVING COALESCE(SUM(mb.quantity_remaining), 0) < COALESCE(m.reorder_level, 20)
            ) as low_stock_meds
        ");
        $result = $stmt->fetch();
        $stats['low_stock'] = $result ? $result['total'] : 0;

        // Pending payments
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM payments WHERE payment_status = 'pending'");
        $result = $stmt->fetch();
        $stats['pending_payments'] = $result ? $result['total'] : 0;

        // =================== FINANCIAL METRICS ===================
        // Dispensary/Pharmacy Income - Today
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM payments 
            WHERE payment_status = 'completed' 
            AND item_type = 'prescription'
            AND DATE(payment_date) = CURDATE()
        ");
        $stmt->execute();
        $stats['dispensary_income_today'] = $stmt->fetch()['total'];

        // Dispensary/Pharmacy Income - This Month
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM payments 
            WHERE payment_status = 'completed' 
            AND item_type = 'prescription'
            AND MONTH(payment_date) = MONTH(CURDATE()) 
            AND YEAR(payment_date) = YEAR(CURDATE())
        ");
        $stmt->execute();
        $stats['dispensary_income_month'] = $stmt->fetch()['total'];

        // Dispensary/Pharmacy Income - Total
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM payments 
            WHERE payment_status = 'completed' 
            AND item_type = 'prescription'
        ");
        $stmt->execute();
        $stats['dispensary_income_total'] = $stmt->fetch()['total'];

        // Total Revenue - Today
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM payments 
            WHERE payment_status = 'completed' 
            AND DATE(payment_date) = CURDATE()
        ");
        $stmt->execute();
        $stats['revenue_today'] = $stmt->fetch()['total'];

        // Total Revenue - This Month
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM payments 
            WHERE payment_status = 'completed' 
            AND MONTH(payment_date) = MONTH(CURDATE()) 
            AND YEAR(payment_date) = YEAR(CURDATE())
        ");
        $stmt->execute();
        $stats['revenue_month'] = $stmt->fetch()['total'];

        // Revenue breakdown by service type
        $stmt = $this->pdo->prepare("
            SELECT 
                CASE 
                    WHEN payment_type = 'consultation' THEN 'Consultation'
                    WHEN item_type = 'prescription' THEN 'Pharmacy'
                    WHEN item_type = 'lab_order' THEN 'Lab'
                    WHEN item_type = 'radiology_order' THEN 'Radiology'
                    WHEN item_type = 'service' OR item_type = 'service_order' THEN 'IPD'
                    ELSE 'Other'
                END as service_type,
                COALESCE(SUM(amount), 0) as total
            FROM payments 
            WHERE payment_status = 'completed'
            AND MONTH(payment_date) = MONTH(CURDATE()) 
            AND YEAR(payment_date) = YEAR(CURDATE())
            GROUP BY service_type
        ");
        $stmt->execute();
        $revenue_breakdown = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $stats['revenue_consultation'] = $revenue_breakdown['Consultation'] ?? 0;
        $stats['revenue_pharmacy'] = $revenue_breakdown['Pharmacy'] ?? 0;
        $stats['revenue_lab'] = $revenue_breakdown['Lab'] ?? 0;
        $stats['revenue_radiology'] = $revenue_breakdown['Radiology'] ?? 0;
        $stats['revenue_ipd'] = $revenue_breakdown['IPD'] ?? 0;

        // Completed payments count
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM payments WHERE payment_status = 'completed'");
        $stats['completed_payments'] = $stmt->fetch()['total'];

        // =================== APPOINTMENTS ===================
        // Note: This system doesn't have a separate appointments table
        // Using patient_visits as a proxy for appointments
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total 
            FROM patient_visits 
            WHERE DATE(visit_date) = CURDATE()
        ");
        $stmt->execute();
        $stats['appointments_today'] = $stmt->fetch()['total'];

        // Pending appointments (visits without completed consultations)
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT pv.id) as total 
            FROM patient_visits pv 
            LEFT JOIN consultations c ON pv.id = c.visit_id AND c.status = 'completed'
            WHERE c.id IS NULL 
            AND DATE(pv.visit_date) >= CURDATE()
        ");
        $stmt->execute();
        $stats['pending_appointments'] = $stmt->fetch()['total'];

        // =================== LAB DEPARTMENT ===================
        // Pending lab test orders
        $stmt = $this->pdo->query("
            SELECT COUNT(*) as total 
            FROM lab_test_orders 
            WHERE status IN ('pending', 'sample_collected', 'in_progress')
        ");
        $stats['pending_lab_orders'] = $stmt->fetch()['total'];

        // Completed tests today
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total 
            FROM lab_results 
            WHERE DATE(completed_at) = CURDATE()
        ");
        $stmt->execute();
        $stats['lab_completed_today'] = $stmt->fetch()['total'];

        // =================== RADIOLOGY DEPARTMENT ===================
        // Pending radiology orders
        $stmt = $this->pdo->query("
            SELECT COUNT(*) as total 
            FROM radiology_test_orders 
            WHERE status IN ('pending', 'scheduled', 'in_progress')
        ");
        $stats['pending_radiology_orders'] = $stmt->fetch()['total'];

        // Completed radiology tests today
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total 
            FROM radiology_results 
            WHERE DATE(completed_at) = CURDATE()
        ");
        $stmt->execute();
        $stats['radiology_completed_today'] = $stmt->fetch()['total'];

        // =================== PHARMACY DEPARTMENT ===================
        // Pending prescriptions
        $stmt = $this->pdo->query("
            SELECT COUNT(*) as total 
            FROM prescriptions 
            WHERE status IN ('pending', 'partial')
        ");
        $stats['pending_prescriptions'] = $stmt->fetch()['total'];

        // Dispensed prescriptions today
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total 
            FROM prescriptions 
            WHERE status = 'dispensed' 
            AND DATE(dispensed_at) = CURDATE()
        ");
        $stmt->execute();
        $stats['dispensed_today'] = $stmt->fetch()['total'];

        // =================== IPD DEPARTMENT ===================
        // Active admissions
        $stmt = $this->pdo->query("
            SELECT COUNT(*) as total 
            FROM ipd_admissions 
            WHERE status = 'active'
        ");
        $stats['active_admissions'] = $stmt->fetch()['total'];

        // New admissions today
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total 
            FROM ipd_admissions 
            WHERE DATE(admission_datetime) = CURDATE()
        ");
        $stmt->execute();
        $stats['admissions_today'] = $stmt->fetch()['total'];

        // Bed statistics
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(*) as total_beds,
                SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_beds,
                SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied_beds
            FROM ipd_beds 
            WHERE is_active = 1
        ");
        $beds = $stmt->fetch();
        $stats['total_beds'] = $beds['total_beds'] ?? 0;
        $stats['available_beds'] = $beds['available_beds'] ?? 0;
        $stats['occupied_beds'] = $beds['occupied_beds'] ?? 0;
        
        // Calculate bed occupancy percentage
        if ($stats['total_beds'] > 0) {
            $stats['bed_occupancy_percent'] = round(($stats['occupied_beds'] / $stats['total_beds']) * 100, 1);
        } else {
            $stats['bed_occupancy_percent'] = 0;
        }

        return $stats;
    }

    private function getRecentActivity() {
        $activities = [];

        // Recent patient registrations
        $stmt = $this->pdo->query("
            SELECT 
                'patient_registration' as activity_type,
                CONCAT(first_name, ' ', last_name) as description,
                created_at as timestamp
            FROM patients
            ORDER BY created_at DESC
            LIMIT 3
        ");
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Recent consultation completions
        $stmt = $this->pdo->query("
            SELECT 
                'consultation' as activity_type,
                CONCAT('Consultation with Dr. ', u.first_name, ' ', u.last_name, ' for ', p.first_name, ' ', p.last_name) as description,
                c.created_at as timestamp
            FROM consultations c
            JOIN users u ON c.doctor_id = u.id
            JOIN patients p ON c.patient_id = p.id
            WHERE c.status = 'completed'
            ORDER BY c.created_at DESC
            LIMIT 3
        ");
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Recent medicine stock updates (using medicine_batches)
        $stmt = $this->pdo->query("
            SELECT 
                'stock_update' as activity_type,
                CONCAT('Stock updated: ', m.name, ' (', mb.quantity_received, ' units added)') as description,
                mb.created_at as timestamp
            FROM medicine_batches mb
            JOIN medicines m ON mb.medicine_id = m.id
            ORDER BY mb.created_at DESC
            LIMIT 2
        ");
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Recent payment transactions
        $stmt = $this->pdo->query("
            SELECT 
                'payment' as activity_type,
                CONCAT('Payment received: Tsh ', FORMAT(amount, 0), ' from ', p.first_name, ' ', p.last_name) as description,
                pay.payment_date as timestamp
            FROM payments pay
            JOIN patients p ON pay.patient_id = p.id
            WHERE pay.payment_status = 'completed'
            ORDER BY pay.payment_date DESC
            LIMIT 2
        ");
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Sort all activities by timestamp and limit to 10
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return array_slice($activities, 0, 10);
    }
}
?>
