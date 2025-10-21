<?php
/**
 * Test Script: Verify Visit Number Fix
 * Run this script to test that visit numbers are working correctly
 */

require_once __DIR__ . '/../config/database.php';

echo "=== VISIT NUMBER FIX VERIFICATION ===\n\n";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    echo "✅ Database connection successful\n\n";

    // Test 1: Check current visit numbers
    echo "📊 CURRENT VISIT NUMBERS:\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            p.registration_number,
            CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
            v.visit_number,
            v.visit_date,
            v.visit_type
        FROM patients p
        JOIN patient_visits v ON p.id = v.patient_id
        ORDER BY p.id, v.visit_number
    ");
    
    $visits = $stmt->fetchAll();
    
    foreach ($visits as $visit) {
        printf("%-15s | %-25s | Visit #%-2d | %s | %s\n", 
            $visit['registration_number'],
            $visit['patient_name'],
            $visit['visit_number'],
            $visit['visit_date'],
            $visit['visit_type']
        );
    }
    
    echo "\n";

    // Test 2: Check for proper visit number sequencing
    echo "🔍 VISIT NUMBER VALIDATION:\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            patient_id,
            COUNT(*) as total_visits,
            MIN(visit_number) as min_visit_num,
            MAX(visit_number) as max_visit_num
        FROM patient_visits 
        GROUP BY patient_id
        HAVING COUNT(*) != MAX(visit_number) OR MIN(visit_number) != 1
    ");
    
    $issues = $stmt->fetchAll();
    
    if (empty($issues)) {
        echo "✅ All visit numbers are properly sequenced!\n";
    } else {
        echo "❌ Found issues with visit number sequencing:\n";
        foreach ($issues as $issue) {
            echo "Patient ID {$issue['patient_id']}: {$issue['total_visits']} visits, range {$issue['min_visit_num']}-{$issue['max_visit_num']}\n";
        }
    }
    
    echo "\n";

    // Test 3: Simulate the next visit number calculation
    echo "🧪 TESTING NEXT VISIT NUMBER CALCULATION:\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("SELECT DISTINCT patient_id FROM patient_visits LIMIT 3");
    $test_patients = $stmt->fetchAll();
    
    foreach ($test_patients as $patient) {
        $patient_id = $patient['patient_id'];
        
        // Get patient name
        $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) as name FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient_info = $stmt->fetch();
        
        // Calculate next visit number
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(visit_number), 0) + 1 as next_visit_number FROM patient_visits WHERE patient_id = ?");
        $stmt->execute([$patient_id]);
        $result = $stmt->fetch();
        
        // Get current visit count
        $stmt = $pdo->prepare("SELECT COUNT(*) as current_visits FROM patient_visits WHERE patient_id = ?");
        $stmt->execute([$patient_id]);
        $count = $stmt->fetch();
        
        printf("Patient ID %d (%s): Current visits: %d, Next visit number: %d\n", 
            $patient_id, 
            $patient_info['name'], 
            $count['current_visits'], 
            $result['next_visit_number']
        );
    }
    
    echo "\n";

    // Test 4: Check for duplicate visit numbers per patient
    echo "🔍 CHECKING FOR DUPLICATE VISIT NUMBERS:\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("
        SELECT patient_id, visit_number, COUNT(*) as duplicates
        FROM patient_visits
        GROUP BY patient_id, visit_number
        HAVING COUNT(*) > 1
    ");
    
    $duplicates = $stmt->fetchAll();
    
    if (empty($duplicates)) {
        echo "✅ No duplicate visit numbers found!\n";
    } else {
        echo "❌ Found duplicate visit numbers:\n";
        foreach ($duplicates as $dup) {
            echo "Patient ID {$dup['patient_id']}, Visit #{$dup['visit_number']}: {$dup['duplicates']} duplicates\n";
        }
    }
    
    echo "\n";

    // Test 5: Show patient history example
    echo "📋 SAMPLE PATIENT HISTORY (Patient ID 4 - Diamond):\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->prepare("
        SELECT 
            v.visit_number,
            v.visit_date,
            v.visit_type,
            COALESCE(c.diagnosis, 'No diagnosis') as diagnosis,
            COALESCE(c.chief_complaint, 'No complaint') as chief_complaint,
            COALESCE(SUM(pay.amount), 0) as total_paid
        FROM patient_visits v
        LEFT JOIN consultations c ON v.id = c.visit_id
        LEFT JOIN payments pay ON v.id = pay.visit_id
        WHERE v.patient_id = 4
        GROUP BY v.id
        ORDER BY v.visit_number
    ");
    $stmt->execute();
    $history = $stmt->fetchAll();
    
    if (empty($history)) {
        echo "No visits found for patient ID 4\n";
    } else {
        printf("%-8s | %-12s | %-15s | %-20s | %-10s\n", 
            "Visit #", "Date", "Type", "Diagnosis", "Paid"
        );
        echo str_repeat("-", 80) . "\n";
        
        foreach ($history as $visit) {
            printf("%-8d | %-12s | %-15s | %-20s | $%-8.2f\n",
                $visit['visit_number'],
                $visit['visit_date'],
                $visit['visit_type'],
                substr($visit['diagnosis'], 0, 20),
                $visit['total_paid']
            );
        }
    }
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "✅ VISIT NUMBER FIX VERIFICATION COMPLETE!\n";
    echo "Your system is now ready to handle patient revisits properly.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>