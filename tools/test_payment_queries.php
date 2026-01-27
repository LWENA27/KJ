<?php
require_once __DIR__ . '/../config/database.php';

echo "=== Testing Radiology Payment Query ===\n\n";

$sql = <<<'SQL'
SELECT 
    rto.id as order_id,
    rto.visit_id,
    rto.patient_id,
    p.first_name,
    p.last_name,
    p.registration_number,
    pv.visit_date,
    rt.test_name,
    rt.price as amount,
    COALESCE(pay.amount, 0) as paid_amount,
    (rt.price - COALESCE(pay.amount, 0)) as remaining_amount_to_pay,
    rto.created_at,
    pay.id as payment_id,
    pay.payment_status
FROM radiology_test_orders rto
JOIN radiology_tests rt ON rto.test_id = rt.id
JOIN patients p ON rto.patient_id = p.id
LEFT JOIN patient_visits pv ON rto.visit_id = pv.id
LEFT JOIN payments pay ON pay.item_type = 'radiology_order' AND pay.item_id = rto.id AND pay.payment_status = 'paid'
WHERE rto.status IN ('pending', 'scheduled')
AND (pay.id IS NULL OR pay.payment_status = 'pending')
ORDER BY pv.visit_date DESC, rto.created_at DESC
SQL;

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Radiology orders found: " . count($results) . "\n\n";
    
    foreach ($results as $r) {
        echo "Patient: " . $r['first_name'] . ' ' . $r['last_name'] . " ({$r['registration_number']})\n";
        echo "  Order ID: " . $r['order_id'] . "\n";
        echo "  Test: " . $r['test_name'] . "\n";
        echo "  Amount: " . $r['amount'] . ", Paid: " . $r['paid_amount'] . ", Remaining: " . $r['remaining_amount_to_pay'] . "\n";
        echo "  Payment ID: " . ($r['payment_id'] ?? 'null') . ", Status: " . ($r['payment_status'] ?? 'null') . "\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n\n=== Testing Ward Payment Query ===\n\n";

$sql2 = <<<'SQL'
SELECT 
    ia.id as admission_id,
    ia.admission_number,
    ia.visit_id,
    ia.patient_id,
    p.first_name,
    p.last_name,
    p.registration_number,
    ia.admission_datetime as admission_date,
    w.ward_name,
    b.daily_rate * DATEDIFF(COALESCE(ia.discharge_datetime, NOW()), ia.admission_datetime) as amount,
    COALESCE(pay.amount, 0) as paid_amount,
    (b.daily_rate * DATEDIFF(COALESCE(ia.discharge_datetime, NOW()), ia.admission_datetime) - COALESCE(pay.amount, 0)) as remaining_amount_to_pay,
    pay.id as payment_id,
    pay.payment_status
FROM ipd_admissions ia
JOIN patients p ON ia.patient_id = p.id
LEFT JOIN ipd_beds b ON ia.bed_id = b.id
LEFT JOIN ipd_wards w ON b.ward_id = w.id
LEFT JOIN payments pay ON pay.item_type = 'service_order' AND pay.item_id = ia.id AND pay.payment_status = 'paid'
WHERE ia.status = 'active'
AND (pay.id IS NULL OR pay.payment_status = 'pending')
ORDER BY ia.admission_datetime DESC
SQL;

try {
    $stmt = $pdo->prepare($sql2);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Ward admissions found: " . count($results) . "\n\n";
    
    foreach ($results as $r) {
        echo "Patient: " . $r['first_name'] . ' ' . $r['last_name'] . " ({$r['registration_number']})\n";
        echo "  Admission ID: " . $r['admission_id'] . " (" . $r['admission_number'] . ")\n";
        echo "  Visit ID: " . $r['visit_id'] . "\n";
        echo "  Ward: " . $r['ward_name'] . "\n";
        echo "  Amount: " . $r['amount'] . ", Paid: " . $r['paid_amount'] . ", Remaining: " . $r['remaining_amount_to_pay'] . "\n";
        echo "  Payment ID: " . ($r['payment_id'] ?? 'null') . ", Status: " . ($r['payment_status'] ?? 'null') . "\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n\n=== All Pending Payments (by status) ===\n\n";

$sqlAll = <<<'SQL'
SELECT 
    id, 
    visit_id, 
    patient_id, 
    payment_type, 
    item_type, 
    item_id, 
    amount, 
    payment_status, 
    payment_date,
    collected_by
FROM payments
WHERE payment_status = 'pending'
ORDER BY payment_date DESC
LIMIT 10
SQL;

try {
    $stmt = $pdo->prepare($sqlAll);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Pending payments in database: " . count($results) . "\n\n";
    
    foreach ($results as $r) {
        echo "ID: {$r['id']}, Type: {$r['payment_type']}, Item Type: {$r['item_type']}, Item ID: {$r['item_id']}, Amount: {$r['amount']}, Status: {$r['payment_status']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n\n=== Paid Radiology Payments ===\n\n";

$sqlPaid = <<<'SQL'
SELECT 
    id, 
    visit_id, 
    patient_id, 
    payment_type, 
    item_type, 
    item_id, 
    amount, 
    payment_status, 
    payment_date
FROM payments
WHERE payment_status = 'paid' AND item_type = 'radiology_order'
ORDER BY payment_date DESC
LIMIT 10
SQL;

try {
    $stmt = $pdo->prepare($sqlPaid);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Paid radiology payments: " . count($results) . "\n\n";
    
    foreach ($results as $r) {
        echo "ID: {$r['id']}, Item ID (rto.id): {$r['item_id']}, Amount: {$r['amount']}, Paid on: {$r['payment_date']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
