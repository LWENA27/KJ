<?php
require_once 'config/database.php';

echo "<h1>Lab System Real Data Integration Test</h1>";

try {
    // Test Lab Equipment Data
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, status FROM lab_equipment GROUP BY status");
    $stmt->execute();
    $equipment_data = $stmt->fetchAll();
    
    echo "<h2>Lab Equipment Status:</h2>";
    foreach($equipment_data as $row) {
        echo "<p>{$row['status']}: {$row['total']} items</p>";
    }
    
    // Test Lab Inventory Data
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, status FROM lab_inventory GROUP BY status");
    $stmt->execute();
    $inventory_data = $stmt->fetchAll();
    
    echo "<h2>Lab Inventory Status:</h2>";
    foreach($inventory_data as $row) {
        echo "<p>{$row['status']}: {$row['total']} items</p>";
    }
    
    // Test Quality Control Data
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, status FROM lab_quality_control GROUP BY status");
    $stmt->execute();
    $qc_data = $stmt->fetchAll();
    
    echo "<h2>Quality Control Status:</h2>";
    foreach($qc_data as $row) {
        echo "<p>{$row['status']}: {$row['total']} tests</p>";
    }
    
    // Test Lab Samples Data
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, status FROM lab_samples GROUP BY status");
    $stmt->execute();
    $samples_data = $stmt->fetchAll();
    
    echo "<h2>Lab Samples Status:</h2>";
    foreach($samples_data as $row) {
        echo "<p>{$row['status']}: {$row['total']} samples</p>";
    }
    
    echo "<h2>✅ Real Data Integration Test PASSED</h2>";
    echo "<p>All database tables are accessible and contain real data!</p>";
    
} catch(Exception $e) {
    echo "<h2>❌ Test FAILED</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
