<?php
require_once __DIR__ . '/../config/database.php';

echo "Cleaning up duplicate consultation records...\n";

try {
    // Find and remove duplicate consultations created today
    $sql = "
    DELETE c1 FROM consultations c1
    INNER JOIN consultations c2 
    WHERE c1.id > c2.id 
    AND c1.patient_id = c2.patient_id 
    AND c1.visit_id != c2.visit_id
    AND DATE(c1.created_at) = CURDATE()
    AND DATE(c2.created_at) = CURDATE()
    AND c1.status = 'pending'
    AND c2.status = 'pending'
    ";
    
    $result = $pdo->exec($sql);
    echo "✅ Removed {$result} duplicate consultation records\n";
    
    // Also clean up duplicate visits for the same patient on the same day
    $sql2 = "
    DELETE v1 FROM patient_visits v1
    INNER JOIN patient_visits v2 
    WHERE v1.id > v2.id 
    AND v1.patient_id = v2.patient_id 
    AND v1.visit_date = v2.visit_date
    AND v1.visit_date = CURDATE()
    AND v1.status = 'active'
    AND v2.status = 'active'
    ";
    
    $result2 = $pdo->exec($sql2);
    echo "✅ Removed {$result2} duplicate visit records\n";
    
    // Show current status
    $stmt = $pdo->query("
        SELECT 
            p.first_name, 
            p.last_name, 
            pv.visit_date,
            pv.visit_number,
            COUNT(c.id) as consultation_count
        FROM patients p
        JOIN patient_visits pv ON p.id = pv.patient_id
        LEFT JOIN consultations c ON pv.id = c.visit_id
        WHERE pv.visit_date = CURDATE()
        GROUP BY p.id, pv.id
        HAVING consultation_count > 1
    ");
    
    $duplicates = $stmt->fetchAll();
    
    if (empty($duplicates)) {
        echo "✅ No remaining duplicates found!\n";
    } else {
        echo "⚠️ Remaining duplicates:\n";
        foreach ($duplicates as $dup) {
            echo "- {$dup['first_name']} {$dup['last_name']}: {$dup['consultation_count']} consultations\n";
        }
    }
    
    echo "\n✅ Cleanup completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>