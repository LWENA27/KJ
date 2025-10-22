<?php
// Test script to show the doctor's lab results
include __DIR__ . '/../config/database.php';

// Simulate what happens in DoctorController::lab_results()
$doctor_id = 1; // Using a default doctor ID

// Fetch recent lab results for patients that belong to this doctor
$stmt = $pdo->prepare("
    SELECT lr.*, t.test_name as test_name, p.first_name, p.last_name, pv.visit_date, 
           lr.result_value, lr.result_text, lto.status, lr.completed_at as created_at
    FROM lab_results lr
    JOIN lab_test_orders lto ON lr.order_id = lto.id
    JOIN lab_tests t ON lr.test_id = t.id
    JOIN consultations c ON lto.consultation_id = c.id
    JOIN patients p ON c.patient_id = p.id
    LEFT JOIN patient_visits pv ON c.visit_id = pv.id
    WHERE c.doctor_id = ?
    ORDER BY lr.completed_at DESC
    LIMIT 200
");
$stmt->execute([$doctor_id]);
$results = $stmt->fetchAll();

echo "Lab results for doctor's dashboard:\n";
print_r($results);
?>