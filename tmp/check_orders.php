<?php
require_once __DIR__ . '/../config/database.php';

try {
    
    // Check lab test orders
    $count = $pdo->query('SELECT COUNT(*) FROM lab_test_orders')->fetchColumn();
    echo "Total lab test orders: $count\n\n";
    
    if ($count > 0) {
        $stmt = $pdo->query('
            SELECT lto.id, lto.patient_id, lto.visit_id, lto.test_id, 
                   lt.test_name, lto.created_at,
                   p.first_name, p.last_name
            FROM lab_test_orders lto
            JOIN lab_tests lt ON lto.test_id = lt.id
            JOIN patients p ON lto.patient_id = p.id
            ORDER BY lto.id DESC LIMIT 5
        ');
        
        echo "Recent Lab Test Orders:\n";
        echo str_repeat("-", 80) . "\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "Order ID: {$row['id']}\n";
            echo "Patient: {$row['first_name']} {$row['last_name']} (ID: {$row['patient_id']})\n";
            echo "Visit ID: {$row['visit_id']}\n";
            echo "Test: {$row['test_name']} (ID: {$row['test_id']})\n";
            echo "Created: {$row['created_at']}\n";
            echo str_repeat("-", 80) . "\n";
        }
    } else {
        echo "No lab test orders found!\n";
    }
    
    // Check pending payments query (what receptionist sees)
    echo "\n\nPending Lab Payments (Receptionist View):\n";
    echo str_repeat("=", 80) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            p.id as patient_id,
            p.first_name,
            p.last_name,
            p.registration_number,
            v.id as visit_id,
            GROUP_CONCAT(lt.test_name SEPARATOR ', ') as tests,
            SUM(lt.price) as total_amount,
            COUNT(lto.id) as test_count
        FROM lab_test_orders lto
        JOIN patients p ON lto.patient_id = p.id
        JOIN patient_visits v ON lto.visit_id = v.id
        JOIN lab_tests lt ON lto.test_id = lt.id
        LEFT JOIN payments pay ON pay.payment_type = 'lab_test' 
            AND pay.item_id = lto.id
            AND pay.item_type = 'lab_order'
        WHERE pay.id IS NULL
        GROUP BY p.id, v.id
        ORDER BY lto.created_at DESC
    ");
    
    $pendingCount = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pendingCount++;
        echo "Patient: {$row['first_name']} {$row['last_name']} (Reg: {$row['registration_number']})\n";
        echo "Tests: {$row['tests']}\n";
        echo "Total Amount: Tsh " . number_format($row['total_amount']) . "\n";
        echo "Test Count: {$row['test_count']}\n";
        echo str_repeat("-", 80) . "\n";
    }
    
    if ($pendingCount == 0) {
        echo "No pending lab payments found!\n";
    } else {
        echo "\nTotal pending lab payments: $pendingCount\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
