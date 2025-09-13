<?php
// Test file to check lab dashboard without authentication
session_start();

// Temporarily set session for testing
$_SESSION['user_id'] = 10; // zanura's ID
$_SESSION['user_role'] = 'lab_technician';
$_SESSION['username'] = 'zanura';

// Include the main files
require_once 'config/database.php';
require_once 'controllers/LabController.php';

try {
    $controller = new LabController();
    $controller->dashboard();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
