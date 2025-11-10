<?php
/**
 * Script to identify and remove duplicate service payment records
 * This script will:
 * 1. Find duplicate payment records for service orders
 * 2. Keep only the first payment record for each service order
 * 3. Delete the duplicate records
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo->beginTransaction();
    
    echo "=== Checking for Duplicate Service Payment Records ===\n\n";
    
    // Find duplicate payment records for service orders
    $stmt = $pdo->prepare("
        SELECT 
            item_id,
            item_type,
            patient_id,
            visit_id,
            COUNT(*) as duplicate_count,
            GROUP_CONCAT(id ORDER BY payment_date ASC) as payment_ids,
            MIN(payment_date) as first_payment_date,
            SUM(amount) as total_amount
        FROM payments
        WHERE payment_type = 'service' 
        AND item_type = 'service_order'
        AND payment_status = 'paid'
        GROUP BY item_id, item_type, patient_id, visit_id
        HAVING duplicate_count > 1
        ORDER BY item_id
    ");
    
    $stmt->execute();
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($duplicates)) {
        echo "✓ No duplicate service payment records found.\n";
        $pdo->commit();
        exit(0);
    }
    
    echo "Found " . count($duplicates) . " service orders with duplicate payment records:\n\n";
    
    $total_duplicates_removed = 0;
    
    foreach ($duplicates as $duplicate) {
        $payment_ids = explode(',', $duplicate['payment_ids']);
        $first_payment_id = $payment_ids[0]; // Keep the first payment
        $ids_to_delete = array_slice($payment_ids, 1); // Delete the rest
        
        // Get service order details
        $stmt = $pdo->prepare("
            SELECT s.service_name, p.first_name, p.last_name, p.registration_number
            FROM service_orders so
            JOIN services s ON so.service_id = s.id
            JOIN patients p ON so.patient_id = p.id
            WHERE so.id = ?
        ");
        $stmt->execute([$duplicate['item_id']]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Service Order ID: {$duplicate['item_id']}\n";
        echo "  Patient: {$service['first_name']} {$service['last_name']} ({$service['registration_number']})\n";
        echo "  Service: {$service['service_name']}\n";
        echo "  Duplicate payments: {$duplicate['duplicate_count']}\n";
        echo "  Total amount paid: Tsh " . number_format($duplicate['total_amount'], 0) . "\n";
        echo "  Keeping payment ID: {$first_payment_id}\n";
        echo "  Deleting payment IDs: " . implode(', ', $ids_to_delete) . "\n";
        
        // Delete duplicate payment records
        if (!empty($ids_to_delete)) {
            $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
            $stmt = $pdo->prepare("DELETE FROM payments WHERE id IN ($placeholders)");
            $stmt->execute($ids_to_delete);
            $deleted_count = $stmt->rowCount();
            $total_duplicates_removed += $deleted_count;
            echo "  ✓ Deleted {$deleted_count} duplicate payment record(s)\n";
        }
        echo "\n";
    }
    
    echo "=== Summary ===\n";
    echo "Total service orders with duplicates: " . count($duplicates) . "\n";
    echo "Total duplicate payment records removed: {$total_duplicates_removed}\n\n";
    
    // Verify the fix
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as remaining_duplicates
        FROM (
            SELECT item_id, item_type, patient_id, visit_id, COUNT(*) as cnt
            FROM payments
            WHERE payment_type = 'service' 
            AND item_type = 'service_order'
            AND payment_status = 'paid'
            GROUP BY item_id, item_type, patient_id, visit_id
            HAVING cnt > 1
        ) as dupes
    ");
    $stmt->execute();
    $remaining = $stmt->fetchColumn();
    
    if ($remaining == 0) {
        echo "✓ All duplicate service payment records have been removed successfully!\n";
        $pdo->commit();
        echo "\n✓ Changes committed to database.\n";
    } else {
        echo "⚠ Warning: {$remaining} duplicate records still remain. Rolling back...\n";
        $pdo->rollBack();
        echo "✗ Changes rolled back.\n";
    }
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Changes rolled back.\n";
    exit(1);
}
