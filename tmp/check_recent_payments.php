<?php
/**
 * Check the most recent payment records
 */

require_once __DIR__ . '/../config/database.php';

try {
    echo "=== Recent Payment Records ===\n\n";
    
    // Get the most recent 10 payments
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.patient_id,
            p.visit_id,
            p.payment_type,
            p.item_id,
            p.item_type,
            p.amount,
            p.payment_status,
            p.payment_date,
            pat.first_name,
            pat.last_name,
            pat.registration_number
        FROM payments p
        JOIN patients pat ON p.patient_id = pat.id
        ORDER BY p.payment_date DESC
        LIMIT 10
    ");
    
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($payments as $payment) {
        echo "Payment ID: {$payment['id']}\n";
        echo "  Patient: {$payment['first_name']} {$payment['last_name']} ({$payment['registration_number']})\n";
        echo "  Type: {$payment['payment_type']}\n";
        echo "  Item Type: '{$payment['item_type']}'\n";
        echo "  Item ID: '{$payment['item_id']}'\n";
        echo "  Amount: Tsh " . number_format($payment['amount'], 0) . "\n";
        echo "  Status: {$payment['payment_status']}\n";
        echo "  Date: {$payment['payment_date']}\n";
        
        // If it's a service payment, check the service order
        if ($payment['payment_type'] === 'service' && $payment['item_id']) {
            $stmt2 = $pdo->prepare("
                SELECT so.id, s.service_name, so.status
                FROM service_orders so
                JOIN services s ON so.service_id = s.id
                WHERE so.id = ?
            ");
            $stmt2->execute([$payment['item_id']]);
            $order = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($order) {
                echo "  ✓ Linked to Service Order {$order['id']}: {$order['service_name']} (Status: {$order['status']})\n";
            } else {
                echo "  ✗ Service Order ID {$payment['item_id']} NOT FOUND\n";
            }
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
