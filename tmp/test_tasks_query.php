<?php
/**
 * Test script to verify tasks query
 */

require_once __DIR__ . '/../config/database.php';

$user_id = 2; // Jane Receptionist

try {
    $stmt = $pdo->prepare("
        SELECT 
            so.id,
            so.patient_id,
            so.visit_id,
            so.status,
            so.notes,
            so.created_at,
            so.updated_at,
            so.performed_at,
            p.first_name,
            p.last_name,
            p.registration_number,
            pv.visit_date,
            s.service_name,
            s.description as service_description,
            u.first_name as ordered_by_first,
            u.last_name as ordered_by_last
        FROM service_orders so
        JOIN patients p ON so.patient_id = p.id
        JOIN services s ON so.service_id = s.id
        LEFT JOIN patient_visits pv ON so.visit_id = pv.id
        LEFT JOIN users u ON so.ordered_by = u.id
        WHERE so.performed_by = ? 
        AND so.status IN ('pending', 'in_progress')
        ORDER BY so.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($tasks) . " tasks for user_id {$user_id}:\n\n";
    
    foreach ($tasks as $task) {
        echo "Task ID: {$task['id']}\n";
        echo "  Patient: {$task['first_name']} {$task['last_name']} ({$task['registration_number']})\n";
        echo "  Service: {$task['service_name']}\n";
        echo "  Status: {$task['status']}\n";
        echo "  Visit Date: {$task['visit_date']}\n";
        echo "  Created: {$task['created_at']}\n";
        if ($task['ordered_by_first']) {
            echo "  Ordered by: {$task['ordered_by_first']} {$task['ordered_by_last']}\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
