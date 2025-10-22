<?php
include __DIR__ . '/../config/database.php';
$stmt = $pdo->prepare('DESCRIBE lab_results');
$stmt->execute();
print_r($stmt->fetchAll());
?>