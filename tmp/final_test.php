<?php
include __DIR__ . '/../config/database.php';

function debug_log($message) {
    echo date('Y-m-d H:i:s') . " - $message\n";
}

try {
    debug_log("Starting final test");
    
    // Get order details
    $test_order_id = 1; // We know this exists from previous tests
    debug_log("Using test order ID: $test_order_id");
    
    $stmt = $pdo->prepare("
        SELECT patient_id, test_id, visit_id FROM lab_test_orders WHERE id = ?
    ");
    $stmt->execute([$test_order_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        throw new Exception("Test order not found");
    }
    
    debug_log("Found order: " . print_r($order, true));
    
    // Simulated form data
    $result_value = '140';
    $unit = 'mg/dL';
    $result_status = 'normal';
    $result_notes = 'Final test result notes';
    $completion_time = date('Y-m-d H:i:s');
    $technician_id = 1;
    
    // Calculate is_normal based on result_status
    $is_normal = ($result_status === 'normal') ? 1 : 0;
    
    debug_log("Starting transaction");
    $pdo->beginTransaction();
    
    // Update lab test order status to completed
    debug_log("Updating lab_test_orders status");
    $stmt = $pdo->prepare("
        UPDATE lab_test_orders 
        SET status = 'completed', 
            updated_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$test_order_id]);
    $rows_updated = $stmt->rowCount();
    debug_log("Updated $rows_updated lab_test_orders rows");
    
    // Insert or update lab_results record
    $sql = "
        INSERT INTO lab_results 
        (order_id, patient_id, test_id, technician_id, result_value, result_text, 
         result_unit, is_normal, completed_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    debug_log("Preparing SQL: $sql");
    $stmt = $pdo->prepare($sql);
    
    $params = [
        $test_order_id,
        $order['patient_id'],
        $order['test_id'],
        $technician_id,
        $result_value,
        $result_notes,
        $unit,
        $is_normal,
        $completion_time
    ];
    
    debug_log("Parameters: " . print_r($params, true));
    $stmt->execute($params);
    $inserted_id = $pdo->lastInsertId();
    debug_log("Inserted lab_results row with ID: $inserted_id");
    
    $pdo->commit();
    debug_log("Transaction committed successfully");
    
    // Verify the insertion
    $stmt = $pdo->prepare("SELECT * FROM lab_results WHERE id = ?");
    $stmt->execute([$inserted_id]);
    $result = $stmt->fetch();
    debug_log("Inserted data: " . print_r($result, true));
    
    // Count how many records now exist in lab_results
    $stmt = $pdo->query("SELECT COUNT(*) FROM lab_results");
    $count = $stmt->fetchColumn();
    debug_log("Total records in lab_results table: $count");
    
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        debug_log("Rolling back transaction due to error");
        $pdo->rollBack();
    }
    debug_log("Error: " . $e->getMessage());
    debug_log("Stack trace: " . $e->getTraceAsString());
}
?>