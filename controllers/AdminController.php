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

        $this->render('admin/dashboard', [
            'stats' => $stats
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
                $role = trim($_POST['role']);
                $password = trim($_POST['password']);

                // Validation
                if (empty($username) || empty($email) || empty($first_name) || empty($last_name) || empty($role) || empty($password)) {
                    throw new Exception('Please fill all required fields');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Please enter a valid email address');
                }

                if (!in_array($role, ['admin', 'doctor', 'receptionist', 'lab_technician'])) {
                    throw new Exception('Invalid role selected');
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

                // Create user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->pdo->prepare("
                    INSERT INTO users (username, email, first_name, last_name, phone, role, password_hash, is_active, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())
                ");
                $stmt->execute([$username, $email, $first_name, $last_name, $phone, $role, $password_hash]);

                $_SESSION['success'] = 'User created successfully';
                $this->redirect('admin/users');

            } catch (Exception $e) {
                $_SESSION['error'] = 'Failed to create user: ' . $e->getMessage();
            }
        }

        $this->render('admin/add_user', [
            'csrf_token' => $this->generateCSRF()
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF($_POST['csrf_token']);

            try {
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $first_name = trim($_POST['first_name']);
                $last_name = trim($_POST['last_name']);
                $phone = trim($_POST['phone']);
                $role = trim($_POST['role']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;

                // Validation
                if (empty($username) || empty($email) || empty($first_name) || empty($last_name) || empty($role)) {
                    throw new Exception('Please fill all required fields');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Please enter a valid email address');
                }

                if (!in_array($role, ['admin', 'doctor', 'receptionist', 'lab_technician'])) {
                    throw new Exception('Invalid role selected');
                }

                // Check if username or email already exists (excluding current user)
                $stmt = $this->pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
                $stmt->execute([$username, $email, $user_id]);
                if ($stmt->fetch()) {
                    throw new Exception('Username or email already exists');
                }

                // Update user
                $stmt = $this->pdo->prepare("
                    UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, 
                                   phone = ?, role = ?, is_active = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$username, $email, $first_name, $last_name, $phone, $role, $is_active, $user_id]);

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

                $_SESSION['success'] = 'User updated successfully';
                $this->redirect('admin/users');

            } catch (Exception $e) {
                $_SESSION['error'] = 'Failed to update user: ' . $e->getMessage();
            }
        }

        $this->render('admin/edit_user', [
            'user' => $user,
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

        return $stats;
    }
}
?>
