<?php
/**
 * Script to check payment data for the patients mentioned in the issue
 */

require_once __DIR__ . '/../config/database.php';

try {
    echo "=== Checking Payment Data ===\n\n";
    
    // Check for the patients mentioned
    $patients = ['el becerril', 'john magufuri', 'ester lupolo', 'lwena samson'];
    
    foreach ($patients as $patient_name) {
        $parts = explode(' ', $patient_name);
        $first_name = $parts[0];
        $last_name = $parts[1] ?? '';
        
        echo "--- Patient: " . ucwords($patient_name) . " ---\n";
        
        // Get patient ID
        $stmt = $pdo->prepare("
            SELECT id, registration_number, first_name, last_name
            FROM patients
            WHERE LOWER(first_name) LIKE LOWER(?) 
            AND LOWER(last_name) LIKE LOWER(?)
            LIMIT 1
        ");
        $stmt->execute([$first_name . '%', $last_name . '%']);
        $patient = $stmt->fetch();
        
        if (!$patient) {
            echo "  Patient not found\n\n";
            continue;
        }
        
        echo "  ID: {$patient['id']}\n";
        echo "  Registration: {$patient['registration_number']}\n\n";
        
        // Check all payments for this patient
        $stmt = $pdo->prepare("
            SELECT 
                p.id as payment_id,
                p.payment_type,
                p.item_type,
                p.item_id,
                p.amount,
                p.payment_status,
                p.payment_date,
                pv.visit_date
            FROM payments p
            LEFT JOIN patient_visits pv ON p.visit_id = pv.id
            WHERE p.patient_id = ?
            ORDER BY p.payment_date DESC
        ");
        $stmt->execute([$patient['id']]);
        $payments = $stmt->fetchAll();
        
        echo "  Total Payments: " . count($payments) . "\n\n";
        
        foreach ($payments as $payment) {
            echo "  Payment ID: {$payment['payment_id']}\n";
            echo "    Type: {$payment['payment_type']}\n";
            echo "    Item Type: {$payment['item_type']}\n";
            echo "    Item ID: {$payment['item_id']}\n";
            echo "    Amount: Tsh " . number_format($payment['amount'], 0) . "\n";
            echo "    Status: {$payment['payment_status']}\n";
            echo "    Payment Date: {$payment['payment_date']}\n";
            echo "    Visit Date: {$payment['visit_date']}\n";
            echo "\n";
        }
        
        // Check service orders for this patient
        $stmt = $pdo->prepare("
            SELECT 
                so.id as order_id,
                so.service_id,
                s.service_name,
                s.price,
                so.created_at,
                pv.visit_date,
                (SELECT COUNT(*) FROM payments 
                 WHERE item_id = so.id 
                 AND item_type = 'service_order' 
                 AND payment_status = 'paid') as paid_count
            FROM service_orders so
            JOIN services s ON so.service_id = s.id
            LEFT JOIN patient_visits pv ON so.visit_id = pv.id
            WHERE so.patient_id = ?
            ORDER BY so.created_at DESC
        ");
        $stmt->execute([$patient['id']]);
        $service_orders = $stmt->fetchAll();
        
        if (!empty($service_orders)) {
            echo "  Service Orders: " . count($service_orders) . "\n\n";
            
            foreach ($service_orders as $order) {
                echo "  Order ID: {$order['order_id']}\n";
                echo "    Service: {$order['service_name']}\n";
                echo "    Price: Tsh " . number_format($order['price'], 0) . "\n";
                echo "    Paid Count: {$order['paid_count']}\n";
                echo "    Created: {$order['created_at']}\n";
                echo "    Visit Date: {$order['visit_date']}\n";
                echo "\n";
            }
        } else {
            echo "  No service orders found\n\n";
        }
        
        echo "----------------------------------------\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
