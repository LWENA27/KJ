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
                    $_SESSION['user_role'] = $user['role']; // Primary role
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    
                    // Load all roles for multi-role support
                    $this->loadUserRolesForUser($user['id'], $user['role']);
                    
                    // Set active role to primary role initially
                    $_SESSION['active_role'] = $user['role'];
                    
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
    
    // Load all roles for user into session
    private function loadUserRolesForUser($user_id, $primary_role) {
        $roles = [$primary_role];
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT role FROM user_roles 
                WHERE user_id = ? AND is_active = 1
            ");
            $stmt->execute([$user_id]);
            
            while ($row = $stmt->fetch()) {
                if (!in_array($row['role'], $roles)) {
                    $roles[] = $row['role'];
                }
            }
        } catch (PDOException $e) {
            // Table might not exist yet - that's OK
            error_log('Loading user roles failed (table may not exist): ' . $e->getMessage());
        }
        
        $_SESSION['user_roles'] = $roles;
    }

    private function redirectToDashboard() {
        // Check for primary role first
        $role = $_SESSION['user_role'];
        
        // If user has multiple roles, check if they have a preferred dashboard
        // Priority: admin > doctor > lab_technician > accountant > pharmacist > radiologist > nurse > receptionist
        $roles = $_SESSION['user_roles'] ?? [$role];
        
        // Determine best dashboard based on role priority
        if (in_array('admin', $roles)) {
            $this->redirect('admin/dashboard');
        } elseif (in_array('doctor', $roles)) {
            $this->redirect('doctor/dashboard');
        } elseif (in_array('lab_technician', $roles)) {
            $this->redirect('lab/dashboard');
        } elseif (in_array('accountant', $roles)) {
            $this->redirect('accountant/dashboard');
        } elseif (in_array('pharmacist', $roles)) {
            $this->redirect('pharmacist/dashboard');
        } elseif (in_array('radiologist', $roles)) {
            $this->redirect('radiologist/dashboard');
        } elseif (in_array('nurse', $roles)) {
            $this->redirect('ipd/dashboard');
        } elseif (in_array('receptionist', $roles)) {
            $this->redirect('receptionist/dashboard');
        } else {
            // Fallback to primary role
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
                case 'accountant':
                    $this->redirect('accountant/dashboard');
                    break;
                case 'pharmacist':
                    $this->redirect('pharmacist/dashboard');
                    break;
                case 'radiologist':
                    $this->redirect('radiologist/dashboard');
                    break;
                case 'nurse':
                    $this->redirect('ipd/dashboard');
                    break;
                default:
                    $this->redirect('auth/login');
            }
        }
    }
    
    // Switch active role for users with multiple roles
    public function switch_role() {
        $this->requireLogin();
        
        $new_role = $_POST['role'] ?? $_GET['role'] ?? '';
        $roles = $_SESSION['user_roles'] ?? [$_SESSION['user_role']];
        
        if (!empty($new_role) && in_array($new_role, $roles)) {
            $_SESSION['active_role'] = $new_role;
            
            // If called via AJAX, return JSON
            if (!empty($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Switched to ' . ucfirst($new_role) . ' role',
                    'active_role' => $new_role
                ]);
                exit;
            }
            
            // Otherwise redirect to appropriate dashboard
            switch ($new_role) {
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
                case 'accountant':
                    $this->redirect('accountant/dashboard');
                    break;
                case 'pharmacist':
                    $this->redirect('pharmacist/dashboard');
                    break;
                case 'nurse':
                    $this->redirect('ipd/dashboard');
                    break;
                case 'radiologist':
                    $this->redirect('radiologist/dashboard');
                    break;
                default:
                    $this->redirectToDashboard();
            }
        } else {
            $_SESSION['error'] = 'You do not have access to that role';
            
            // If called via AJAX, return JSON error
            if (!empty($_POST['ajax'])) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'You do not have access to that role']);
                exit;
            }
            
            $this->redirectToDashboard();
        }
    }
}
?>
