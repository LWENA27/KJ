<?php
/**
 * Script to clean up pending service payment records
 * These records should not exist - only paid payment records should be in the payments table
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo->beginTransaction();
    
    echo "=== Cleaning Up Pending Service Payment Records ===\n\n";
    
    // Find all pending service payments
    $stmt = $pdo->prepare("
        SELECT 
            p.id as payment_id,
            p.patient_id,
            p.visit_id,
            p.item_id,
            p.item_type,
            p.amount,
            p.payment_date,
            pat.first_name,
            pat.last_name,
            pat.registration_number
        FROM payments p
        JOIN patients pat ON p.patient_id = pat.id
        WHERE p.payment_type IN ('minor_service', 'service')
        AND p.payment_status = 'pending'
        ORDER BY p.payment_date DESC
    ");
    
    $stmt->execute();
    $pending_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($pending_payments)) {
        echo "✓ No pending service payment records found to clean up.\n";
        $pdo->commit();
        exit(0);
    }
    
    echo "Found " . count($pending_payments) . " pending service payment records to remove:\n\n";
    
    foreach ($pending_payments as $payment) {
        echo "Payment ID: {$payment['payment_id']}\n";
        echo "  Patient: {$payment['first_name']} {$payment['last_name']} ({$payment['registration_number']})\n";
        echo "  Amount: Tsh " . number_format($payment['amount'], 0) . "\n";
        echo "  Created: {$payment['payment_date']}\n";
        echo "  Item Type: {$payment['item_type']}\n";
        echo "  Item ID: {$payment['item_id']}\n";
        echo "\n";
    }
    
    // Delete all pending service payments
    $payment_ids = array_column($pending_payments, 'payment_id');
    $placeholders = implode(',', array_fill(0, count($payment_ids), '?'));
    
    $stmt = $pdo->prepare("DELETE FROM payments WHERE id IN ($placeholders)");
    $stmt->execute($payment_ids);
    $deleted_count = $stmt->rowCount();
    
    echo "=== Summary ===\n";
    echo "✓ Deleted {$deleted_count} pending service payment record(s)\n\n";
    
    echo "Note: Service orders remain intact. Payments will be created when the receptionist\n";
    echo "      records the actual payment from the patient.\n\n";
    
    $pdo->commit();
    echo "✓ Changes committed to database.\n";
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Changes rolled back.\n";
    exit(1);
}
