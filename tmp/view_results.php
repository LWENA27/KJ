<?php
include __DIR__ . '/../config/database.php';
$stmt = $pdo->query("SELECT * FROM lab_results");
$results = $stmt->fetchAll();
echo "All lab_results records:\n";
print_r($results);
?>