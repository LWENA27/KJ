<?php
include __DIR__ . '/../config/database.php';
$stmt = $pdo->prepare('DESCRIBE lab_results');
$stmt->execute();
$columns = $stmt->fetchAll();

echo "Column list for lab_results:\n";
foreach ($columns as $column) {
    echo $column['Field'] . ' - ' . $column['Type'] . "\n";
}
?>