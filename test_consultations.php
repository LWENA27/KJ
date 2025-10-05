<?php
require_once 'config/database.php';

session_start();
$_SESSION['user_id'] = 3; // Dr. John Doe
$_SESSION['user_role'] = 'doctor';

$pdo = Database::getInstance()->getConnection();
$doctor_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT c.*, p.first_name, p.last_name, p.date_of_birth, p.phone,
           ws.consultation_registration_paid, ws.lab_tests_paid, ws.results_review_paid,
           COALESCE(c.main_complaint, c.symptoms) as main_complaint,
           COALESCE(c.final_diagnosis, c.preliminary_diagnosis, c.diagnosis) as final_diagnosis,
           c.preliminary_diagnosis,
           COALESCE(c.appointment_date, c.visit_date, c.created_at) as appointment_date
    FROM consultations c
    JOIN patients p ON c.patient_id = p.id
    LEFT JOIN workflow_status ws ON p.id = ws.patient_id
    WHERE c.doctor_id = ?
    ORDER BY COALESCE(c.appointment_date, c.visit_date, c.created_at) DESC
");
$stmt->execute([$doctor_id]);
$consultations = $stmt->fetchAll();

echo "<pre>";
echo "Total consultations: " . count($consultations) . "\n\n";
print_r($consultations);
echo "</pre>";
