<?php
/**
 * Fix service payment records that have incorrect item_type and item_id
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo->beginTransaction();
    
    echo "=== Fixing Service Payment Records ===\n\n";
    
    // Find service payments with item_type = 'service' (should be 'service_order')
    $stmt = $pdo->prepare("
        SELECT 
            p.id as payment_id,
            p.patient_id,
            p.visit_id,
            p.item_id as current_item_id,
            p.item_type as current_item_type,
            p.amount,
            p.payment_date,
            pat.first_name,
            pat.last_name,
            pat.registration_number
        FROM payments p
        JOIN patients pat ON p.patient_id = pat.id
        WHERE p.payment_type IN ('minor_service', 'service')
        AND p.payment_status = 'paid'
        AND p.item_type = 'service'
        ORDER BY p.payment_date DESC
    ");
    
    $stmt->execute();
    $incorrect_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($incorrect_payments)) {
        echo "✓ No service payment records with incorrect item_type found.\n";
        $pdo->commit();
        exit(0);
    }
    
    echo "Found " . count($incorrect_payments) . " service payment records with incorrect item_type:\n\n";
    
    $fixed_count = 0;
    $skipped_count = 0;
    
    foreach ($incorrect_payments as $payment) {
        echo "Payment ID: {$payment['payment_id']}\n";
        echo "  Patient: {$payment['first_name']} {$payment['last_name']} ({$payment['registration_number']})\n";
        echo "  Amount: Tsh " . number_format($payment['amount'], 0) . "\n";
        echo "  Current item_type: '{$payment['current_item_type']}'\n";
        echo "  Current item_id: '{$payment['current_item_id']}' (this is service_id, not service_order_id)\n";
        
        // Find the corresponding service_order
        // The current item_id is the service_id, so we need to find the service_order
        // Try to match by patient, service, and a reasonable time window
        $stmt = $pdo->prepare("
            SELECT so.id as order_id, s.service_name, so.created_at, pv.visit_date
            FROM service_orders so
            JOIN services s ON so.service_id = s.id
            LEFT JOIN patient_visits pv ON so.visit_id = pv.id
            WHERE so.patient_id = ?
            AND so.service_id = ?
            AND so.created_at <= ?
            ORDER BY so.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([
            $payment['patient_id'],
            $payment['current_item_id'],
            $payment['payment_date']
        ]);
        $service_order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($service_order) {
            echo "  Found matching service_order: ID={$service_order['order_id']}, Service={$service_order['service_name']}\n";
            
            // Update the payment record
            $stmt = $pdo->prepare("
                UPDATE payments 
                SET item_id = ?, item_type = 'service_order'
                WHERE id = ?
            ");
            $stmt->execute([$service_order['order_id'], $payment['payment_id']]);
            
            echo "  ✓ Updated payment record: item_id={$service_order['order_id']}, item_type='service_order'\n";
            $fixed_count++;
        } else {
            echo "  ✗ Could not find matching service_order (skipping)\n";
            $skipped_count++;
        }
        echo "\n";
    }
    
    echo "=== Summary ===\n";
    echo "Fixed: {$fixed_count} payment record(s)\n";
    echo "Skipped: {$skipped_count} payment record(s)\n\n";
    
    if ($skipped_count == 0) {
        $pdo->commit();
        echo "✓ All changes committed to database.\n";
    } else {
        echo "⚠ Some records could not be fixed. ";
        $response = readline("Commit changes anyway? (yes/no): ");
        if (strtolower(trim($response)) === 'yes') {
            $pdo->commit();
            echo "✓ Changes committed.\n";
        } else {
            $pdo->rollBack();
            echo "✗ Changes rolled back.\n";
        }
    }
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Changes rolled back.\n";
    exit(1);
}
