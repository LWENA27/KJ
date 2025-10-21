<?php
require_once __DIR__ . '/../config/database.php';

echo "Applying visit number fix...\n";

try {
    // Apply the visit number fix
    $sql = "
    SET @patient_id = 0;
    SET @visit_num = 0;
    
    UPDATE patient_visits
    JOIN (
        SELECT 
            id,
            patient_id,
            @visit_num := IF(@patient_id = patient_id, @visit_num + 1, 1) AS new_visit_number,
            @patient_id := patient_id
        FROM patient_visits
        ORDER BY patient_id, created_at
    ) AS numbered ON patient_visits.id = numbered.id
    SET patient_visits.visit_number = numbered.new_visit_number;
    ";
    
    $pdo->exec($sql);
    echo "✅ Visit number fix applied successfully!\n";
    
    // Verify results
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
    
    echo "\n📊 UPDATED VISIT NUMBERS:\n";
    echo str_repeat("-", 60) . "\n";
    
    while ($row = $stmt->fetch()) {
        printf("%-12s | %-20s | Visit #%-2d | %s\n", 
            $row['registration_number'],
            substr($row['patient_name'], 0, 20),
            $row['visit_number'],
            $row['visit_date']
        );
    }
    
    echo "\n✅ All done! Visit numbers are now properly sequenced.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>