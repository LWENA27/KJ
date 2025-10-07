<?php
// Debug script for lab samples
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Simulate lab technician session for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'lab_technician';
$_SESSION['logged_in'] = true;

require_once 'config/database.php';
require_once 'controllers/LabController.php';

try {
    echo "Testing LabController samples method...\n";
    
    $controller = new LabController();
    echo "Controller created successfully\n";
    
    // Test database queries individually
    $pdo = new PDO("mysql:host=localhost;dbname=zahanati", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Testing sample statistics query...\n";
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_samples,
            COUNT(CASE WHEN status = 'collected' THEN 1 END) as collected,
            COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
            COUNT(CASE WHEN DATE(collection_date) = CURDATE() THEN 1 END) as today_samples
        FROM lab_samples
    ");
    $stmt->execute();
    $stats = $stmt->fetch();
    print_r($stats);
    
    echo "Testing samples to be collected query...\n";
    $stmt = $pdo->prepare("
        SELECT lr.*, t.test_name as test_name, t.test_code as test_code, t.category_id as category, p.first_name, p.last_name, c.appointment_date,
               ws.consultation_registration_paid, ws.lab_tests_paid
        FROM lab_results lr
        JOIN lab_tests t ON lr.test_id = t.id
        JOIN consultations c ON lr.consultation_id = c.id
        JOIN patients p ON c.patient_id = p.id
        LEFT JOIN workflow_status ws ON p.id = ws.patient_id
        WHERE lr.technician_id = ? AND lr.status IN ('pending', 'sample_collected')
        ORDER BY lr.status ASC, lr.created_at ASC
        LIMIT 5
    ");
    $stmt->execute([1]);
    $samples = $stmt->fetchAll();
    echo "Found " . count($samples) . " samples\n";
    
    echo "\nAll queries executed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
