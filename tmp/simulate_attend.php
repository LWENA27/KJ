<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/DoctorController.php';
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'doctor';
// Ensure a CSRF token exists in session for controller validation
if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// Simulate sending to lab for patient 28
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [];
$_POST['csrf_token'] = $_SESSION['csrf_token'];
$_POST['patient_id'] = 28;
$_POST['selectedTests'] = json_encode([1,2,3]);
$ctrl = new DoctorController();
$ctrl->send_to_lab();

// Simulate sending to medicine for patient 28
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [];
$_POST['csrf_token'] = $_SESSION['csrf_token'];
$_POST['patient_id'] = 28;
$_POST['selectedMedicines'] = json_encode([['id'=>1,'quantity'=>1,'instructions'=>'Take after food']]);
$ctrl = new DoctorController();
$ctrl->send_to_medicine();

echo "Simulated attend script finished\n";
