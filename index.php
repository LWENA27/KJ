<?php
// Ensure we have a writable session directory inside the project to avoid
// permission issues with the system session directory (e.g. /var/lib/php/sessions)
$sessionDir = __DIR__ . '/storage/sessions';
if (!is_dir($sessionDir)) {
    // Try to create with restrictive permissions
    @mkdir($sessionDir, 0700, true);
}
if (is_dir($sessionDir) && is_writable($sessionDir)) {
    // Use project-local session files
    session_save_path($sessionDir);
} else {
    // Fall back: attempt to use a temp dir inside system tmp
    error_log("Warning: project session directory '$sessionDir' is not writable; attempting fallback");
    $tmp = sys_get_temp_dir() . '/php-sessions';
    if (!is_dir($tmp)) {
        @mkdir($tmp, 0700, true);
    }
    if (is_dir($tmp) && is_writable($tmp)) {
        session_save_path($tmp);
    } else {
        // Leave PHP to use the configured session.save_path; this may still trigger warnings
        error_log("Warning: fallback temp session directory '$tmp' is not writable; using system session.save_path");
    }
}

session_start();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include logger
require_once __DIR__ . '/includes/logger.php';

// Include configuration
require_once __DIR__ . '/config/database.php';

// Include helpers (currency formatting, etc.)
require_once __DIR__ . '/includes/helpers.php';

// Determine base path from the request (works with symlinks or aliases)
if (!defined('BASE_PATH')) {
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    define('BASE_PATH', $scriptDir === '' ? '' : $scriptDir);
}

// Parse the URL
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remove base path prefix (supports any folder the app is in)
if (strpos($path, BASE_PATH) === 0) {
    $path = substr($path, strlen(BASE_PATH));
}

// Remove leading slash
$path = ltrim($path, '/');

// Split path into segments
$segments = explode('/', $path);

// Default routing
if (empty($path) || $path === '') {
    // Redirect to login if not authenticated
    if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/auth/login');
        exit;
    }
    
    // Redirect based on user role
    $role = $_SESSION['user_role'] ?? '';
    switch($role) {
        case 'admin':
        header('Location: ' . BASE_PATH . '/admin/dashboard');
            break;
        case 'doctor':
        header('Location: ' . BASE_PATH . '/doctor/dashboard');
            break;
        case 'lab_technician':
        header('Location: ' . BASE_PATH . '/lab/dashboard');
            break;
        case 'receptionist':
        header('Location: ' . BASE_PATH . '/receptionist/dashboard');
            break;
        default:
        header('Location: ' . BASE_PATH . '/auth/login');
    }
    exit;
}

// Extract controller and action
$controller = $segments[0] ?? 'auth';
$action = $segments[1] ?? 'login';

// Validate controller
$validControllers = ['auth', 'admin', 'doctor', 'lab', 'receptionist', 'patienthistory'];
if (!in_array($controller, $validControllers)) {
    http_response_code(404);
    echo "Controller not found: " . htmlspecialchars($controller);
    exit;
}

// Build controller file path
$controllerFile = __DIR__ . '/controllers/' . ucfirst($controller) . 'Controller.php';

// Check if controller file exists
if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "Controller file not found: " . htmlspecialchars($controllerFile);
    exit;
}

// Include the controller
require_once $controllerFile;

// Create controller instance
$controllerClass = ucfirst($controller) . 'Controller';
if (!class_exists($controllerClass)) {
    http_response_code(404);
    echo "Controller class not found: " . htmlspecialchars($controllerClass);
    exit;
}

try {
    $controllerInstance = new $controllerClass();

    // Check if action method exists
    if (!method_exists($controllerInstance, $action)) {
        http_response_code(404);
        echo "Action not found: " . htmlspecialchars($action);
        exit;
    }

    // Collect any additional URL segments as parameters for the action
    $params = array_slice($segments, 2);

    // Call the action with parameters
    call_user_func_array([$controllerInstance, $action], $params);

} catch (Throwable $e) { // Catch all errors and exceptions
    Logger::error("Error in controller: " . $e->getMessage(), [
        'controller' => $controller,
        'action' => $action,
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    error_log("Error in controller: " . $e->getMessage());
    http_response_code(500);
    echo "Internal server error";
}
?>
