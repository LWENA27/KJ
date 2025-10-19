<?php
require_once __DIR__ . '/../includes/BaseController.php';

class PatienthistoryController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    // Enhanced patient search with smart filtering
    public function search_patients() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $query = $_POST['query'] ?? '';
        $filters = $_POST['filters'] ?? [];

        if (strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }

        try {
            // Simple search first for debugging
            $stmt = $this->pdo->prepare("
                SELECT id, first_name, last_name, phone, registration_number, 
                       TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) as age
                FROM patients 
                WHERE first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR registration_number LIKE ?
                ORDER BY first_name, last_name
                LIMIT 10
            ");
            $searchTerm = "%{$query}%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $patients = $stmt->fetchAll();

            header('Content-Type: application/json');
            echo json_encode($patients);
            exit;
        } catch (Exception $e) {
            error_log("Search error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Search failed: ' . $e->getMessage()]);
            exit;
        }
        
        // Original complex search code (commented out for debugging)
        /*
        // Original complex search code (commented out for debugging)
        */
    }

    // Get patient health trends and analytics
    public function get_patient_analytics($patient_id) {
        try {
            // Get vital signs trends
            $stmt = $this->pdo->prepare("
          SELECT COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date, 
                       CAST(SUBSTRING_INDEX(blood_pressure, '/', 1) AS UNSIGNED) as systolic,
                       CAST(SUBSTRING_INDEX(blood_pressure, '/', -1) AS UNSIGNED) as diastolic,
                       pulse_rate, temperature, weight
                FROM consultations c
                LEFT JOIN patient_visits pv ON c.visit_id = pv.id
                WHERE patient_id = ? AND DATE(COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) >= DATE_SUB(NOW(), INTERVAL 1 YEAR)
                ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at)
            ");
            $stmt->execute([$patient_id]);
            $vitals_trends = $stmt->fetchAll();

            // Get lab trends
                $stmt = $this->pdo->prepare("
                    SELECT lr.created_at, t.test_name as test_name, lr.result_value, t.normal_range
                    FROM lab_results lr
                    JOIN lab_tests t ON lr.test_id = t.id
                    JOIN consultations c ON lr.consultation_id = c.id
                    WHERE c.patient_id = ? AND lr.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)
                    ORDER BY lr.created_at
                ");
            $stmt->execute([$patient_id]);
            $lab_trends = $stmt->fetchAll();

            // Get medication history
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date, m.name as medicine_name, ma.quantity, ma.dosage, ma.instructions
                FROM medicine_allocations ma
                JOIN medicines m ON ma.medicine_id = m.id
                JOIN consultations c ON ma.consultation_id = c.id
                LEFT JOIN patient_visits pv ON c.visit_id = pv.id
                WHERE c.patient_id = ? AND DATE(COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) >= DATE_SUB(NOW(), INTERVAL 1 YEAR)
                ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at) DESC
            ");
            $stmt->execute([$patient_id]);
            $medication_history = $stmt->fetchAll();

            // Get diagnosis patterns
            $stmt = $this->pdo->prepare("
                SELECT final_diagnosis, COUNT(*) as frequency, MAX(COALESCE(appointment_date, pv.visit_date, created_at)) as last_occurrence
                FROM consultations c
                LEFT JOIN patient_visits pv ON c.visit_id = pv.id
                WHERE patient_id = ? AND final_diagnosis IS NOT NULL AND final_diagnosis != ''
                GROUP BY final_diagnosis
                ORDER BY frequency DESC, last_occurrence DESC
            ");
            $stmt->execute([$patient_id]);
            $diagnosis_patterns = $stmt->fetchAll();

            return [
                'vitals_trends' => $vitals_trends,
                'lab_trends' => $lab_trends,
                'medication_history' => $medication_history,
                'diagnosis_patterns' => $diagnosis_patterns
            ];
        } catch (Exception $e) {
            return ['error' => 'Failed to get analytics'];
        }
    }

    // Generate clinical decision support alerts
    public function get_clinical_alerts($patient_id) {
        $alerts = [];

        try {
            // Check for drug interactions
            $stmt = $this->pdo->prepare("
                SELECT m.name, ma.dosage, pv.visit_date
                FROM medicine_allocations ma
                JOIN medicines m ON ma.medicine_id = m.id
                JOIN consultations c ON ma.consultation_id = c.id
                LEFT JOIN patient_visits pv ON c.visit_id = pv.id
                WHERE c.patient_id = ? AND pv.visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ORDER BY pv.visit_date DESC
            ");
            $stmt->execute([$patient_id]);
            $recent_medications = $stmt->fetchAll();

            // Check for potential drug interactions (simplified)
            $interaction_pairs = [
                ['warfarin', 'aspirin'],
                ['metformin', 'alcohol'],
                ['digoxin', 'furosemide']
            ];

            foreach ($interaction_pairs as $pair) {
                $found_drugs = [];
                foreach ($recent_medications as $med) {
                    if (stripos($med['name'], $pair[0]) !== false || stripos($med['name'], $pair[1]) !== false) {
                        $found_drugs[] = $med['name'];
                    }
                }
                if (count($found_drugs) >= 2) {
                    $alerts[] = [
                        'type' => 'drug_interaction',
                        'severity' => 'high',
                        'message' => 'Potential drug interaction detected: ' . implode(' and ', $found_drugs),
                        'action' => 'Review medication compatibility'
                    ];
                }
            }

            // Check for abnormal lab trends
            $stmt = $this->pdo->prepare("
                SELECT t.test_name as name, lr.result_value, lr.created_at
                FROM lab_results lr
                JOIN lab_tests t ON lr.test_id = t.id
                JOIN consultations c ON lr.consultation_id = c.id
                WHERE c.patient_id = ? AND lr.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                ORDER BY t.test_name, lr.created_at DESC
            ");
            $stmt->execute([$patient_id]);
            $recent_labs = $stmt->fetchAll();

            // Group by test type and check for trends
            $lab_groups = [];
            foreach ($recent_labs as $lab) {
                $lab_groups[$lab['name']][] = $lab;
            }

            foreach ($lab_groups as $test_name => $results) {
                if (count($results) >= 2) {
                    $latest = floatval($results[0]['result_value']);
                    $previous = floatval($results[1]['result_value']);
                    
                    if ($latest > $previous * 1.5) {
                        $alerts[] = [
                            'type' => 'lab_trend',
                            'severity' => 'medium',
                            'message' => "Significant increase in {$test_name}: {$previous} → {$latest}",
                            'action' => 'Monitor closely and consider intervention'
                        ];
                    }
                }
            }

            // Check visit frequency
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as visit_count
                FROM consultations 
                WHERE patient_id = ? AND appointment_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stmt->execute([$patient_id]);
            $recent_visits = $stmt->fetch();

            if ($recent_visits['visit_count'] >= 3) {
                $alerts[] = [
                    'type' => 'frequent_visits',
                    'severity' => 'medium',
                    'message' => "Patient has visited {$recent_visits['visit_count']} times in the last 30 days",
                    'action' => 'Consider underlying chronic condition or treatment effectiveness'
                ];
            }

        } catch (Exception $e) {
            $alerts[] = [
                'type' => 'system_error',
                'severity' => 'low',
                'message' => 'Unable to generate clinical alerts',
                'action' => 'Manual review recommended'
            ];
        }

        return $alerts;
    }

    // Generate comprehensive patient summary
    public function get_patient_summary($patient_id) {
        try {
            // Basic patient info
            $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
            $stmt->execute([$patient_id]);
            $patient = $stmt->fetch();

            // Visit statistics
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_visits,
                    MIN(appointment_date) as first_visit,
                    MAX(appointment_date) as last_visit,
                    AVG(DATEDIFF(NOW(), appointment_date)) as avg_days_between_visits
                FROM consultations 
                WHERE patient_id = ?
            ");
            $stmt->execute([$patient_id]);
            $visit_stats = $stmt->fetch();

            // Most common diagnoses
            $stmt = $this->pdo->prepare("
                SELECT final_diagnosis, COUNT(*) as frequency
                FROM consultations 
                WHERE patient_id = ? AND final_diagnosis IS NOT NULL AND final_diagnosis != ''
                GROUP BY final_diagnosis
                ORDER BY frequency DESC
                LIMIT 5
            ");
            $stmt->execute([$patient_id]);
            $common_diagnoses = $stmt->fetchAll();

            // Recent test results
            $stmt = $this->pdo->prepare("
                SELECT t.test_name as name, lr.result_value, lr.created_at
                FROM lab_results lr
                JOIN lab_tests t ON lr.test_id = t.id
                JOIN consultations c ON lr.consultation_id = c.id
                WHERE c.patient_id = ?
                ORDER BY lr.created_at DESC
                LIMIT 10
            ");
            $stmt->execute([$patient_id]);
            $recent_tests = $stmt->fetchAll();

            return [
                'patient' => $patient,
                'visit_stats' => $visit_stats,
                'common_diagnoses' => $common_diagnoses,
                'recent_tests' => $recent_tests,
                'analytics' => $this->get_patient_analytics($patient_id),
                'clinical_alerts' => $this->get_clinical_alerts($patient_id)
            ];
        } catch (Exception $e) {
            return ['error' => 'Failed to generate patient summary'];
        }
    }
}
?>
