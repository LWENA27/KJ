<?php
require_once __DIR__ . '/../includes/BaseController.php';

class AuthController extends BaseController {

    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirectToDashboard();
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $username = $this->sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Please fill in all fields';
            } else {
                // Check credentials
                $stmt = $this->pdo->prepare("SELECT id, password_hash, role, first_name, last_name FROM users WHERE username = ? AND is_active = 1");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $this->redirectToDashboard();
                } else {
                    $error = 'Invalid username or password';
                }
            }
        }

        $this->render('auth/login', [
            'error' => $error,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function logout() {
        session_destroy();
        $this->redirect('auth/login');
    }

    private function redirectToDashboard() {
        $role = $_SESSION['user_role'];
        switch ($role) {
            case 'admin':
                $this->redirect('admin/dashboard');
                break;
            case 'receptionist':
                $this->redirect('receptionist/dashboard');
                break;
            case 'doctor':
                $this->redirect('doctor/dashboard');
                break;
            case 'lab_technician':
                $this->redirect('lab/dashboard');
                break;
            default:
                $this->redirect('auth/login');
        }
    }
}
?>
