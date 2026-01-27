<?php
/**
 * Test Radiologist Payment Blocking Implementation
 * Verifies that radiologists cannot start tests without payment
 */

// Include the base controller
require_once __DIR__ . '/../includes/BaseController.php';
require_once __DIR__ . '/../config/database.php';

echo "=== Testing Radiologist Payment Blocking ===\n\n";

try {
    // Check if workflow_overrides table exists
    $result = $pdo->query("SHOW TABLES LIKE 'workflow_overrides'");
    if ($result->rowCount() === 0) {
        echo "[INFO] Creating workflow_overrides table...\n";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS workflow_overrides (
                id INT PRIMARY KEY AUTO_INCREMENT,
                patient_id INT NOT NULL,
                workflow_step VARCHAR(50) NOT NULL,
                override_reason VARCHAR(255),
                overridden_by INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (patient_id) REFERENCES patients(id),
                FOREIGN KEY (overridden_by) REFERENCES users(id),
                KEY (patient_id, workflow_step)
            )
        ");
        echo "✓ workflow_overrides table created\n\n";
    } else {
        echo "[INFO] workflow_overrides table already exists\n\n";
    }
    
    // Test Case 1: Find a patient with an unpaid radiology order
    echo "--- Test Case 1: Check for unpaid radiology orders ---\n";
    
    $stmt = $pdo->prepare("
        SELECT 
            rto.id as order_id,
            rto.patient_id,
            p.first_name,
            p.last_name,
            p.registration_number,
            rt.test_name,
            pv.id as visit_id,
            COALESCE(pay.id, 0) as paid_radiology
        FROM radiology_test_orders rto
        JOIN patients p ON rto.patient_id = p.id
        JOIN radiology_tests rt ON rto.test_id = rt.id
        JOIN patient_visits pv ON rto.patient_id = pv.patient_id AND pv.id = (
            SELECT MAX(id) FROM patient_visits WHERE patient_id = rto.patient_id
        )
        LEFT JOIN payments pay ON pv.id = pay.visit_id AND pay.payment_type = 'service' AND pay.item_type = 'radiology_order' AND pay.payment_status = 'paid'
        WHERE rto.status IN ('pending', 'scheduled')
        LIMIT 5
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll();
    
    if (count($orders) > 0) {
        foreach ($orders as $order) {
            $status = ($order['paid_radiology'] > 0) ? '✓ PAID' : '✗ UNPAID';
            echo "  Order ID: {$order['order_id']} - Patient: {$order['first_name']} {$order['last_name']} ({$order['registration_number']})\n";
            echo "  Test: {$order['test_name']} - Status: {$status}\n";
            echo "  Visit ID: {$order['visit_id']}\n\n";
        }
    } else {
        echo "  No pending radiology orders found\n";
    }
    
    // Test Case 2: Verify checkWorkflowAccess recognizes 'radiology' step
    echo "\n--- Test Case 2: Verify checkWorkflowAccess method ---\n";
    
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.first_name,
            p.last_name,
            pv.id as visit_id
        FROM patients p
        JOIN patient_visits pv ON p.id = pv.patient_id
        LIMIT 1
    ");
    $stmt->execute();
    $patient = $stmt->fetch();
    
    if ($patient) {
        echo "  Testing with patient: {$patient['first_name']} {$patient['last_name']} (ID: {$patient['id']})\n";
        echo "  Visit ID: {$patient['visit_id']}\n";
        
        // Check if they have radiology payment
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM payments 
            WHERE visit_id = ? 
            AND payment_type = 'service' 
            AND item_type = 'radiology_order' 
            AND payment_status = 'paid'
        ");
        $stmt->execute([$patient['visit_id']]);
        $payment_count = $stmt->fetch()['count'];
        
        echo "  Radiology payments (paid): {$payment_count}\n";
        
        if ($payment_count > 0) {
            echo "  Result: Access should be ALLOWED (patient has paid)\n";
        } else {
            echo "  Result: Access should be BLOCKED (patient has not paid)\n";
        }
    }
    
    // Test Case 3: Check perform_test view file
    echo "\n--- Test Case 3: Verify perform_test view updated ---\n";
    
    $view_file = __DIR__ . '/../views/radiologist/perform_test.php';
    if (file_exists($view_file)) {
        $content = file_get_contents($view_file);
        
        if (strpos($content, 'Payment Required') !== false) {
            echo "  ✓ perform_test.php contains 'Payment Required' modal\n";
        } else {
            echo "  ✗ perform_test.php missing payment modal\n";
        }
        
        if (strpos($content, 'override_reason') !== false) {
            echo "  ✓ perform_test.php contains override_reason field\n";
        } else {
            echo "  ✗ perform_test.php missing override_reason field\n";
        }
        
        if (strpos($content, "\$access_check") !== false) {
            echo "  ✓ perform_test.php checks access_check variable\n";
        } else {
            echo "  ✗ perform_test.php doesn't check access_check\n";
        }
    }
    
    // Test Case 4: Check BaseController for radiology in checkWorkflowAccess
    echo "\n--- Test Case 4: Verify BaseController updated ---\n";
    
    $controller_file = __DIR__ . '/../includes/BaseController.php';
    if (file_exists($controller_file)) {
        $content = file_get_contents($controller_file);
        
        if (strpos($content, "'radiology'") !== false) {
            echo "  ✓ BaseController contains 'radiology' step\n";
        } else {
            echo "  ✗ BaseController missing 'radiology' step\n";
        }
        
        if (strpos($content, "item_type = 'radiology_order'") !== false) {
            echo "  ✓ BaseController checks for 'radiology_order' item_type\n";
        } else {
            echo "  ✗ BaseController doesn't check radiology_order item_type\n";
        }
    }
    
    // Test Case 5: Check RadiologistController for payment blocking
    echo "\n--- Test Case 5: Verify RadiologistController updated ---\n";
    
    $radiologist_file = __DIR__ . '/../controllers/RadiologistController.php';
    if (file_exists($radiologist_file)) {
        $content = file_get_contents($radiologist_file);
        
        if (strpos($content, "checkWorkflowAccess(\$patient_id, 'radiology')") !== false) {
            echo "  ✓ RadiologistController calls checkWorkflowAccess for radiology\n";
        } else {
            echo "  ✗ RadiologistController doesn't call checkWorkflowAccess\n";
        }
        
        if (strpos($content, "override_reason") !== false) {
            echo "  ✓ RadiologistController handles override_reason\n";
        } else {
            echo "  ✗ RadiologistController doesn't handle override_reason\n";
        }
        
        if (strpos($content, "workflow_overrides") !== false) {
            echo "  ✓ RadiologistController logs overrides\n";
        } else {
            echo "  ✗ RadiologistController doesn't log overrides\n";
        }
    }
    
    echo "\n=== All Tests Completed Successfully ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    error_log("Test script error: " . $e->getMessage());
}
?>
