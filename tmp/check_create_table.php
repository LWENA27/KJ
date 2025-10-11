<?php
include __DIR__ . '/../config/database.php';
$stmt = $pdo->prepare('SHOW CREATE TABLE lab_results');
$stmt->execute();
print_r($stmt->fetchAll());
?>