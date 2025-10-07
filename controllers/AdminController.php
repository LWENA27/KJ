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
        $medicines = $this->pdo->query("SELECT id, name, generic_name, stock_quantity, unit_price, expiry_date FROM medicines ORDER BY name")->fetchAll();

        $this->render('admin/medicines', [
            'medicines' => $medicines
        ]);
    }

    public function tests() {
        // lab_tests table uses `test_name`, `test_code`, `category_id`, `price`
        $tests = $this->pdo->query("SELECT id, test_name as name, test_code as code, category_id, price FROM lab_tests ORDER BY test_name")->fetchAll();

        $this->render('admin/tests', [
            'tests' => $tests
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
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM consultations WHERE DATE(appointment_date) = CURDATE()");
        $stmt->execute();
        $stats['today_consultations'] = $stmt->fetch()['total'];

        // Total medicines
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM medicines");
        $stats['total_medicines'] = $stmt->fetch()['total'];

        // Low stock medicines
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM medicines WHERE stock_quantity < 10");
        $stats['low_stock'] = $stmt->fetch()['total'];

        // Pending payments
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM payments WHERE status = 'pending'");
        $stats['pending_payments'] = $stmt->fetch()['total'];

        return $stats;
    }
}
?>
