<?php
require_once __DIR__ . '/../includes/BaseController.php';

class DoctorController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireRole('doctor');
    }

    public function dashboard() {
        $doctor_id = $_SESSION['user_id'];

        // Patients ready for consultation removed from dashboard UI - no query needed

                // Today's completed consultations (check both created_at and follow_up_date for robustness)
                                $stmt = $this->pdo->prepare("                        SELECT c.*, p.first_name, p.last_name, p.date_of_birth, p.phone,
                                                COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date
                                                FROM consultations c
                                                JOIN patients p ON c.patient_id = p.id
                                                LEFT JOIN patient_visits pv ON c.visit_id = pv.id
                                                WHERE c.doctor_id = ?
                                                    AND c.status = 'completed'
                                                    AND (
                                                                        DATE(c.created_at) = CURDATE()
                                                                        OR DATE(COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) = CURDATE()
                                                                    )
                                                ORDER BY COALESCE(c.created_at, COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) DESC
                                ");
                $stmt->execute([$doctor_id]);
        $today_completed = $stmt->fetchAll();

        // Consultation statistics
        $stmt = $this->pdo->prepare("
            SELECT
                COUNT(*) as total_consultations,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_consultations,
                COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_consultations
            FROM consultations
            WHERE doctor_id = ?
        ");
        $stmt->execute([$doctor_id]);
        $stats = $stmt->fetch();

        // Patients waiting for lab results review (join lab_results with tests to get names)
        // Scope to consultations that belong to this doctor
        $stmt = $this->pdo->prepare("
            SELECT p.*,
                   ag.test_names,
                   ag.latest_result_date as result_date
            FROM patients p
            LEFT JOIN (
                SELECT c.patient_id,
                       GROUP_CONCAT(lt.test_name SEPARATOR ', ') AS test_names,
                       MAX(lr.completed_at) AS latest_result_date
                FROM lab_results lr
                JOIN lab_test_orders lto ON lr.order_id = lto.id
                JOIN consultations c ON lto.consultation_id = c.id
                JOIN lab_tests lt ON lr.test_id = lt.id
                WHERE lto.status = 'completed' AND c.doctor_id = ?
                GROUP BY c.patient_id
            ) ag ON p.id = ag.patient_id
            WHERE (SELECT pvx2.status FROM patient_visits pvx2 WHERE pvx2.patient_id = p.id ORDER BY pvx2.created_at DESC LIMIT 1) = 'results_review' AND ag.test_names IS NOT NULL
            ORDER BY ag.latest_result_date DESC
        ");
        $stmt->execute([$doctor_id]);
        $pending_results = $stmt->fetchAll();

        // Get all doctors for patient allocation
        $stmt = $this->pdo->prepare("
            SELECT id, CONCAT(first_name, ' ', last_name) as name
            FROM users 
            WHERE role = 'doctor' AND id != ?
        ");
        $stmt->execute([$doctor_id]);
        $other_doctors = $stmt->fetchAll();

        // Recent lab results
        $stmt = $this->pdo->prepare("
            SELECT lr.*, lt.test_name as test_name, p.first_name, p.last_name
            FROM lab_results lr
            JOIN lab_test_orders lto ON lr.order_id = lto.id
            JOIN lab_tests lt ON lr.test_id = lt.id
            JOIN consultations c ON lto.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            WHERE c.doctor_id = ?
            ORDER BY lr.completed_at DESC
            LIMIT 5
        ");
        $stmt->execute([$doctor_id]);
        $recent_results = $stmt->fetchAll();

        // Available patients (paid for consultation) - Today's patients in FIFO order
        $filter_date = $_GET['date'] ?? date('Y-m-d');
        $filter_date = trim($filter_date);
        $search_q = trim($_GET['q'] ?? '');

        $sql = "
            SELECT p.*,
                   pv.visit_date,
                   pv.visit_type,
                   pv.created_at as visit_created_at,
                   COALESCE(cc.consultation_count, 0) as consultation_count,
                   c.status as consultation_status,
                   c.consultation_type,
                   IF(EXISTS(
                       SELECT 1 FROM payments pay 
                       WHERE pay.visit_id = pv.id 
                       AND pay.payment_type = 'registration' 
                       AND pay.payment_status = 'paid'
                   ), 1, 0) as consultation_registration_paid
            FROM patients p
            JOIN patient_visits pv ON p.id = pv.patient_id
            LEFT JOIN (
                SELECT patient_id, COUNT(*) as consultation_count
                FROM consultations 
                GROUP BY patient_id
            ) cc ON p.id = cc.patient_id
            LEFT JOIN consultations c ON p.id = c.patient_id AND c.visit_id = pv.id
            WHERE pv.visit_type = 'consultation' 
            AND DATE(pv.visit_date) = CURDATE()
            AND pv.status = 'active'
            AND (c.status IS NULL OR c.status NOT IN ('completed', 'cancelled'))
            ORDER BY pv.created_at ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $available_patients = $stmt->fetchAll();

                // Pending consultations (registered / scheduled / waiting) for this doctor
                        $stmt = $this->pdo->prepare(
                        "SELECT c.*, p.first_name AS patient_first, p.last_name AS patient_last, p.date_of_birth AS patient_dob, p.phone AS patient_phone,
                         (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv WHERE pv.patient_id = p.id ORDER BY pv.created_at DESC LIMIT 1) AND pay.payment_type = 'registration' AND pay.payment_status = 'paid'),1,0)) as consultation_registration_paid
                         FROM consultations c
                         JOIN patients p ON c.patient_id = p.id
                         WHERE c.doctor_id = ?
                             AND c.status NOT IN ('completed', 'cancelled')
                         ORDER BY COALESCE(c.created_at, c.follow_up_date) ASC"
                );
                $stmt->execute([$doctor_id]);
                $pending_consultations = $stmt->fetchAll();

        // Add these variables for the dashboard statistics
        $stats = [
            'today_consultations' => 0,
            'completed_consultations' => 0,
            'total_consultations' => 0
        ];

        // Pass all required variables to the view
        $this->render('doctor/dashboard', [
            'available_patients' => $available_patients,
            'stats' => $stats,
            'csrf_token' => $this->generateCSRF(),
            'other_doctors' => []  // Add empty array to prevent undefined variable error
        ]);
    }

    public function consultations() {
        $doctor_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("
            SELECT c.*, p.first_name, p.last_name, p.date_of_birth, p.phone,
                   pv.visit_date,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv WHERE pv.patient_id = p.id ORDER BY pv.created_at DESC LIMIT 1) AND pay.payment_type = 'registration' AND pay.payment_status = 'paid'),1,0)) as consultation_registration_paid,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay2 WHERE pay2.visit_id = (SELECT id FROM patient_visits pv2 WHERE pv2.patient_id = p.id ORDER BY pv2.created_at DESC LIMIT 1) AND pay2.payment_type = 'lab_test' AND pay2.payment_status = 'paid'),1,0)) as lab_tests_paid,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay3 WHERE pay3.visit_id = (SELECT id FROM patient_visits pv3 WHERE pv3.patient_id = p.id ORDER BY pv3.created_at DESC LIMIT 1) AND pay3.payment_type = 'lab_test' AND pay3.payment_status = 'paid'),1,0)) as results_review_paid,
                   c.main_complaint,
                   c.diagnosis as final_diagnosis,
                   c.diagnosis as preliminary_diagnosis,
                   COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date
            FROM consultations c
            JOIN patients p ON c.patient_id = p.id
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            WHERE c.doctor_id = ?
            ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at) DESC
        ");
            $stmt->execute([$doctor_id]);
        $consultations = $stmt->fetchAll();

        $this->render('doctor/consultations', [
            'consultations' => $consultations,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function view_patient($patient_id = null) {
        // Accept either path param or ?id= fallback
        if ($patient_id === null) {
            $patient_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
        }
        if (!$patient_id) {
            $this->redirect('doctor/patients');
        }

        // Check workflow access
        $access_check = $this->checkWorkflowAccess($patient_id, 'consultation');
        if (!$access_check['access']) {
            $this->render('doctor/payment_required', [
                'patient_id' => $patient_id,
                'step' => $access_check['step'],
                'message' => $access_check['message'],
                'csrf_token' => $this->generateCSRF()
            ]);
            return;
        }

        // Get patient details
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch();

        if (!$patient) {
            $this->redirect('doctor/patients');
        }

        // Get existing consultations
        $stmt = $this->pdo->prepare("
            SELECT c.*, pv.visit_date
            FROM consultations c
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            WHERE c.patient_id = ? 
            ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at) DESC
        ");
        $stmt->execute([$patient_id]);
        $consultations = $stmt->fetchAll();

        // Find latest visit for this patient (to scope consultation/lab orders to the new visit)
        $stmt = $this->pdo->prepare("SELECT * FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$patient_id]);
        $latest_visit = $stmt->fetch();
        $latest_visit_id = $latest_visit['id'] ?? null;

        // Get latest vital signs for this patient
        if (!empty($latest_visit_id)) {
            $stmt = $this->pdo->prepare("\n                SELECT vs.*, pv.visit_date\n                FROM vital_signs vs\n                LEFT JOIN patient_visits pv ON vs.visit_id = pv.id\n                WHERE vs.visit_id = ?\n                ORDER BY vs.recorded_at DESC\n                LIMIT 1\n            ");
            $stmt->execute([$latest_visit_id]);
            $vital_signs = $stmt->fetch();
            // fallback to patient-level latest if visit-level not present
            if (!$vital_signs) {
                $stmt = $this->pdo->prepare("\n                    SELECT vs.*, pv.visit_date\n                    FROM vital_signs vs\n                    LEFT JOIN patient_visits pv ON vs.visit_id = pv.id\n                    WHERE vs.patient_id = ?\n                    ORDER BY vs.recorded_at DESC\n                    LIMIT 1\n                ");
                $stmt->execute([$patient_id]);
                $vital_signs = $stmt->fetch();
            }
        } else {
            $stmt = $this->pdo->prepare("\n                SELECT vs.*, pv.visit_date\n                FROM vital_signs vs\n                LEFT JOIN patient_visits pv ON vs.visit_id = pv.id\n                WHERE vs.patient_id = ?\n                ORDER BY vs.recorded_at DESC\n                LIMIT 1\n            ");
            $stmt->execute([$patient_id]);
            $vital_signs = $stmt->fetch();
        }

        // Get lab test orders for the latest visit (for real-time medical record updates)
        if ($latest_visit_id) {
            $stmt = $this->pdo->prepare("\n                SELECT lto.*, lt.test_name, lt.test_code, lr.result_value, lr.result_text, lr.completed_at as result_completed_at\n                FROM lab_test_orders lto\n                JOIN lab_tests lt ON lto.test_id = lt.id\n                LEFT JOIN lab_results lr ON lto.id = lr.order_id\n                WHERE lto.patient_id = ? AND lto.visit_id = ?\n                ORDER BY lto.created_at DESC\n            ");
            $stmt->execute([$patient_id, $latest_visit_id]);
            $lab_orders = $stmt->fetchAll();
        } else {
            $lab_orders = [];
        }

        // Build a map of latest lab results by test name for this visit (if available), otherwise patient-level
        $lab_results_map = [];
        if ($latest_visit_id) {
            $stmt = $this->pdo->prepare("\n                SELECT lr.*, lt.test_name\n                FROM lab_results lr\n                JOIN lab_test_orders lto ON lr.order_id = lto.id\n                JOIN lab_tests lt ON lr.test_id = lt.id\n                WHERE lto.patient_id = ? AND lto.visit_id = ?\n                ORDER BY lt.test_name ASC, lr.completed_at DESC\n            ");
            $stmt->execute([$patient_id, $latest_visit_id]);
        } else {
            $stmt = $this->pdo->prepare("\n                SELECT lr.*, lt.test_name\n                FROM lab_results lr\n                JOIN lab_test_orders lto ON lr.order_id = lto.id\n                JOIN lab_tests lt ON lr.test_id = lt.id\n                WHERE lto.patient_id = ?\n                ORDER BY lt.test_name ASC, lr.completed_at DESC\n            ");
            $stmt->execute([$patient_id]);
        }
        $all_lab_results = $stmt->fetchAll();
        foreach ($all_lab_results as $r) {
            $name = $r['test_name'] ?? '';
            if ($name === '') continue;
            // keep first (most recent) occurrence per exact test_name
            if (!isset($lab_results_map[$name])) {
                $lab_results_map[$name] = $r;
            }
            // also store a normalized key to improve lookup flexibility
            $norm = strtolower(preg_replace('/\s+/', '', $name));
            if (!isset($lab_results_map[$norm])) {
                $lab_results_map[$norm] = $r;
            }
        }

        $this->render('doctor/view_patient', [
            'patient' => $patient,
            'consultations' => $consultations,
            'vital_signs' => $vital_signs,
            'lab_orders' => $lab_orders,
            'lab_results_map' => $lab_results_map,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function patients() {
        $doctor_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("
         SELECT p.*,
             COALESCE(consultation_counts.consultation_count, 0) as consultation_count,
                   (SELECT pv3.status FROM patient_visits pv3 WHERE pv3.patient_id = p.id ORDER BY pv3.created_at DESC LIMIT 1) as workflow_status,
             (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv2 WHERE pv2.patient_id = p.id ORDER BY pv2.created_at DESC LIMIT 1) AND pay.payment_type = 'registration' AND pay.payment_status = 'paid'),1,0)) AS consultation_registration_paid,
             (SELECT IF(EXISTS(SELECT 1 FROM payments pay2 WHERE pay2.visit_id = (SELECT id FROM patient_visits pv3 WHERE pv3.patient_id = p.id ORDER BY pv3.created_at DESC LIMIT 1) AND pay2.payment_type = 'lab_test' AND pay2.payment_status = 'paid'),1,0)) AS lab_tests_paid,
             (SELECT IF(EXISTS(SELECT 1 FROM payments pay3 WHERE pay3.visit_id = (SELECT id FROM patient_visits pv4 WHERE pv4.patient_id = p.id ORDER BY pv4.created_at DESC LIMIT 1) AND pay3.payment_type = 'lab_test' AND pay3.payment_status = 'paid'),1,0)) AS results_review_paid
         FROM patients p
            LEFT JOIN (
                SELECT patient_id, COUNT(*) as consultation_count
                FROM consultations
                WHERE doctor_id = ?
                GROUP BY patient_id
            ) consultation_counts ON p.id = consultation_counts.patient_id
                WHERE (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv WHERE pv.patient_id = p.id ORDER BY pv.created_at DESC LIMIT 1) AND pay.payment_type = 'registration' AND pay.payment_status = 'paid'),1,0)) = 1
            ORDER BY p.first_name
        ");
        $stmt->execute([$doctor_id]);
        $patients = $stmt->fetchAll();

        $this->render('doctor/patients', [
            'patients' => $patients,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function start_consultation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('doctor/dashboard');
        }

        $this->validateCSRF();

        $patient_id = $_POST['patient_id'];
        $doctor_id = $_SESSION['user_id'];

        // Determine latest visit for patient
        $stmt = $this->pdo->prepare("SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$patient_id]);
        $row = $stmt->fetch();
        $visit_id = $row['id'] ?? null;

        if (!$visit_id) {
            $_SESSION['error'] = 'No visit found for this patient';
            $this->redirect('doctor/view_patient/' . $patient_id);
        }

        // Check if doctor can attend this visit
        $can = $this->canAttend($visit_id);
        if (!$can['ok']) {
            $_SESSION['error'] = 'Cannot start consultation: ' . $can['reason'];
            $this->redirect('doctor/view_patient/' . $patient_id);
        }

        // Start or resume consultation using BaseController helper
        $start = $this->startConsultation($visit_id, $doctor_id);
        if (!$start['ok']) {
            $_SESSION['error'] = 'Failed to start consultation: ' . ($start['message'] ?? $start['reason'] ?? 'unknown');
            $this->redirect('doctor/view_patient/' . $patient_id);
        }

        $consultation_id = $start['consultation_id'];

        // Now proceed to handle submitted consultation details (lab tests, medicines, diagnosis)
        try {
            $this->pdo->beginTransaction();

            // Update the consultation record with submitted data
            $stmt = $this->pdo->prepare("UPDATE consultations SET main_complaint = ?, on_examination = ?, preliminary_diagnosis = ?, final_diagnosis = ?, treatment_plan = ?, status = 'completed', completed_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                $_POST['main_complaint'] ?? '',
                $_POST['on_examination'] ?? '',
                $_POST['preliminary_diagnosis'] ?? '',
                $_POST['final_diagnosis'] ?? ($_POST['diagnosis'] ?? ''),
                $_POST['treatment_plan'] ?? '',
                $consultation_id
            ]);

            // Handle selected lab tests - create lab test orders
            if (!empty($_POST['selected_tests'])) {
                $selected_tests = json_decode($_POST['selected_tests'], true);
                if (is_array($selected_tests)) {
                    // Find a lab technician for assignment
                    $stmtTech = $this->pdo->prepare("SELECT id FROM users WHERE role = 'lab_technician' AND is_active = 1 LIMIT 1");
                    $stmtTech->execute();
                    $technician = $stmtTech->fetch();
                    $technician_id = $technician['id'] ?? null;

                    // Create lab test orders for each selected test
                    $stmtOrder = $this->pdo->prepare("
                        INSERT INTO lab_test_orders 
                        (visit_id, patient_id, consultation_id, test_id, ordered_by, assigned_to, priority, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, 'normal', 'pending', NOW())
                    ");
                    
                    foreach ($selected_tests as $test_id) {
                        $stmtOrder->execute([
                            $visit_id,
                            $patient_id,
                            $consultation_id,
                            $test_id,
                            $doctor_id,
                            $technician_id
                        ]);
                    }
                    
                    // Update workflow - patient needs to pay for lab tests
                    $this->updateWorkflowStatus($patient_id, 'pending_payment', ['lab_tests_ordered' => true]);
                }
            }

            // Handle selected medicines
            if (!empty($_POST['selected_medicines'])) {
                $selected_medicines = json_decode($_POST['selected_medicines'], true);
                if (is_array($selected_medicines)) {
                    // Create prescriptions for each selected medicine
                    $stmtPrescription = $this->pdo->prepare("
                        INSERT INTO prescriptions 
                        (visit_id, patient_id, consultation_id, doctor_id, medicine_id, 
                         quantity_prescribed, dosage, frequency, duration, instructions, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
                    ");
                    
                    foreach ($selected_medicines as $medicine_data) {
                        $stmtPrescription->execute([
                            $visit_id,
                            $patient_id,
                            $consultation_id,
                            $doctor_id,
                            $medicine_data['id'],
                            $medicine_data['quantity'] ?? 1,
                            $medicine_data['dosage'] ?? '',
                            $medicine_data['frequency'] ?? 'as prescribed',
                            $medicine_data['duration'] ?? '',
                            $medicine_data['instructions'] ?? '',
                        ]);
                    }
                    
                    $this->updateWorkflowStatus($patient_id, 'pending_payment', ['medicine_prescribed' => true]);
                }
            }

            // Final workflow update and determine where to redirect
            $has_lab_tests = !empty($_POST['selected_tests']);
            $has_medicines = !empty($_POST['selected_medicines']);
            
            if (!$has_lab_tests && !$has_medicines) {
                $this->updateWorkflowStatus($patient_id, 'completed');
            }

            $this->pdo->commit();
            
            // Redirect based on what was ordered
            if ($has_lab_tests || $has_medicines) {
                $_SESSION['success'] = 'Consultation completed successfully. Patient needs to make payment for lab tests/medicines.';
                // Redirect to receptionist payments page so receptionist can process payment
                $this->redirect('receptionist/payments');
            } else {
                $_SESSION['success'] = 'Consultation completed successfully';
                $this->redirect('doctor/view_patient/' . $patient_id);
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to complete consultation: ' . $e->getMessage();
            $this->redirect('doctor/view_patient/' . $patient_id);
        }
    }

    public function patient_journey($patient_id = null) {
        if ($patient_id === null) {
            $patient_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
        }
        if (!$patient_id) {
            $this->redirect('doctor/patients');
        }

        // Check workflow access
        $access_check = $this->checkWorkflowAccess($patient_id, 'consultation');
        if (!$access_check['access']) {
            $_SESSION['error'] = $access_check['message'];
            $this->redirect('doctor/patients');
        }

        // Get patient details
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch();

        if (!$patient) {
            $this->redirect('doctor/patients');
        }

        // Get complete patient journey
        $journey = $this->getPatientJourney($patient_id);

        $this->render('doctor/patient_journey', [
            'patient' => $patient,
            'journey' => $journey,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function search_tests() {
        // Check authentication
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'doctor') {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $query = $_GET['q'] ?? '';

        if (strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("
                SELECT id, test_name as name, test_code as code, category_id as category, price, description
                FROM lab_tests
                WHERE test_name LIKE ? OR test_code LIKE ? OR description LIKE ?
                ORDER BY test_name
                LIMIT 20
            ");
            $search = "%{$query}%";
            $stmt->execute([$search, $search, $search]);
            $tests = $stmt->fetchAll();

            header('Content-Type: application/json');
            echo json_encode($tests);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
        exit;
    }

    public function search_medicines() {
        // Return JSON list of active medicines with current stock (from batches).
        $q = trim($_GET['q'] ?? '');
        try {
            if ($q === '') {
                // return top 50 active medicines if no query
                $stmt = $this->pdo->prepare("
                    SELECT m.id, m.name, m.generic_name, m.strength, m.unit, m.unit_price,
                           COALESCE(SUM(mb.quantity_remaining), 0) AS stock_quantity
                    FROM medicines m
                    LEFT JOIN medicine_batches mb ON mb.medicine_id = m.id AND mb.status = 'active'
                    WHERE m.is_active = 1
                    GROUP BY m.id
                    ORDER BY m.name
                    LIMIT 50
                ");
                $stmt->execute();
            } else {
                $term = '%' . str_replace('%','\\%',$q) . '%';
                $stmt = $this->pdo->prepare("
                    SELECT m.id, m.name, m.generic_name, m.strength, m.unit, m.unit_price,
                           COALESCE(SUM(mb.quantity_remaining), 0) AS stock_quantity
                    FROM medicines m
                    LEFT JOIN medicine_batches mb ON mb.medicine_id = m.id AND mb.status = 'active'
                    WHERE m.is_active = 1
                      AND (m.name LIKE ? OR m.generic_name LIKE ?)
                    GROUP BY m.id
                    ORDER BY m.name
                    LIMIT 50
                ");
                $stmt->execute([$term, $term]);
            }

            $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($medicines);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8', true, 500);
            echo json_encode(['error' => 'Failed to search medicines']);
            error_log('search_medicines error: ' . $e->getMessage());
            exit;
        }
    }

    public function view_lab_results($patient_id = null) {
        if ($patient_id === null) {
            $patient_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
        }
        if (!$patient_id) {
            $this->redirect('doctor/patients');
        }

        // Get lab results for this patient
            $stmt = $this->pdo->prepare("
                SELECT lr.*, t.test_name as test_name, t.test_code as test_code, t.category_id as category, t.normal_range, t.unit,
                       pv.visit_date, p.first_name, p.last_name, lto.status
                FROM lab_results lr
                JOIN lab_test_orders lto ON lr.order_id = lto.id
                JOIN lab_tests t ON lr.test_id = t.id
                JOIN consultations c ON lto.consultation_id = c.id
                JOIN patients p ON c.patient_id = p.id
                LEFT JOIN patient_visits pv ON c.visit_id = pv.id
                WHERE c.patient_id = ? AND lto.status = 'completed'
                ORDER BY lr.completed_at DESC
            ");
        $stmt->execute([$patient_id]);
        $lab_results = $stmt->fetchAll();

        // Get patient details
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch();

        $this->render('doctor/lab_results_view', [
            'patient' => $patient,
            'lab_results' => $lab_results,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    // List lab results index for the doctor (route: /doctor/lab_results)
    public function lab_results() {
        $doctor_id = $_SESSION['user_id'];

        // FIXED: Added c.patient_id to SELECT clause
        $stmt = $this->pdo->prepare("
            SELECT lr.*, 
                   t.test_name as test_name, 
                   p.first_name, 
                   p.last_name, 
                   c.patient_id,
                   pv.visit_date, 
                   lr.result_value, 
                   lr.result_text, 
                   lto.status, 
                   lr.completed_at as created_at
            FROM lab_results lr
            JOIN lab_test_orders lto ON lr.order_id = lto.id
            JOIN lab_tests t ON lr.test_id = t.id
            JOIN consultations c ON lto.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            WHERE c.doctor_id = ?
            ORDER BY lr.completed_at DESC
            LIMIT 200
        ");
        $stmt->execute([$doctor_id]);
        $results = $stmt->fetchAll();

        $this->render('doctor/lab_results', [
            'results' => $results,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function review_results() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('doctor/patients');
        }

        $this->validateCSRF();

        $patient_id = $_POST['patient_id'];
        $action = $_POST['action']; // 'prescribe', 'retest', or 'finalize'

        if (!$patient_id) {
            $_SESSION['error'] = 'Invalid request - patient ID missing';
            $this->redirect('doctor/patients');
        }

        try {
            $this->pdo->beginTransaction();

            if ($action === 'prescribe') {
                // Handle medicine prescription with details
                $results_summary = $_POST['results_summary'] ?? '';
                $prescription = $_POST['prescription'] ?? '';
                $selected_medicines = $_POST['selected_medicines'] ?? [];

                if (empty($prescription)) {
                    throw new Exception('Prescription details are required');
                }

                // Get the latest consultation for this patient
                $stmt = $this->pdo->prepare("
                    SELECT id FROM consultations
                    WHERE patient_id = ? AND doctor_id = ?
                    ORDER BY appointment_date DESC LIMIT 1
                ");
                $stmt->execute([$patient_id, $_SESSION['user_id']]);
                $consultation = $stmt->fetch();

                if (!$consultation) {
                    throw new Exception('No consultation found for this patient');
                }

                // Update consultation with prescription and results summary
                $stmt = $this->pdo->prepare("
                    UPDATE consultations
                    SET prescription = ?, treatment_plan = ?, status = 'completed'
                    WHERE id = ?
                ");
                $stmt->execute([$prescription, $results_summary, $consultation['id']]);

                // Create medicine allocations if medicines were selected
                if (!empty($selected_medicines)) {
                    foreach ($selected_medicines as $medicine_data) {
                        if (!empty($medicine_data['id']) && !empty($medicine_data['quantity'])) {
                            $stmt = $this->pdo->prepare("
                                INSERT INTO medicine_allocations (consultation_id, medicine_id, quantity, dosage, instructions, allocated_by)
                                VALUES (?, ?, ?, ?, ?, ?)
                            ");
                            $stmt->execute([
                                $consultation['id'],
                                $medicine_data['id'],
                                $medicine_data['quantity'],
                                $medicine_data['dosage'] ?? '',
                                $medicine_data['instructions'] ?? '',
                                $_SESSION['user_id']
                            ]);
                        }
                    }
                }

                // Update workflow status to medicine_dispensing and mark as prescribed
                $this->updateWorkflowStatus($patient_id, 'medicine_dispensing', ['medicine_prescribed' => true]);

                $_SESSION['success'] = 'Medicine prescribed successfully. Patient can now proceed to medicine dispensing.';

            } elseif ($action === 'retest') {
                // Send patient back for additional testing
                $this->updateWorkflowStatus($patient_id, 'lab_testing');
                $_SESSION['success'] = 'Patient sent back for additional testing.';

            } elseif ($action === 'finalize') {
                // Finalize treatment without prescription
                $results_summary = $_POST['results_summary'] ?? '';

                // Get the latest consultation for this patient
                $stmt = $this->pdo->prepare("
                    SELECT id FROM consultations
                    WHERE patient_id = ? AND doctor_id = ?
                    ORDER BY appointment_date DESC LIMIT 1
                ");
                $stmt->execute([$patient_id, $_SESSION['user_id']]);
                $consultation = $stmt->fetch();

                if ($consultation) {
                    // Update consultation with results summary
                    $stmt = $this->pdo->prepare("
                        UPDATE consultations
                        SET treatment_plan = ?, status = 'completed'
                        WHERE id = ?
                    ");
                    $stmt->execute([$results_summary, $consultation['id']]);
                }

                // Update workflow status to completed
                $this->updateWorkflowStatus($patient_id, 'completed');
                $_SESSION['success'] = 'Treatment finalized successfully. Patient journey completed.';

            } else {
                throw new Exception('Invalid action specified');
            }

            $this->pdo->commit();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to process request: ' . $e->getMessage();
        }

        $this->redirect('doctor/view_lab_results/' . $patient_id);
    }

    public function allocate_patient() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('doctor/dashboard');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);
        
        try {
            $patient_id = filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT);
            $target_doctor_id = filter_input(INPUT_POST, 'target_doctor_id', FILTER_VALIDATE_INT);
            $notes = trim($_POST['notes'] ?? '');

            if (!$patient_id || !$target_doctor_id) {
                throw new Exception('Invalid patient or doctor ID');
            }

            $this->pdo->beginTransaction();

            // Create consultation record for the target doctor
            $stmt = $this->pdo->prepare("
                INSERT INTO consultations (patient_id, doctor_id, status, notes, created_at)
                VALUES (?, ?, 'scheduled', ?, NOW())
            ");
            $stmt->execute([$patient_id, $target_doctor_id, $notes]);

            $this->pdo->commit();
            $_SESSION['success'] = 'Patient allocated successfully to another doctor';
            $this->redirect('doctor/dashboard');

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to allocate patient: ' . $e->getMessage();
            $this->redirect('doctor/dashboard');
        }
    }

    public function send_to_lab() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('doctor/dashboard');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);
        
        try {
            $patient_id = filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT);
            $tests = $_POST['tests'] ?? [];
            $notes = trim($_POST['notes'] ?? '');

            if (!$patient_id || empty($tests)) {
                throw new Exception('Invalid patient ID or no tests selected');
            }

            $this->pdo->beginTransaction();

            // Verify consultation registration payment: check latest visit's payments
            $stmt = $this->pdo->prepare("SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$patient_id]);
            $visit = $stmt->fetch();
            if (!$visit) {
                throw new Exception('No visit found for patient');
            }
            $visit_id = $visit['id'];

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE visit_id = ? AND payment_type = 'registration' AND payment_status = 'paid'");
            $stmt->execute([$visit_id]);
            $paid = (int)$stmt->fetchColumn();
            if ($paid === 0) {
                throw new Exception('Patient must complete consultation payment first');
            }

            // Find consultation in progress (if any) to associate orders
            $stmt = $this->pdo->prepare("SELECT id FROM consultations WHERE patient_id = ? AND doctor_id = ? AND status = 'in_progress' ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$patient_id, $_SESSION['user_id']]);
            $consultation_row = $stmt->fetch();
            $consultation_id = $consultation_row['id'] ?? null;

            // Create lab test orders (new schema) linked to visit and consultation
            foreach ($tests as $test_id) {
                $stmt = $this->pdo->prepare("INSERT INTO lab_test_orders (visit_id, patient_id, consultation_id, test_id, ordered_by, status, instructions, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW())");
                $stmt->execute([$visit_id, $patient_id, $consultation_id, $test_id, $_SESSION['user_id'], $notes]);
            }

            // Complete the consultation
            $stmt = $this->pdo->prepare("UPDATE consultations SET status = 'completed', notes = CONCAT(COALESCE(notes, ''), ' - Sent to lab for tests') WHERE patient_id = ? AND doctor_id = ? AND status = 'in_progress'");
            $stmt->execute([$patient_id, $_SESSION['user_id']]);

            $this->pdo->commit();
            $_SESSION['success'] = 'Patient sent to lab for tests. They must pay at reception first.';
            $this->redirect('doctor/dashboard');

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to send to lab: ' . $e->getMessage();
            $this->redirect('doctor/dashboard');
        }
    }

    public function send_to_medicine() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('doctor/dashboard');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);
        
        try {
            $patient_id = filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT);
            $medicines = $_POST['medicines'] ?? [];
            $notes = trim($_POST['notes'] ?? '');

            if (!$patient_id || empty($medicines)) {
                throw new Exception('Invalid patient ID or no medicines selected');
            }

            $this->pdo->beginTransaction();

            // Find latest visit and consultation in progress to attach prescriptions
            $stmt = $this->pdo->prepare("SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$patient_id]);
            $visit_row = $stmt->fetch();
            $visit_id = $visit_row['id'] ?? null;

            $stmt = $this->pdo->prepare("SELECT id FROM consultations WHERE patient_id = ? AND doctor_id = ? AND status = 'in_progress' ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$patient_id, $_SESSION['user_id']]);
            $consult_row = $stmt->fetch();
            $consultation_id = $consult_row['id'] ?? null;

            // Create prescriptions linked to consultation and visit
            foreach ($medicines as $medicine) {
                $stmt = $this->pdo->prepare("INSERT INTO prescriptions (consultation_id, visit_id, patient_id, doctor_id, medicine_id, quantity, dosage, instructions, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
                $stmt->execute([
                    $consultation_id,
                    $visit_id,
                    $patient_id,
                    $_SESSION['user_id'],
                    $medicine['medicine_id'],
                    $medicine['quantity'],
                    $medicine['dosage'],
                    $notes
                ]);
            }

            // Complete the consultation
            $stmt = $this->pdo->prepare("UPDATE consultations SET status = 'completed', notes = CONCAT(COALESCE(notes, ''), ' - Prescribed medicines') WHERE patient_id = ? AND doctor_id = ? AND status = 'in_progress'");
            $stmt->execute([$patient_id, $_SESSION['user_id']]);

            $this->pdo->commit();
            $_SESSION['success'] = 'Patient sent to reception for medicine dispensing. Payment required.';
            $this->redirect('doctor/dashboard');

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to prescribe medicines: ' . $e->getMessage();
            $this->redirect('doctor/dashboard');
        }
    }

    public function attend_patient($patient_id = null) {
        // Get patient ID from URL param or query string
        if ($patient_id === null) {
            $patient_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
        }
        
        if (!$patient_id) {
            $_SESSION['error'] = 'Invalid patient ID';
            $this->redirect('doctor/patients');
            return;
        }

        // Get patient details
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch();

        if (!$patient) {
            $_SESSION['error'] = 'Patient not found';
            $this->redirect('doctor/patients');
            return;
        }

        // Check workflow access
        $access_check = $this->checkWorkflowAccess($patient_id, 'consultation');
        if (!$access_check['access']) {
            $_SESSION['error'] = $access_check['message'];
            $this->redirect('doctor/patients');
            return;
        }

        $this->render('doctor/attend_patient', [
            'patient' => $patient,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function prescribe_medicine() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('doctor/lab_results');
            return;
        }

        $this->validateCSRF();
        
        $patient_id = filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT);
        
        // Get selected_medicines from POST
        $medicines_json = $_POST['selected_medicines'] ?? '';
        
        // Decode JSON string to array
        $medicines = [];
        if (is_string($medicines_json) && $medicines_json !== '') {
            $decoded = json_decode($medicines_json, true);
            if (is_array($decoded)) {
                $medicines = $decoded;
            }
        }
        
        $notes = trim($_POST['notes'] ?? '');

        // Validation
        if (!$patient_id) {
            $_SESSION['error'] = 'Invalid request - missing patient ID';
            $this->redirect('doctor/lab_results');
            return;
        }

        if (empty($medicines)) {
            $_SESSION['error'] = 'Please select at least one medicine';
            $this->redirect('doctor/lab_results');
            return;
        }

        try {
            $this->pdo->beginTransaction();

            // Get latest visit
            $stmt = $this->pdo->prepare("
                SELECT id FROM patient_visits 
                WHERE patient_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$patient_id]);
            $visit = $stmt->fetch();
            
            if (!$visit) {
                throw new Exception('No active visit found for patient');
            }

            $visit_id = $visit['id'];

            // Get or create consultation for this patient
            $stmt = $this->pdo->prepare("
                SELECT id FROM consultations 
                WHERE patient_id = ? AND doctor_id = ? 
                AND status IN ('in_progress', 'completed') 
                ORDER BY created_at DESC LIMIT 1
            ");
            $stmt->execute([$patient_id, $_SESSION['user_id']]);
            $consultation = $stmt->fetch();
            
            if (!$consultation) {
                throw new Exception('No active consultation found for this patient');
            }
            
            $consultation_id = $consultation['id'];
            
            // Create prescription records first to get prescription IDs
            $stmt = $this->pdo->prepare("
                INSERT INTO prescriptions (
                    consultation_id, visit_id, patient_id, doctor_id,
                    medicine_id, quantity_prescribed, dosage, 
                    frequency, duration, instructions, status, created_at
                ) VALUES (
                    ?, ?, ?, ?,
                    ?, ?, ?, 
                    ?, ?, ?, 'pending', NOW()
                )
            ");

            $prescription_ids = [];
            foreach ($medicines as $medicine_data) {
                $stmt->execute([
                    $consultation_id,
                    $visit_id, 
                    $patient_id,
                    $_SESSION['user_id'],
                    $medicine_data['id'],
                    $medicine_data['quantity'] ?? 1,
                    $medicine_data['dosage'] ?? '',
                    $medicine_data['frequency'] ?? 'as prescribed',
                    $medicine_data['duration'] ?? '',
                    $medicine_data['instructions'] ?? $notes
                ]);
                
                // Get the ID of the prescription we just created
                $prescription_ids[] = $this->pdo->lastInsertId();
            }

            // Update workflow status to pending_payment (same as start_consultation)
            $this->updateWorkflowStatus($patient_id, 'pending_payment', ['medicine_prescribed' => true]);

            $this->pdo->commit();
            
            // Redirect to receptionist payments page (same as start_consultation)
            $_SESSION['success'] = 'Medicine prescribed successfully. Patient needs to make payment.';
            $this->redirect('receptionist/payments');

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to create prescription: ' . $e->getMessage();
            error_log('Prescription error: ' . $e->getMessage()); // Add error logging
            $this->redirect('doctor/lab_results');
        }
    }
}
?>