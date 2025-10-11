<?php
include __DIR__ . '/../config/database.php';
$stmt = $pdo->query("DESCRIBE lab_test_orders");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Just get the column names
echo "Column names in lab_test_orders:\n";
print_r($columns);
?>