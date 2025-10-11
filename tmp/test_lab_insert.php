<?php
include __DIR__ . '/../config/database.php';

try {
    // Get a sample test order ID
    $stmt = $pdo->prepare("SELECT id, patient_id, test_id FROM lab_test_orders LIMIT 1");
    $stmt->execute();
    $order = $stmt->fetch();
    
    if (!$order) {
        echo "No test orders found. Creating a test order...\n";
        // Insert a test lab order if none exists
        $stmt = $pdo->prepare("
            INSERT INTO lab_test_orders (visit_id, patient_id, test_id, ordered_by, status) 
            VALUES (1, 1, 1, 1, 'pending')
        ");
        $stmt->execute();
        $order_id = $pdo->lastInsertId();
        echo "Created test order with ID: $order_id\n";
        
        // Get the order details
        $stmt = $pdo->prepare("SELECT id, patient_id, test_id FROM lab_test_orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();
    } else {
        echo "Found test order with ID: " . $order['id'] . "\n";
    }
    
    // Now insert a lab result
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("
        INSERT INTO lab_results 
        (order_id, patient_id, test_id, technician_id, result_value, result_text, 
         result_unit, is_normal, completed_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $params = [
        $order['id'],
        $order['patient_id'],
        $order['test_id'],
        1, // technician_id
        '120', // result_value
        'Normal test result', // result_text
        'mg/dL', // result_unit
        1, // is_normal
        date('Y-m-d H:i:s') // completed_at
    ];
    
    echo "Executing SQL with parameters:\n";
    print_r($params);
    
    $stmt->execute($params);
    $result_id = $pdo->lastInsertId();
    
    // Update lab_test_orders status to completed
    $stmt = $pdo->prepare("
        UPDATE lab_test_orders 
        SET status = 'completed', 
            completed_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$order['id']]);
    
    $pdo->commit();
    
    echo "Successfully inserted lab result with ID: $result_id\n";
    
    // Verify the insertion
    $stmt = $pdo->prepare("SELECT * FROM lab_results WHERE id = ?");
    $stmt->execute([$result_id]);
    echo "Inserted lab result data:\n";
    print_r($stmt->fetch());
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>