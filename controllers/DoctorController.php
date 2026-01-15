<?php
require_once __DIR__ . '/../includes/BaseController.php';

class DoctorController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireRole('doctor');
    }

    public function dashboard() {
    // Debug: log incoming POST (helps diagnose empty allocations)
    \Logger::debug('[save_allocation] POST payload: ' . json_encode(array_keys($_POST)));

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

        // Recent lab results - normalize keys expected by the view (status, created_at)
        $stmt = $this->pdo->prepare("
            SELECT
                lr.*, 
                lt.test_name as test_name, 
                p.first_name, p.last_name,
                COALESCE(lto.status, 'unknown') as status,
                COALESCE(lr.completed_at, lto.created_at) as created_at
            FROM lab_results lr
            JOIN lab_test_orders lto ON lr.order_id = lto.id
            JOIN lab_tests lt ON lr.test_id = lt.id
            JOIN consultations c ON lto.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            WHERE c.doctor_id = ?
            ORDER BY COALESCE(lr.completed_at, lto.created_at) DESC
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
                   pv.id as visit_id,
                   pv.visit_date,
                   pv.visit_type,
                   pv.created_at as visit_created_at,
                   COALESCE(cc.consultation_count, 0) as consultation_count,
                   c.status as consultation_status,
                   c.consultation_type,
                   IF(EXISTS(
                       SELECT 1 FROM payments pay 
                       WHERE pay.visit_id = pv.id 
                       AND pay.payment_type IN ('consultation', 'registration')
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
            AND NOT EXISTS (
                SELECT 1 FROM consultations c2 
                WHERE c2.patient_id = p.id 
                AND c2.visit_id = pv.id 
                AND c2.status IN ('in_progress', 'completed', 'cancelled')
            )
            ORDER BY pv.created_at ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $available_patients = $stmt->fetchAll();

                // Pending consultations (registered / scheduled / waiting) for this doctor
                        $stmt = $this->pdo->prepare(
                        "SELECT c.*, p.first_name AS patient_first, p.last_name AS patient_last, p.date_of_birth AS patient_dob, p.phone AS patient_phone,
                         (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv WHERE pv.patient_id = p.id ORDER BY pv.created_at DESC LIMIT 1) AND pay.payment_type IN ('consultation', 'registration') AND pay.payment_status = 'paid'),1,0)) as consultation_registration_paid
                         FROM consultations c
                         JOIN patients p ON c.patient_id = p.id
                         WHERE c.doctor_id = ?
                             AND c.status NOT IN ('completed', 'cancelled')
                         ORDER BY COALESCE(c.created_at, c.follow_up_date) ASC"
                );
                $stmt->execute([$doctor_id]);
                $pending_consultations = $stmt->fetchAll();

        // Prepare stats for the dashboard using the earlier stats query result
        // (the $stats variable was set earlier from the DB query)
        $dashboardStats = [
            'today_consultations' => isset($stats['today_consultations']) ? (int)$stats['today_consultations'] : 0,
            'completed_consultations' => isset($stats['completed_consultations']) ? (int)$stats['completed_consultations'] : 0,
            'total_consultations' => isset($stats['total_consultations']) ? (int)$stats['total_consultations'] : 0,
        ];

        // Normalize available_patients created_at to what the view expects
        foreach ($available_patients as &$p) {
            if (empty($p['created_at']) && !empty($p['visit_created_at'])) {
                $p['created_at'] = $p['visit_created_at'];
            }
        }
        unset($p);

        // Ensure we have a list of other users (doctors, receptionists, lab techs, etc.) for allocation
        $stmt = $this->pdo->prepare(
            "SELECT id, CONCAT(first_name, ' ', last_name) as name, role FROM users WHERE id != ? AND is_active = 1 AND role != 'admin' ORDER BY role, first_name, last_name"
        );
        $stmt->execute([$doctor_id]);
        $other_users = $stmt->fetchAll();

        // For backward compatibility some views reference other_doctors - provide both
        $other_doctors = $other_users;

        // Pass all required variables to the view
        $this->render('doctor/dashboard', [
            'available_patients' => $available_patients,
            'stats' => $dashboardStats,
            'csrf_token' => $this->generateCSRF(),
            'other_doctors' => $other_doctors,
            'other_users' => $other_users,
            'pending_results' => $pending_results ?? [],
            'today_completed' => $today_completed ?? [],
            'recent_results' => $recent_results ?? [],
            'pending_consultations' => $pending_consultations ?? []
        ]);
    }

    public function consultations() {
        $doctor_id = $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("
            SELECT c.*, p.first_name, p.last_name, p.date_of_birth, p.phone,
                   pv.visit_date,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv WHERE pv.patient_id = p.id ORDER BY pv.created_at DESC LIMIT 1) AND pay.payment_type IN ('consultation', 'registration') AND pay.payment_status = 'paid'),1,0)) as consultation_registration_paid,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay2 WHERE pay2.visit_id = (SELECT id FROM patient_visits pv2 WHERE pv2.patient_id = p.id ORDER BY pv2.created_at DESC LIMIT 1) AND pay2.payment_type = 'lab_test' AND pay2.payment_status = 'paid'),1,0)) as lab_tests_paid,
                   (SELECT IF(EXISTS(SELECT 1 FROM payments pay3 WHERE pay3.visit_id = (SELECT id FROM patient_visits pv3 WHERE pv3.patient_id = p.id ORDER BY pv3.created_at DESC LIMIT 1) AND pay3.payment_type = 'lab_test' AND pay3.payment_status = 'paid'),1,0)) as results_review_paid,
                   c.main_complaint,
                   c.diagnosis as final_diagnosis,
                   c.diagnosis as preliminary_diagnosis,
                   COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date
            FROM consultations c
            JOIN patients p ON c.patient_id = p.id
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            WHERE c.doctor_id = ? AND c.status = 'completed'
            ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at) DESC
        ");
        $stmt->execute([$doctor_id]);
        $consultations = $stmt->fetchAll();

        // Pending consultations (awaiting consultation - only status='pending', not in_progress or completed)
        // Show consultations assigned to this doctor OR unassigned consultations (doctor_id = 1)
        $stmt = $this->pdo->prepare("
            SELECT c.*, p.first_name AS patient_first, p.last_name AS patient_last, p.date_of_birth AS patient_dob, p.phone AS patient_phone, pv.visit_date, COALESCE(c.follow_up_date, pv.visit_date, c.created_at) as appointment_date
            FROM consultations c
            JOIN patients p ON c.patient_id = p.id
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            WHERE (c.doctor_id = ? OR c.doctor_id = 1) 
              AND c.status = 'pending'
            ORDER BY COALESCE(c.created_at, c.follow_up_date, pv.visit_date) ASC
        ");
        $stmt->execute([$doctor_id]);
        $pending_consultations = $stmt->fetchAll();

        $this->render('doctor/consultations', [
            'consultations' => $consultations,
            'pending_consultations' => $pending_consultations,
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

    /**
     * Render standalone medical form page (printable) for a specific consultation.
     * URL: /doctor/view_patient_medicalform?id={patient_id}&consultation_id={consultation_id}
     */
    public function view_patient_medicalform($patient_id = null) {
        // Accept either path param or ?id= fallback
        if ($patient_id === null) {
            $patient_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
        }
        if (!$patient_id) {
            $this->redirect('doctor/patients');
        }

        // Optional access check (same as view_patient)
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

        // Get all consultations for this patient (so the view can pick latest if needed)
        $stmt = $this->pdo->prepare("\n            SELECT c.*, pv.visit_date\n            FROM consultations c\n            LEFT JOIN patient_visits pv ON c.visit_id = pv.id\n            WHERE c.patient_id = ? \n            ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at) DESC\n        ");
        $stmt->execute([$patient_id]);
        $consultations = $stmt->fetchAll();

        // Determine which consultation to show
        $consultation_id = filter_input(INPUT_GET, 'consultation_id', FILTER_VALIDATE_INT) ?: null;
        $selected_consultation = null;
        if ($consultation_id) {
            $stmt = $this->pdo->prepare("SELECT * FROM consultations WHERE id = ? AND patient_id = ? LIMIT 1");
            $stmt->execute([$consultation_id, $patient_id]);
            $selected_consultation = $stmt->fetch();
        }

        // Fallback: use the latest consultation if none selected
        if (!$selected_consultation && !empty($consultations)) {
            $selected_consultation = $consultations[0];
            $consultation_id = $selected_consultation['id'] ?? null;
        }

        // Find the visit id to scope vitals and lab orders
        $visit_id = $selected_consultation['visit_id'] ?? null;
        if (!$visit_id) {
            // fallback to latest visit for patient
            $stmt = $this->pdo->prepare("SELECT * FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$patient_id]);
            $latest_visit = $stmt->fetch();
            $visit_id = $latest_visit['id'] ?? null;
        }

        // Get vital signs for this visit (or latest patient-level)
        if (!empty($visit_id)) {
            $stmt = $this->pdo->prepare("\n                SELECT vs.*, pv.visit_date\n                FROM vital_signs vs\n                LEFT JOIN patient_visits pv ON vs.visit_id = pv.id\n                WHERE vs.visit_id = ?\n                ORDER BY vs.recorded_at DESC\n                LIMIT 1\n            ");
            $stmt->execute([$visit_id]);
            $vital_signs = $stmt->fetch();
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

        // Get lab orders for this visit/consultation
        $lab_orders = [];
        if (!empty($visit_id)) {
            $stmt = $this->pdo->prepare("\n                SELECT lto.*, lt.test_name, lt.test_code, lr.result_value, lr.result_text, lr.completed_at as result_completed_at\n                FROM lab_test_orders lto\n                JOIN lab_tests lt ON lto.test_id = lt.id\n                LEFT JOIN lab_results lr ON lto.id = lr.order_id\n                WHERE lto.patient_id = ? AND lto.visit_id = ?" . (!empty($consultation_id) ? " AND lto.consultation_id = ?" : "") . "\n                ORDER BY lto.created_at DESC\n            ");
            if (!empty($consultation_id)) {
                $stmt->execute([$patient_id, $visit_id, $consultation_id]);
            } else {
                $stmt->execute([$patient_id, $visit_id]);
            }
            $lab_orders = $stmt->fetchAll();
        }

        // Build lab results map (most recent per test_name) scoped to visit if possible
        $lab_results_map = [];
        if (!empty($visit_id)) {
            $stmt = $this->pdo->prepare("\n                SELECT lr.*, lt.test_name\n                FROM lab_results lr\n                JOIN lab_test_orders lto ON lr.order_id = lto.id\n                JOIN lab_tests lt ON lr.test_id = lt.id\n                WHERE lto.patient_id = ? AND lto.visit_id = ?\n                ORDER BY lt.test_name ASC, lr.completed_at DESC\n            ");
            $stmt->execute([$patient_id, $visit_id]);
        } else {
            $stmt = $this->pdo->prepare("\n                SELECT lr.*, lt.test_name\n                FROM lab_results lr\n                JOIN lab_test_orders lto ON lr.order_id = lto.id\n                JOIN lab_tests lt ON lr.test_id = lt.id\n                WHERE lto.patient_id = ?\n                ORDER BY lt.test_name ASC, lr.completed_at DESC\n            ");
            $stmt->execute([$patient_id]);
        }
        $all_lab_results = $stmt->fetchAll();
        foreach ($all_lab_results as $r) {
            $name = $r['test_name'] ?? '';
            if ($name === '') continue;
            if (!isset($lab_results_map[$name])) {
                $lab_results_map[$name] = $r;
            }
            $norm = strtolower(preg_replace('/\s+/', '', $name));
            if (!isset($lab_results_map[$norm])) {
                $lab_results_map[$norm] = $r;
            }
        }

        // Render the standalone medical form view
        $this->render('doctor/view_patient_medicalform', [
            'patient' => $patient,
            'consultations' => $consultations,
            'latest_consultation' => $selected_consultation,
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
             (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv2 WHERE pv2.patient_id = p.id ORDER BY pv2.created_at DESC LIMIT 1) AND pay.payment_type IN ('consultation', 'registration') AND pay.payment_status = 'paid'),1,0)) AS consultation_registration_paid,
             (SELECT IF(EXISTS(SELECT 1 FROM payments pay2 WHERE pay2.visit_id = (SELECT id FROM patient_visits pv3 WHERE pv3.patient_id = p.id ORDER BY pv3.created_at DESC LIMIT 1) AND pay2.payment_type = 'lab_test' AND pay2.payment_status = 'paid'),1,0)) AS lab_tests_paid,
             (SELECT IF(EXISTS(SELECT 1 FROM payments pay3 WHERE pay3.visit_id = (SELECT id FROM patient_visits pv4 WHERE pv4.patient_id = p.id ORDER BY pv4.created_at DESC LIMIT 1) AND pay3.payment_type = 'lab_test' AND pay3.payment_status = 'paid'),1,0)) AS results_review_paid
         FROM patients p
            LEFT JOIN (
                SELECT patient_id, COUNT(*) as consultation_count
                FROM consultations
                WHERE doctor_id = ?
                GROUP BY patient_id
            ) consultation_counts ON p.id = consultation_counts.patient_id
                WHERE (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = (SELECT id FROM patient_visits pv WHERE pv.patient_id = p.id ORDER BY pv.created_at DESC LIMIT 1) AND pay.payment_type IN ('consultation', 'registration') AND pay.payment_status = 'paid'),1,0)) = 1
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
    error_log('[start_consultation] METHOD CALLED - REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log('[start_consultation] Not POST request, redirecting to dashboard');
        $this->redirect('doctor/dashboard');
        return;
    }

    $this->validateCSRF();

    $patient_id = $_POST['patient_id'];
    $doctor_id = $_SESSION['user_id'];
    $next_step = $_POST['next_step'] ?? '';

    // Determine latest visit for patient
    $stmt = $this->pdo->prepare("SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$patient_id]);
    $row = $stmt->fetch();
    $visit_id = $row['id'] ?? null;

    if (!$visit_id) {
        $_SESSION['error'] = 'No visit found for this patient';
        $this->redirect('doctor/view_patient/' . $patient_id);
        return;
    }

    // Check if doctor can attend this visit
    $can = $this->canAttend($visit_id);
    if (!$can['ok']) {
        $_SESSION['error'] = 'Cannot start consultation: ' . $can['reason'];
        $this->redirect('doctor/view_patient/' . $patient_id);
        return;
    }

    // Start or resume consultation using BaseController helper
    $start = $this->startConsultation($visit_id, $doctor_id);
    if (!$start['ok']) {
        $_SESSION['error'] = 'Failed to start consultation: ' . ($start['message'] ?? $start['reason'] ?? 'unknown');
        $this->redirect('doctor/view_patient/' . $patient_id);
        return;
    }

    $consultation_id = $start['consultation_id'];

    // Debug logging: record ALL incoming POST data for troubleshooting
    error_log('[start_consultation] ========== FORM SUBMISSION ==========');
    error_log('[start_consultation] patient_id: ' . $patient_id);
    error_log('[start_consultation] next_step: ' . $next_step);
    error_log('[start_consultation] selected_tests: ' . ($_POST['selected_tests'] ?? '<empty>'));
    error_log('[start_consultation] selected_medicines: ' . ($_POST['selected_medicines'] ?? '<empty>'));
    error_log('[start_consultation] selected_allocations: ' . ($_POST['selected_allocations'] ?? '<empty>'));
    error_log('[start_consultation] main_complaint: ' . ($_POST['main_complaint'] ?? '<empty>'));
    error_log('[start_consultation] on_examination: ' . ($_POST['on_examination'] ?? '<empty>'));

    // Now proceed to handle submitted consultation details
    try {
        $this->pdo->beginTransaction();

        // Update the consultation record with submitted data
        // Use information_schema to avoid unknown-column errors on different schemas
        $stmtCols = $this->pdo->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'consultations'");
        $stmtCols->execute();
        $cols = array_column($stmtCols->fetchAll(PDO::FETCH_ASSOC), 'COLUMN_NAME');

        $updateParts = [];
        $params = [];

        if (in_array('main_complaint', $cols)) {
            $updateParts[] = 'main_complaint = ?';
            $params[] = $_POST['main_complaint'] ?? '';
        }
        if (in_array('on_examination', $cols)) {
            $updateParts[] = 'on_examination = ?';
            $params[] = $_POST['on_examination'] ?? '';
        }

        // Handle preliminary diagnosis (both ID and text for backward compatibility)
        if (in_array('preliminary_diagnosis_id', $cols) && !empty($_POST['preliminary_diagnosis_id'])) {
            $updateParts[] = 'preliminary_diagnosis_id = ?';
            $params[] = $_POST['preliminary_diagnosis_id'];
            
            // Also store the diagnosis name in the text field for backward compatibility
            if (in_array('preliminary_diagnosis', $cols)) {
                $diagStmt = $this->pdo->prepare("SELECT name FROM icd_codes WHERE id = ?");
                $diagStmt->execute([$_POST['preliminary_diagnosis_id']]);
                $diagName = $diagStmt->fetchColumn();
                if ($diagName) {
                    $updateParts[] = 'preliminary_diagnosis = ?';
                    $params[] = $diagName;
                }
            }
        } elseif (in_array('preliminary_diagnosis', $cols) && !empty($_POST['preliminary_diagnosis'])) {
            // Fallback to text-only if no ID provided
            $updateParts[] = 'preliminary_diagnosis = ?';
            $params[] = $_POST['preliminary_diagnosis'];
        } elseif (in_array('diagnosis', $cols) && !empty($_POST['preliminary_diagnosis'])) {
            $updateParts[] = 'diagnosis = ?';
            $params[] = $_POST['preliminary_diagnosis'];
        }

        // Handle final diagnosis (both ID and text for backward compatibility)
        if (in_array('final_diagnosis_id', $cols) && !empty($_POST['final_diagnosis_id'])) {
            $updateParts[] = 'final_diagnosis_id = ?';
            $params[] = $_POST['final_diagnosis_id'];
            
            // Also store the diagnosis name in the text field for backward compatibility
            if (in_array('final_diagnosis', $cols)) {
                $diagStmt = $this->pdo->prepare("SELECT name FROM icd_codes WHERE id = ?");
                $diagStmt->execute([$_POST['final_diagnosis_id']]);
                $diagName = $diagStmt->fetchColumn();
                if ($diagName) {
                    $updateParts[] = 'final_diagnosis = ?';
                    $params[] = $diagName;
                }
            }
        } elseif (in_array('final_diagnosis', $cols) && !empty($_POST['final_diagnosis'])) {
            // Fallback to text-only if no ID provided
            $updateParts[] = 'final_diagnosis = ?';
            $params[] = $_POST['final_diagnosis'] ?? ($_POST['diagnosis'] ?? '');
        } elseif (in_array('diagnosis', $cols) && empty($_POST['preliminary_diagnosis']) && !empty($_POST['final_diagnosis'])) {
            // if preliminary wasn't provided but final is, and only 'diagnosis' exists, use it
            $updateParts[] = 'diagnosis = ?';
            $params[] = $_POST['final_diagnosis'];
        }

        if (in_array('treatment_plan', $cols)) {
            $updateParts[] = 'treatment_plan = ?';
            $params[] = $_POST['treatment_plan'] ?? '';
        }

        // Don't mark as completed yet - consultation will be marked complete only when there are no pending payments/tests/medicines
        // Only update the updated_at timestamp
        if (in_array('updated_at', $cols)) {
            $updateParts[] = 'updated_at = NOW()';
        }

        if (!empty($updateParts)) {
            $sql = 'UPDATE consultations SET ' . implode(', ', $updateParts) . ' WHERE id = ?';
            $params[] = $consultation_id;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        }

        // Handle selected lab tests
        if (!empty($_POST['selected_tests']) && 
            ($next_step === 'lab_tests' || $next_step === 'lab_medicine' || $next_step === 'all')) {
            
            $selected_tests = json_decode($_POST['selected_tests'], true);
            if (is_array($selected_tests)) {
                // Find a lab technician for assignment
                $stmtTech = $this->pdo->prepare("
                    SELECT id FROM users 
                    WHERE role = 'lab_technician' AND is_active = 1 
                    LIMIT 1
                ");
                $stmtTech->execute();
                $technician = $stmtTech->fetch();
                $technician_id = $technician['id'] ?? null;

                // Create lab test orders
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
                    
                    // Get test price to create pending payment record
                    $stmtTest = $this->pdo->prepare("SELECT price FROM lab_tests WHERE id = ?");
                    $stmtTest->execute([$test_id]);
                    $test = $stmtTest->fetch();
                    $test_price = $test['price'] ?? 0;
                    
                    if ($test_price > 0) {
                        // Create pending payment record for this test
                        $stmtPayment = $this->pdo->prepare("
                            INSERT INTO payments (visit_id, patient_id, payment_type, item_id, item_type, amount, payment_status, created_at, updated_at)
                            VALUES (?, ?, 'lab_test', ?, 'lab_test', ?, 'pending', NOW(), NOW())
                        ");
                        $stmtPayment->execute([$visit_id, $patient_id, $test_id, $test_price]);
                    }
                }
                
                $this->updateWorkflowStatus($patient_id, 'pending_payment', ['lab_tests_ordered' => true]);
            }
        }

        // Handle selected medicines
        if (!empty($_POST['selected_medicines']) && 
            ($next_step === 'medicine' || $next_step === 'lab_medicine' || $next_step === 'all')) {
            
            $selected_medicines = json_decode($_POST['selected_medicines'], true);
            if (is_array($selected_medicines)) {
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
                        $medicine_data['frequency'] ?? 'Once daily',
                        $medicine_data['duration'] ?? 1,
                        $medicine_data['instructions'] ?? '',
                    ]);
                    
                    // Get medicine price to create pending payment record
                    $stmtMed = $this->pdo->prepare("SELECT unit_price FROM medicines WHERE id = ?");
                    $stmtMed->execute([$medicine_data['id']]);
                    $medicine = $stmtMed->fetch();
                    $medicine_unit_price = $medicine['unit_price'] ?? 0;
                    $medicine_total_price = $medicine_unit_price * ($medicine_data['quantity'] ?? 1);
                    
                    if ($medicine_total_price > 0) {
                        // Create pending payment record for this medicine
                        $stmtPayment = $this->pdo->prepare("
                            INSERT INTO payments (visit_id, patient_id, payment_type, item_id, item_type, amount, payment_status, created_at, updated_at)
                            VALUES (?, ?, 'medicine', ?, 'medicine', ?, 'pending', NOW(), NOW())
                        ");
                        $stmtPayment->execute([$visit_id, $patient_id, $medicine_data['id'], $medicine_total_price]);
                    }
                }
                
                $this->updateWorkflowStatus($patient_id, 'pending_payment', ['medicine_prescribed' => true]);
            }
        }

        // Handle service allocations (NEW FUNCTIONALITY)
        if (!empty($_POST['selected_allocations']) && 
            ($next_step === 'allocation' || $next_step === 'all')) {
            
            $selected_allocations = json_decode($_POST['selected_allocations'], true);
            if (is_array($selected_allocations)) {
                
                foreach ($selected_allocations as $allocation) {
                    $service_id = $allocation['service_id'] ?? null;
                    $performed_by = $allocation['assigned_to'] ?? null;
                    $notes = $allocation['instructions'] ?? '';

                    if (!$service_id) continue;

                    // Get service details for payment
                    $stmt = $this->pdo->prepare("
                        SELECT id, service_name, price 
                        FROM services 
                        WHERE id = ? AND is_active = 1
                    ");
                    $stmt->execute([$service_id]);
                    $service = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$service) continue;

                    // Create service order
                    $stmt = $this->pdo->prepare("
                        INSERT INTO service_orders (
                            visit_id, patient_id, service_id, 
                            ordered_by, performed_by, 
                            status, notes, created_at, updated_at
                        ) VALUES (
                            ?, ?, ?, 
                            ?, ?, 
                            'pending', ?, NOW(), NOW()
                        )
                    ");
                    $stmt->execute([
                        $visit_id, 
                        $patient_id, 
                        $service_id,
                        $doctor_id, 
                        $performed_by,
                        $notes
                    ]);

                    $order_id = $this->pdo->lastInsertId();
                    
                    // Create pending payment record for this service
                    if ($service['price'] > 0) {
                        $stmtPayment = $this->pdo->prepare("
                            INSERT INTO payments (visit_id, patient_id, payment_type, item_id, item_type, amount, payment_status, created_at, updated_at)
                            VALUES (?, ?, 'service', ?, 'service', ?, 'pending', NOW(), NOW())
                        ");
                        $stmtPayment->execute([$visit_id, $patient_id, $service_id, $service['price']]);
                    }
                    
                    // Send notification if staff assigned
                    if ($performed_by) {
                        $this->sendAllocationNotification([
                            'id' => $order_id,
                            'service_id' => $service_id,
                            'service_name' => $service['service_name'],
                            'performed_by' => $performed_by
                        ], $patient_id);
                    }
                }
                
                $this->updateWorkflowStatus($patient_id, 'services_allocated');
            }
        }

        // Final workflow update
        $has_lab_tests = !empty($_POST['selected_tests']) && 
                        ($next_step === 'lab_tests' || $next_step === 'lab_medicine' || $next_step === 'all');
        $has_medicines = !empty($_POST['selected_medicines']) && 
                        ($next_step === 'medicine' || $next_step === 'lab_medicine' || $next_step === 'all');
        $has_allocations = !empty($_POST['selected_allocations']) && 
                          ($next_step === 'allocation' || $next_step === 'all');
        
        if (!$has_lab_tests && !$has_medicines && !$has_allocations) {
            // Only mark consultation as 'completed' when there's nothing else to do (discharge case)
            $stmt = $this->pdo->prepare("
                UPDATE consultations 
                SET status = 'completed', completed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$consultation_id]);
            
            $this->updateWorkflowStatus($patient_id, 'completed');
            // Note: patient_visits.status remains 'active' until patient is discharged (entire journey complete)
        }

        $this->pdo->commit();
        
        // Redirect based on what was ordered
        if ($has_lab_tests || $has_medicines || $has_allocations) {
            $_SESSION['success'] = 'Consultation completed successfully. Patient needs to make payment.';
            $this->redirect('receptionist/payments');
        } else {
            $_SESSION['success'] = 'Consultation completed and patient discharged successfully';
            $this->redirect('doctor/dashboard');
        }
        
    } catch (Exception $e) {
        $this->pdo->rollBack();
        $_SESSION['error'] = 'Failed to complete consultation: ' . $e->getMessage();
        error_log('Consultation error: ' . $e->getMessage());
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

    /**
     * Search ICD diagnosis codes (AJAX JSON)
     * Returns standardized diagnosis codes for NMCP compliance
     */
    public function search_diagnoses() {
        // Check authentication
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'doctor') {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $query = $_GET['q'] ?? '';

        if (strlen($query) < 2) {
            // Return common diagnoses if no search query
            try {
                $stmt = $this->pdo->prepare("
                    SELECT id, code, name, category, description
                    FROM icd_codes
                    WHERE is_active = 1
                    ORDER BY name
                    LIMIT 20
                ");
                $stmt->execute();
                $diagnoses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                header('Content-Type: application/json');
                echo json_encode($diagnoses);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Database error']);
            }
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("
                SELECT id, code, name, category, description
                FROM icd_codes
                WHERE is_active = 1 
                  AND (name LIKE ? OR code LIKE ? OR description LIKE ? OR category LIKE ?)
                ORDER BY 
                  CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN code LIKE ? THEN 2
                    ELSE 3
                  END,
                  name
                LIMIT 50
            ");
            $search = "%{$query}%";
            $searchStart = "{$query}%";
            $stmt->execute([$search, $search, $search, $search, $searchStart, $searchStart]);
            $diagnoses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($diagnoses);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            error_log('search_diagnoses error: ' . $e->getMessage());
        }
        exit;
    }

    /**
     * Search services for allocation modal (AJAX JSON)
     */
    public function search_services() {
        $q = trim($_GET['q'] ?? '');
        try {
            if ($q === '') {
                $stmt = $this->pdo->prepare("SELECT id, service_name as name, price, description, service_code FROM services WHERE is_active = 1 ORDER BY service_name LIMIT 50");
                $stmt->execute();
            } else {
                $term = '%' . str_replace('%','\\%',$q) . '%';
                $stmt = $this->pdo->prepare("SELECT id, service_name as name, price, description, service_code FROM services WHERE is_active = 1 AND (service_name LIKE ? OR service_code LIKE ? OR description LIKE ?) ORDER BY service_name LIMIT 50");
                $stmt->execute([$term, $term, $term]);
            }
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($services);
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8', true, 500);
            echo json_encode(['error' => 'Failed to search services']);
            error_log('search_services error: ' . $e->getMessage());
        }
        exit;
    }

    /**
     * Search staff (users) by name or id for allocation suggestions (AJAX JSON)
     */
    public function search_staff() {
        $q = trim($_GET['q'] ?? '');
        try {
            if ($q === '') {
                echo json_encode([]);
                exit;
            }
            $term = '%' . str_replace('%','\\%',$q) . '%';
            $stmt = $this->pdo->prepare("SELECT id, first_name, last_name, role FROM users WHERE is_active = 1 AND (CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ? OR id LIKE ?) ORDER BY first_name LIMIT 30");
            $stmt->execute([$term, $term, $term]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($users);
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8', true, 500);
            echo json_encode(['error' => 'Failed to search staff']);
            error_log('search_staff error: ' . $e->getMessage());
        }
        exit;
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

    public function allocated_services() {
        $doctor_id = $_SESSION['user_id'];

        // Get all service orders allocated by this doctor
        $stmt = $this->pdo->prepare("
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
                s.price,
                u.first_name as staff_first,
                u.last_name as staff_last,
                u.role as staff_role,
                (SELECT COUNT(*) FROM payments 
                 WHERE item_id = so.id 
                 AND item_type = 'service_order' 
                 AND payment_status = 'paid') as payment_count
            FROM service_orders so
            JOIN patients p ON so.patient_id = p.id
            JOIN services s ON so.service_id = s.id
            LEFT JOIN patient_visits pv ON so.visit_id = pv.id
            LEFT JOIN users u ON so.performed_by = u.id
            WHERE so.ordered_by = ?
            ORDER BY so.created_at DESC
        ");
        $stmt->execute([$doctor_id]);
        $allocations = $stmt->fetchAll();

        // Count by status
        $pending_count = 0;
        $in_progress_count = 0;
        $completed_count = 0;
        $unpaid_count = 0;

        foreach ($allocations as $allocation) {
            if ($allocation['status'] === 'pending') $pending_count++;
            if ($allocation['status'] === 'in_progress') $in_progress_count++;
            if ($allocation['status'] === 'completed') $completed_count++;
            if ($allocation['payment_count'] == 0) $unpaid_count++;
        }

        $this->render('doctor/allocated_services', [
            'allocations' => $allocations,
            'pending_count' => $pending_count,
            'in_progress_count' => $in_progress_count,
            'completed_count' => $completed_count,
            'unpaid_count' => $unpaid_count,
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

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE visit_id = ? AND payment_type IN ('consultation', 'registration') AND payment_status = 'paid'");
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

        // Check workflow access (payment verification)
        $access_check = $this->checkWorkflowAccess($patient_id, 'consultation');
        
        // If not paid, check if doctor is providing an override reason
        if (!$access_check['access']) {
            // Check if this is a POST with override reason
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['override_reason'])) {
                $this->validateCSRF($_POST['csrf_token'] ?? '');
                $override_reason = trim($_POST['override_reason'] ?? '');
                
                if (empty($override_reason)) {
                    $_SESSION['error'] = 'Please provide a reason for attending unpaid patient';
                    $this->redirect('doctor/payment_required?patient_id=' . $patient_id);
                    return;
                }
                
                // Log the override for audit
                $this->logPaymentOverride($patient_id, $override_reason);
                
                // Continue to attend patient with override flag
                $_SESSION['payment_override'] = [
                    'patient_id' => $patient_id,
                    'reason' => $override_reason,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'doctor_id' => $_SESSION['user_id']
                ];
            } else {
                // Redirect to payment required page with reason form
                $this->redirect('doctor/payment_required?patient_id=' . $patient_id);
                return;
            }
        }

        // Find latest active visit for this patient to attach allocations/orders
        $stmt = $this->pdo->prepare("SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$patient_id]);
        $latest_visit_row = $stmt->fetch();
        $latest_visit_id = $latest_visit_row['id'] ?? null;

        // Mark consultation as 'in_progress' when doctor starts attending
        // This removes the patient from the "waiting" list
        $consultation = null;
        $is_reopening = false;
        
        if ($latest_visit_id) {
            // Find or create consultation for this visit
            $stmt = $this->pdo->prepare("
                SELECT * FROM consultations 
                WHERE patient_id = ? AND visit_id = ? 
                ORDER BY created_at DESC LIMIT 1
            ");
            $stmt->execute([$patient_id, $latest_visit_id]);
            $consultation = $stmt->fetch();
            
            if (!$consultation) {
                // Create new consultation if doesn't exist
                $stmt = $this->pdo->prepare("
                    INSERT INTO consultations 
                    (visit_id, patient_id, doctor_id, status, started_at, created_at, updated_at)
                    VALUES (?, ?, ?, 'in_progress', NOW(), NOW(), NOW())
                ");
                $stmt->execute([$latest_visit_id, $patient_id, $_SESSION['user_id']]);
                
                // Fetch the newly created consultation
                $stmt = $this->pdo->prepare("
                    SELECT * FROM consultations 
                    WHERE patient_id = ? AND visit_id = ? 
                    ORDER BY created_at DESC LIMIT 1
                ");
                $stmt->execute([$patient_id, $latest_visit_id]);
                $consultation = $stmt->fetch();
            } else {
                // Mark existing as in_progress and note that this is a reopening
                if ($consultation['status'] === 'in_progress') {
                    $is_reopening = true;
                }
                
                $stmt = $this->pdo->prepare("
                    UPDATE consultations 
                    SET status = 'in_progress', started_at = COALESCE(started_at, NOW()), updated_at = NOW()
                    WHERE id = ? AND status IN ('pending', 'in_progress')
                ");
                $stmt->execute([$consultation['id']]);
            }
        }

        // Fetch previous chief complaints for patient history reference
        $stmt = $this->pdo->prepare("
            SELECT c.main_complaint, c.created_at, c.preliminary_diagnosis, c.final_diagnosis,
                   CONCAT(u.first_name, ' ', u.last_name) as doctor_name
            FROM consultations c
            LEFT JOIN users u ON c.doctor_id = u.id
            WHERE c.patient_id = ? 
              AND c.main_complaint IS NOT NULL 
              AND c.main_complaint != ''
            ORDER BY c.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$patient_id]);
        $previous_complaints = $stmt->fetchAll();

        $this->render('doctor/attend_patient', [
            'patient' => $patient,
            'csrf_token' => $this->generateCSRF(),
            'visit_id' => $latest_visit_id,
            'payment_override' => $_SESSION['payment_override'] ?? null,
            'consultation' => $consultation,
            'is_reopening' => $is_reopening,
            'previous_complaints' => $previous_complaints
        ]);
        
        // Clear the override session after use
        unset($_SESSION['payment_override']);
    }

    /**
     * Log payment override for audit purposes
     */
    private function logPaymentOverride($patient_id, $reason) {
        try {
            // Get latest visit
            $stmt = $this->pdo->prepare("SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$patient_id]);
            $visit = $stmt->fetch();
            $visit_id = $visit['id'] ?? null;
            
            // Check if consultation_overrides table exists, create if not
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'consultation_overrides'");
            if ($stmt->rowCount() === 0) {
                $this->pdo->exec("
                    CREATE TABLE IF NOT EXISTS `consultation_overrides` (
                        `id` int NOT NULL AUTO_INCREMENT,
                        `patient_id` int NOT NULL,
                        `visit_id` int DEFAULT NULL,
                        `doctor_id` int NOT NULL,
                        `override_reason` text COLLATE utf8mb4_general_ci NOT NULL,
                        `override_type` enum('payment_bypass','emergency','other') DEFAULT 'payment_bypass',
                        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        KEY `idx_patient_id` (`patient_id`),
                        KEY `idx_doctor_id` (`doctor_id`),
                        KEY `idx_created_at` (`created_at`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
                ");
            }
            
            // Insert override record
            $stmt = $this->pdo->prepare("
                INSERT INTO consultation_overrides (patient_id, visit_id, doctor_id, override_reason, override_type)
                VALUES (?, ?, ?, ?, 'payment_bypass')
            ");
            $stmt->execute([$patient_id, $visit_id, $_SESSION['user_id'], $reason]);
            
            \Logger::info("Payment override: Doctor {$_SESSION['user_id']} attended patient {$patient_id} without payment. Reason: {$reason}");
            
        } catch (Exception $e) {
            \Logger::error("Failed to log payment override: " . $e->getMessage());
        }
    }

    /**
     * Show payment required page with override option
     */
    public function payment_required() {
        $patient_id = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT);
        
        if (!$patient_id) {
            $_SESSION['error'] = 'Invalid patient ID';
            $this->redirect('doctor/dashboard');
            return;
        }
        
        // Get patient details
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch();
        
        if (!$patient) {
            $_SESSION['error'] = 'Patient not found';
            $this->redirect('doctor/dashboard');
            return;
        }
        
        // Get payment status
        $stmt = $this->pdo->prepare("
            SELECT pv.id as visit_id, pv.visit_date, pv.visit_type,
                   pay.payment_status, pay.amount, pay.payment_type
            FROM patient_visits pv
            LEFT JOIN payments pay ON pay.visit_id = pv.id AND pay.payment_type IN ('consultation', 'registration')
            WHERE pv.patient_id = ?
            ORDER BY pv.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$patient_id]);
        $visit_payment = $stmt->fetch();
        
        $this->render('doctor/payment_required', [
            'patient' => $patient,
            'visit_payment' => $visit_payment,
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

    /**
     * Allocate Services to Other Users
     * Doctor hands over/delegates services (lab tests, nursing, etc.) to other staff
     * Creates records in service_orders table
     */
    public function allocate_resources($patient_id = null) {
        // Accept either path param or ?id= fallback
        if ($patient_id === null) {
            $patient_id = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT) ?: null;
        }
        if (!$patient_id) {
            $this->redirect('doctor/patients');
        }

        // Get patient details
        $stmt = $this->pdo->prepare("
            SELECT p.*, 
                   (SELECT COUNT(*) FROM patient_visits WHERE patient_id = p.id) as total_visits,
                   (SELECT MAX(visit_date) FROM patient_visits WHERE patient_id = p.id) as last_visit
            FROM patients p
            WHERE p.id = ?
        ");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch();

        if (!$patient) {
            $_SESSION['error'] = 'Patient not found';
            $this->redirect('doctor/patients');
        }

        // Get patient's active visit (for service_orders)
        $stmt = $this->pdo->prepare("
            SELECT * FROM patient_visits 
            WHERE patient_id = ? AND status = 'active'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$patient_id]);
        $active_visit = $stmt->fetch();

        // Get available services
        $stmt = $this->pdo->prepare("
            SELECT * FROM services 
            WHERE is_active = 1
            ORDER BY service_name ASC
        ");
        $stmt->execute();
        $available_services = $stmt->fetchAll();

        // Get available staff members (all non-admin, non-doctor users for allocation)
        // This allows doctors to delegate to lab techs, nurses, other doctors, etc.
        $stmt = $this->pdo->prepare("
            SELECT id, CONCAT(first_name, ' ', last_name) as staff_name, role, specialization
            FROM users 
            WHERE is_active = 1 AND role != 'admin'
            ORDER BY role, first_name ASC
        ");
        $stmt->execute();
        $available_staff = $stmt->fetchAll();

        // Get pending service orders for this patient (to avoid duplicates)
        $stmt = $this->pdo->prepare("
            SELECT so.*, s.service_name, u.first_name, u.last_name, u.role
            FROM service_orders so
            JOIN services s ON so.service_id = s.id
            JOIN users u ON so.performed_by = u.id
            WHERE so.patient_id = ? AND so.status IN ('pending', 'in_progress')
            ORDER BY so.created_at DESC
        ");
        $stmt->execute([$patient_id]);
        $pending_orders = $stmt->fetchAll();

        $this->render('doctor/allocate_resources', [
            'patient' => $patient,
            'active_visit' => $active_visit,
            'available_services' => $available_services,
            'available_staff' => $available_staff,
            'pending_orders' => $pending_orders,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * Save Service Allocation
     * Process the allocation form and create service_orders records
     */
    public function save_allocation() {
    // Set JSON header first
    header('Content-Type: application/json');
    
    // Validate CSRF
    try {
        $this->validateCSRF($_POST['csrf_token'] ?? null);
    } catch (Exception $e) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'CSRF token invalid']);
        exit;
    }

    $doctor_id = $_SESSION['user_id'];
    $patient_id = filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT);
    $visit_id = filter_input(INPUT_POST, 'visit_id', FILTER_VALIDATE_INT);
    $allocations = json_decode($_POST['allocations'] ?? '[]', true);

    if (!$patient_id || !$visit_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    if (empty($allocations) || !is_array($allocations)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No allocations provided']);
        exit;
    }

    try {
        $this->pdo->beginTransaction();

        $created_orders = [];

        foreach ($allocations as $allocation) {
            $service_id = filter_var($allocation['service_id'] ?? null, FILTER_VALIDATE_INT);
            $performed_by = filter_var($allocation['assigned_to'] ?? null, FILTER_VALIDATE_INT);
            $service_notes = $allocation['instructions'] ?? '';

            if (!$service_id) {
                continue;
            }

            // Get service details
            $stmt = $this->pdo->prepare("
                SELECT id, service_name, price 
                FROM services 
                WHERE id = ? AND is_active = 1
            ");
            $stmt->execute([$service_id]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$service) {
                continue;
            }

            // Don't create pending payment records - payments should only be created when actually received
            // The service_orders table is the source of truth for services that need payment

            // Verify staff if assigned
            $staff = null;
            if ($performed_by) {
                $stmt = $this->pdo->prepare("
                    SELECT id, email, first_name, last_name 
                    FROM users 
                    WHERE id = ? AND is_active = 1
                ");
                $stmt->execute([$performed_by]);
                $staff = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Create service order
            $stmt = $this->pdo->prepare("
                INSERT INTO service_orders (
                    visit_id, patient_id, service_id, 
                    ordered_by, performed_by, 
                    status, notes, created_at, updated_at
                ) VALUES (
                    ?, ?, ?, 
                    ?, ?, 
                    'pending', ?, NOW(), NOW()
                )
            ");
            $stmt->execute([
                $visit_id, $patient_id, $service_id,
                $doctor_id, $performed_by,
                $service_notes
            ]);

            $order_id = $this->pdo->lastInsertId();
            
            $created_orders[] = [
                'id' => $order_id,
                'service_id' => $service_id,
                'service_name' => $service['service_name'],
                'performed_by' => $performed_by,
                'staff_email' => $staff['email'] ?? null,
                'staff_name' => $staff ? ($staff['first_name'] . ' ' . $staff['last_name']) : null
            ];

            // Send notification if staff assigned
            if ($staff) {
                $this->sendAllocationNotification([
                    'id' => $order_id,
                    'service_id' => $service_id,
                    'service_name' => $service['service_name'],
                    'performed_by' => $performed_by,
                    'staff_email' => $staff['email'],
                    'staff_name' => $staff['first_name'] . ' ' . $staff['last_name']
                ], $patient_id);
            }
        }

        // Update workflow
        $this->updateWorkflowStatus($patient_id, 'services_allocated', [
            'allocated_count' => count($created_orders),
            'allocated_by' => $doctor_id
        ]);

        $this->pdo->commit();

        $message = count($created_orders) . ' service(s) allocated successfully.';
        if (count($created_orders) > 0) {
            $message .= ' Pending payments created for receptionist.';
        }

        $_SESSION['success'] = $message;
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'orders_created' => count($created_orders),
            'patient_id' => $patient_id
        ]);

    } catch (Exception $e) {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        
        $_SESSION['error'] = 'Failed to allocate services: ' . $e->getMessage();
        error_log('Allocation error: ' . $e->getMessage());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to allocate services: ' . $e->getMessage()
        ]);
    }
    exit;
}
    /**
     * Create a new service via AJAX
     */
    public function create_service() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        // Validate CSRF (throws Exception on failure)
        try {
            $this->validateCSRF($_POST['csrf_token'] ?? null);
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF token invalid']);
            exit;
        }

        $name = trim($_POST['service_name'] ?? '');
        $code = trim($_POST['service_code'] ?? '');
        $price = $_POST['service_price'] ?? 0;
        // Normalize price to float
        $price = is_numeric($price) ? (float)$price : 0.0;
        $description = trim($_POST['service_description'] ?? '');
        $requires_doctor = isset($_POST['service_requires_doctor']) ? 1 : 0;

        // Validate required fields
        if ($name === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Service name is required']);
            exit;
        }
        if ($code === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Service code is required']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("INSERT INTO services (service_name, service_code, price, description, requires_doctor, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())");
            $stmt->execute([$name, $code, $price, $description, $requires_doctor]);
            $id = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare("SELECT id, service_name, service_code, price, description, requires_doctor, is_active FROM services WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->pdo->commit();

            echo json_encode(['success' => true, 'service' => $service]);
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            // Handle duplicate service_code (unique constraint)
            $sqlState = $e->errorInfo[0] ?? '';
            if ($sqlState === '23000') {
                http_response_code(409);
                echo json_encode(['error' => 'Service code already exists']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create service: ' . $e->getMessage()]);
            }
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create service: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Cancel Service Order
     * Doctor can cancel pending allocations
     */
    public function cancel_service_order() {
        if (!$this->validateCSRF($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF token invalid']);
            exit;
        }

        $doctor_id = $_SESSION['user_id'];
        $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        $cancellation_reason = $_POST['cancellation_reason'] ?? '';

        if (!$order_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Order ID required']);
            exit;
        }

        try {
            // Verify the order exists and belongs to a patient this doctor worked with
            $stmt = $this->pdo->prepare("
                SELECT so.*, c.doctor_id 
                FROM service_orders so
                LEFT JOIN consultations c ON so.patient_id = c.patient_id
                WHERE so.id = ? AND so.status != 'cancelled'
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch();

            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found or already cancelled']);
                exit;
            }

            // Update order status
            $stmt = $this->pdo->prepare("
                UPDATE service_orders 
                SET status = 'cancelled', 
                    cancellation_reason = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$cancellation_reason, $order_id]);

            $_SESSION['success'] = 'Service order cancelled successfully';
            echo json_encode(['success' => true, 'message' => $_SESSION['success']]);

        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to cancel service order: ' . $e->getMessage();
            http_response_code(500);
            echo json_encode(['error' => $_SESSION['error']]);
        }
        exit;
    }

    /**
     * Send allocation notification to staff
     */
    private function sendAllocationNotification($order, $patient_id) {
        try {
            // Get patient info
            $stmt = $this->pdo->prepare("SELECT first_name, last_name FROM patients WHERE id = ?");
            $stmt->execute([$patient_id]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$patient) return;

            $patient_name = $patient['first_name'] . ' ' . $patient['last_name'];

            // Check if notifications table exists
            $stmt = $this->pdo->prepare("
                SELECT 1 FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'notifications'
            ");
            $stmt->execute();
            $table_exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($table_exists) {
                // Create system notification
                $notification_message = "You have been allocated: {$order['service_name']} for patient {$patient_name}";
                $stmt = $this->pdo->prepare("
                    INSERT INTO notifications (
                        user_id, type, title, message, 
                        related_id, related_type, 
                        is_read, created_at
                    ) VALUES (
                        ?, ?, ?, ?,
                        ?, ?,
                        0, NOW()
                    )
                ");
                $stmt->execute([
                    $order['performed_by'],
                    'service_allocation',
                    'New Service Allocated',
                    $notification_message,
                    $order['id'],
                    'service_order'
                ]);
            }

            // Log allocation in activities if table exists
            $stmt = $this->pdo->prepare("
                SELECT 1 FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'activity_logs'
            ");
            $stmt->execute();
            $log_table_exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($log_table_exists) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO activity_logs (
                        user_id, action, description, 
                        entity_type, entity_id,
                        ip_address, created_at
                    ) VALUES (
                        ?, ?, ?,
                        ?, ?,
                        ?, NOW()
                    )
                ");
                $stmt->execute([
                    $_SESSION['user_id'] ?? null,
                    'service_allocated',
                    "Allocated {$order['service_name']} to {$order['staff_name']} for patient {$patient_name}",
                    'service_order',
                    $order['id'],
                    $_SERVER['REMOTE_ADDR'] ?? null
                ]);
            }

        } catch (Exception $e) {
            // Silently fail - notification is non-critical
            error_log("Notification error: " . $e->getMessage());
        }
    }
}
?>