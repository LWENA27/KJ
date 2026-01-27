<?php
require_once __DIR__ . '/../config/database.php';

echo "=== Fixing Paid Radiology Payments with Blank item_id ===\n\n";

// Find paid radiology payments with blank item_id
$stmt = $pdo->query("
    SELECT id, visit_id, item_id, amount, payment_date
    FROM payments 
    WHERE payment_status = 'paid' 
    AND item_type = 'radiology_order' 
    AND (item_id IS NULL OR item_id = '')
    ORDER BY payment_date DESC
");
$blankPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($blankPayments) . " paid radiology payments with blank item_id\n\n";

foreach ($blankPayments as $payment) {
    echo "Processing Payment ID: {$payment['id']} (Amount: {$payment['amount']}, Date: {$payment['payment_date']})\n";
    
    // Find the radiology order(s) for this visit
    $stmt = $pdo->prepare("
        SELECT rto.id, rto.patient_id, rt.price
        FROM radiology_test_orders rto
        JOIN radiology_tests rt ON rto.test_id = rt.id
        WHERE rto.visit_id = ?
        ORDER BY rto.created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$payment['visit_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order) {
        echo "  Found radiology order ID: {$order['id']}, Price: {$order['price']}\n";
        echo "  Updating payment with item_id = {$order['id']}...\n";
        
        $updateStmt = $pdo->prepare("UPDATE payments SET item_id = ? WHERE id = ?");
        $updateStmt->execute([$order['id'], $payment['id']]);
        
        echo "  ✓ Updated!\n\n";
    } else {
        echo "  ✗ ERROR: No radiology order found for visit_id {$payment['visit_id']}\n\n";
    }
}

echo "\n=== Verification ===\n\n";

$stmt = $pdo->query("
    SELECT id, visit_id, item_id, amount, payment_status
    FROM payments 
    WHERE payment_status = 'paid' 
    AND item_type = 'radiology_order'
    AND (item_id IS NULL OR item_id = '')
");
$remaining = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($remaining) === 0) {
    echo "✓ SUCCESS: All paid radiology payments now have item_id values!\n";
} else {
    echo "✗ STILL FOUND " . count($remaining) . " payments with blank item_id:\n";
    foreach ($remaining as $p) {
        echo "  - Payment ID: {$p['id']}, item_id: " . ($p['item_id'] ?? 'NULL') . "\n";
    }
}
?>
