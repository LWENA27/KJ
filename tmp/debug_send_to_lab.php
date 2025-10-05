<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/DoctorController.php';
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'doctor';
if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [];
$_POST['csrf_token'] = $_SESSION['csrf_token'];
$_POST['patient_id'] = 28;
$_POST['selectedTests'] = json_encode([1,2,3]);

try {
    $ctrl = new DoctorController();
    $ctrl->send_to_lab();
    echo "send_to_lab completed\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

