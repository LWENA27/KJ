<?php
include __DIR__ . '/../config/database.php';
$stmt = $pdo->query("SELECT DATABASE()");
$currentDB = $stmt->fetchColumn();
echo "Current database: " . $currentDB . "\n";

// List all tables in the database
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "\nTables in this database:\n";
print_r($tables);

// Check the definition of lab_results
echo "\nTrying to insert a simple record into lab_results:\n";
try {
    $pdo->beginTransaction();
    
    // Check if there's a completed_at column
    $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'lab_results' AND column_name = 'completed_at'");
    $hasCompletedAt = (int)$stmt->fetchColumn();
    echo "Has completed_at column: " . ($hasCompletedAt ? "Yes" : "No") . "\n";
    
    // Try a simple insert
    if ($hasCompletedAt) {
        $sql = "INSERT INTO lab_results (order_id, patient_id, test_id, technician_id, result_value, completed_at) VALUES (1, 1, 1, 1, 'Test', NOW())";
    } else {
        $sql = "INSERT INTO lab_results (order_id, patient_id, test_id, technician_id, result_value) VALUES (1, 1, 1, 1, 'Test')";
    }
    
    echo "SQL: " . $sql . "\n";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $pdo->commit();
    echo "Insert successful!\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
}
?>