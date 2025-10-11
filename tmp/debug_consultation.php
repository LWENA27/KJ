<?php
// Test script to debug consultation submission
session_start();
require_once '../config/database.php';
require_once '../includes/BaseController.php';

// Simulate doctor session
$_SESSION['user_id'] = 1; // Assuming doctor ID 1
$_SESSION['user_role'] = 'doctor';

$pdo = getDbConnection();

echo "<h2>Consultation Submission Debug Test</h2>";

// Test 1: Check if lab tests exist
echo "<h3>Test 1: Lab Tests Available</h3>";
$stmt = $pdo->query("SELECT id, test_name, price FROM lab_tests LIMIT 5");
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($tests);
echo "</pre>";

// Test 2: Check if patients exist
echo "<h3>Test 2: Patients Available</h3>";
$stmt = $pdo->query("SELECT id, first_name, last_name, registration_number FROM patients LIMIT 5");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($patients);
echo "</pre>";

// Test 3: Check if patient visits exist
echo "<h3>Test 3: Patient Visits</h3>";
$stmt = $pdo->query("SELECT id, patient_id, visit_date, status FROM patient_visits ORDER BY created_at DESC LIMIT 5");
$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($visits);
echo "</pre>";

// Test 4: Simulate POST data
echo "<h3>Test 4: Simulated POST Data</h3>";
$simulatedPost = [
    'patient_id' => $patients[0]['id'] ?? 1,
    'main_complaint' => 'Test headache',
    'on_examination' => 'Test examination findings',
    'preliminary_diagnosis' => 'Test preliminary diagnosis',
    'final_diagnosis' => 'Test final diagnosis',
    'selected_tests' => json_encode([1, 2]), // Test IDs
    'selected_medicines' => json_encode([])
];
echo "<pre>";
print_r($simulatedPost);
echo "</pre>";

// Test 5: Check if JSON decode works
echo "<h3>Test 5: JSON Decode Test</h3>";
$decoded_tests = json_decode($simulatedPost['selected_tests'], true);
echo "Decoded tests: ";
print_r($decoded_tests);
echo "<br>";
echo "Is array: " . (is_array($decoded_tests) ? 'YES' : 'NO');
echo "<br>";

// Test 6: Try to create a lab test order
echo "<h3>Test 6: Test Lab Order Creation</h3>";
try {
    $pdo->beginTransaction();
    
    $visit_id = $visits[0]['id'] ?? null;
    $patient_id = $patients[0]['id'] ?? null;
    
    if ($visit_id && $patient_id) {
        // Create a test consultation
        $stmt = $pdo->prepare("INSERT INTO consultations (visit_id, patient_id, doctor_id, main_complaint, status, created_at) VALUES (?, ?, ?, 'Debug test', 'pending', NOW())");
        $stmt->execute([$visit_id, $patient_id, 1]);
        $consultation_id = $pdo->lastInsertId();
        echo "Created consultation ID: $consultation_id<br>";
        
        // Try to create lab test order
        $stmt = $pdo->prepare("
            INSERT INTO lab_test_orders 
            (visit_id, patient_id, consultation_id, test_id, ordered_by, assigned_to, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$visit_id, $patient_id, $consultation_id, 1, 1, null]);
        echo "Created lab test order ID: " . $pdo->lastInsertId() . "<br>";
        
        echo "<strong style='color: green;'>SUCCESS! Lab test order creation works!</strong><br>";
    } else {
        echo "<strong style='color: red;'>ERROR: No visit or patient found</strong><br>";
    }
    
    $pdo->rollBack(); // Rollback so we don't actually insert test data
    echo "Transaction rolled back (test data not saved)<br>";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<strong style='color: red;'>ERROR: " . $e->getMessage() . "</strong><br>";
}

// Test 7: Check pending lab payments query
echo "<h3>Test 7: Pending Lab Payments Query</h3>";
try {
    $stmt = $pdo->query("
        SELECT DISTINCT
            lto.id as order_id,
            lto.patient_id,
            pv.id as visit_id,
            pt.first_name,
            pt.last_name,
            GROUP_CONCAT(DISTINCT lt.test_name SEPARATOR ', ') as test_names,
            SUM(lt.price) as total_amount
        FROM lab_test_orders lto
        JOIN patients pt ON lto.patient_id = pt.id
        JOIN patient_visits pv ON lto.visit_id = pv.id
        JOIN lab_tests lt ON lto.test_id = lt.id
        LEFT JOIN payments pay ON pay.visit_id = pv.id 
            AND pay.payment_type = 'lab_test' 
            AND pay.payment_status = 'paid'
        WHERE pay.id IS NULL
        GROUP BY lto.patient_id, pv.id
    ");
    $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Pending lab payments found: " . count($pending) . "<br>";
    if (count($pending) > 0) {
        echo "<pre>";
        print_r($pending);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<strong style='color: red;'>ERROR: " . $e->getMessage() . "</strong><br>";
}

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<ul>";
echo "<li>Lab tests available: " . count($tests) . "</li>";
echo "<li>Patients available: " . count($patients) . "</li>";
echo "<li>Visits available: " . count($visits) . "</li>";
echo "<li>Database connection: ✓ Working</li>";
echo "</ul>";
?>
