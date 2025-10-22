<?php
include __DIR__ . '/../config/database.php';
$stmt = $pdo->prepare('DESCRIBE lab_test_orders');
$stmt->execute();
print_r($stmt->fetchAll());
?>