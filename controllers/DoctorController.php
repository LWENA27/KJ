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

                // Today's completed consultations (check both created_at and appointment_date for robustness)
                                $stmt = $this->pdo->prepare("                        SELECT c.*, p.first_name, p.last_name, p.date_of_birth, p.phone
                                                FROM consultations c
                                                JOIN patients p ON c.patient_id = p.id
                                                WHERE c.doctor_id = ?
                                                    AND c.status = 'completed'
                                                    AND (
                                                                        DATE(c.created_at) = CURDATE()
                                                                        OR DATE(c.appointment_date) = CURDATE()
                                                                    )
                                                ORDER BY COALESCE(c.created_at, c.appointment_date) DESC
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
        $stmt = $this->pdo->prepare("            SELECT p.*, ws.current_step,
                   ag.test_names,
                   ag.latest_result_date as result_date
            FROM patients p
            JOIN workflow_status ws ON p.id = ws.patient_id
            LEFT JOIN (
                SELECT c.patient_id,
               GROUP_CONCAT(lt.test_name SEPARATOR ', ') AS test_names,
               MAX(lr.created_at) AS latest_result_date
                FROM lab_results lr
                JOIN consultations c ON lr.consultation_id = c.id
                JOIN lab_tests lt ON lr.test_id = lt.id
                WHERE lr.status = 'completed' AND c.doctor_id = ?
                GROUP BY c.patient_id
            ) ag ON p.id = ag.patient_id
            WHERE ws.current_step = 'results_review' AND ag.test_names IS NOT NULL
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
            JOIN lab_tests lt ON lr.test_id = lt.id
            JOIN consultations c ON lr.consultation_id = c.id
            JOIN patients p ON c.patient_id = p.id
            WHERE c.doctor_id = ?
            ORDER BY lr.created_at DESC
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
                   COALESCE(cc.consultation_count, 0) as consultation_count,
                   c.status as consultation_status,
                   c.consultation_type
            FROM patients p
            LEFT JOIN (
                SELECT patient_id, COUNT(*) as consultation_count
                FROM consultations 
                GROUP BY patient_id
            ) cc ON p.id = cc.patient_id
            LEFT JOIN consultations c ON p.id = c.patient_id
            WHERE p.visit_type = 'consultation' 
            AND DATE(p.created_at) = CURDATE()
            AND (c.status IS NULL OR c.status NOT IN ('completed', 'cancelled'))
            ORDER BY p.created_at ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $available_patients = $stmt->fetchAll();

                // Pending consultations (registered / scheduled / waiting) for this doctor
                $stmt = $this->pdo->prepare(
                        "SELECT c.*, p.first_name AS patient_first, p.last_name AS patient_last, p.date_of_birth AS patient_dob, p.phone AS patient_phone, ws.consultation_registration_paid
                         FROM consultations c
                         JOIN patients p ON c.patient_id = p.id
                         LEFT JOIN workflow_status ws ON p.id = ws.patient_id
                         WHERE c.doctor_id = ?
                             AND c.status NOT IN ('completed', 'cancelled')
                         ORDER BY COALESCE(c.created_at, c.appointment_date) ASC"
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
                   ws.consultation_registration_paid, ws.lab_tests_paid, ws.results_review_paid,
                   COALESCE(c.main_complaint, c.symptoms) as main_complaint,
                   COALESCE(c.final_diagnosis, c.preliminary_diagnosis, c.diagnosis) as final_diagnosis,
                   c.preliminary_diagnosis,
                   COALESCE(c.appointment_date, c.visit_date, c.created_at) as appointment_date
            FROM consultations c
            JOIN patients p ON c.patient_id = p.id
            LEFT JOIN workflow_status ws ON p.id = ws.patient_id
            WHERE c.doctor_id = ?
            ORDER BY COALESCE(c.appointment_date, c.visit_date, c.created_at) DESC
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
        $stmt = $this->pdo->prepare("SELECT * FROM consultations WHERE patient_id = ? ORDER BY appointment_date DESC");
        $stmt->execute([$patient_id]);
        $consultations = $stmt->fetchAll();

        $this->render('doctor/view_patient', [
            'patient' => $patient,
            'consultations' => $consultations,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function patients() {
        $doctor_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("
            SELECT p.*,
                   COALESCE(consultation_counts.consultation_count, 0) as consultation_count,
                   ws.current_step as workflow_status,
                   ws.consultation_registration_paid,
                   ws.lab_tests_paid,
                   ws.results_review_paid
            FROM patients p
            LEFT JOIN workflow_status ws ON p.id = ws.patient_id
            LEFT JOIN (
                SELECT patient_id, COUNT(*) as consultation_count
                FROM consultations
                WHERE doctor_id = ?
                GROUP BY patient_id
            ) consultation_counts ON p.id = consultation_counts.patient_id
            WHERE ws.consultation_registration_paid = TRUE
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

        // Check workflow access
        $access_check = $this->checkWorkflowAccess($patient_id, 'consultation');
        if (!$access_check['access']) {
            $_SESSION['error'] = $access_check['message'];
            $this->redirect('doctor/view_patient/' . $patient_id);
        }

        // Start transaction
        $this->pdo->beginTransaction();

        try {
            // Create or update consultation
            $stmt = $this->pdo->prepare("
                INSERT INTO consultations (patient_id, doctor_id, appointment_date, status, main_complaint, on_examination,
                                         preliminary_diagnosis, final_diagnosis, lab_investigation, prescription, treatment_plan)
                VALUES (?, ?, NOW(), 'completed', ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                status = 'completed',
                main_complaint = VALUES(main_complaint),
                on_examination = VALUES(on_examination),
                preliminary_diagnosis = VALUES(preliminary_diagnosis),
                final_diagnosis = VALUES(final_diagnosis),
                lab_investigation = VALUES(lab_investigation),
                prescription = VALUES(prescription),
                treatment_plan = VALUES(treatment_plan)
            ");

            $stmt->execute([
                $patient_id,
                $doctor_id,
                $_POST['main_complaint'] ?? '',
                $_POST['on_examination'] ?? '',
                $_POST['preliminary_diagnosis'] ?? '',
                $_POST['final_diagnosis'] ?? '',
                $_POST['lab_investigation'] ?? '',
                $_POST['prescription'] ?? '',
                $_POST['treatment_plan'] ?? ''
            ]);

            $consultation_id = $this->pdo->lastInsertId();

            // Handle selected lab tests
            if (!empty($_POST['selected_tests'])) {
                $selected_tests = json_decode($_POST['selected_tests'], true);
                if (is_array($selected_tests)) {
                    // Get the first available lab technician
                    $stmt = $this->pdo->prepare("SELECT id FROM users WHERE role = 'lab_technician' AND is_active = 1 LIMIT 1");
                    $stmt->execute();
                    $technician = $stmt->fetch();

                    if ($technician) {
                        $technician_id = $technician['id'];
                    } else {
                        // Fallback to first lab technician if none found
                        $technician_id = 4; // Default lab technician ID
                    }

                    foreach ($selected_tests as $test_id) {
                        $stmt = $this->pdo->prepare("
                            INSERT INTO lab_results (consultation_id, test_id, technician_id, status)
                            VALUES (?, ?, ?, 'pending')
                        ");
                        $stmt->execute([$consultation_id, $test_id, $technician_id]);
                    }
                    $this->updateWorkflowStatus($patient_id, 'lab_testing');
                }
            }

            // Handle selected medicines
            if (!empty($_POST['selected_medicines'])) {
                $selected_medicines = json_decode($_POST['selected_medicines'], true);
                if (is_array($selected_medicines)) {
                    foreach ($selected_medicines as $medicine_data) {
                        $stmt = $this->pdo->prepare("
                            INSERT INTO medicine_allocations (consultation_id, medicine_id, quantity, dosage, instructions, allocated_by)
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $consultation_id,
                            $medicine_data['id'],
                            $medicine_data['quantity'] ?? 1,
                            $medicine_data['dosage'] ?? '',
                            $medicine_data['instructions'] ?? '',
                            $doctor_id
                        ]);
                    }
                }
            }

            // If no lab tests or medicines, mark as completed
            if (empty($_POST['selected_tests']) && empty($_POST['selected_medicines'])) {
                $this->updateWorkflowStatus($patient_id, 'completed');
            } elseif (!empty($_POST['selected_medicines'])) {
                // Medicines selected, set to medicine dispensing
                $this->updateWorkflowStatus($patient_id, 'medicine_dispensing', ['medicine_prescribed' => true]);
            }

            $this->pdo->commit();
            $_SESSION['success'] = 'Consultation completed successfully';

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to complete consultation: ' . $e->getMessage();
        }

        $this->redirect('doctor/view_patient/' . $patient_id);
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
                SELECT id, name, generic_name, unit_price, description, stock_quantity
                FROM medicines
                WHERE (name LIKE ? OR generic_name LIKE ? OR description LIKE ?)
                AND stock_quantity > 0
                ORDER BY name
                LIMIT 20
            ");
            $search = "%{$query}%";
            $stmt->execute([$search, $search, $search]);
            $medicines = $stmt->fetchAll();

            header('Content-Type: application/json');
            echo json_encode($medicines);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
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
                       c.appointment_date, p.first_name, p.last_name
                FROM lab_results lr
                JOIN lab_tests t ON lr.test_id = t.id
                JOIN consultations c ON lr.consultation_id = c.id
                JOIN patients p ON c.patient_id = p.id
                WHERE c.patient_id = ? AND lr.status = 'completed'
                ORDER BY lr.created_at DESC
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

    // Fetch recent lab results for patients that belong to this doctor (use lab_tests)
    $stmt = $this->pdo->prepare("\n            SELECT lr.*, t.test_name as test_name, p.first_name, p.last_name, c.appointment_date, lr.result_value, lr.result_text, lr.status, lr.created_at as created_at\n            FROM lab_results lr\n            JOIN lab_tests t ON lr.test_id = t.id\n            JOIN consultations c ON lr.consultation_id = c.id\n            JOIN patients p ON c.patient_id = p.id\n            WHERE c.doctor_id = ?\n            ORDER BY lr.created_at DESC\n            LIMIT 200\n        ");
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

            // Check if consultation payment is made
            $stmt = $this->pdo->prepare("
                SELECT consultation_registration_paid FROM workflow_status WHERE patient_id = ?
            ");
            $stmt->execute([$patient_id]);
            $workflow = $stmt->fetch();

            if (!$workflow || !$workflow['consultation_registration_paid']) {
                throw new Exception('Patient must complete consultation payment first');
            }

            // Update workflow status
            $stmt = $this->pdo->prepare("
                UPDATE workflow_status 
                SET current_step = 'lab_tests', lab_tests_required = 1
                WHERE patient_id = ?
            ");
            $stmt->execute([$patient_id]);

            // Create lab test requests
            foreach ($tests as $test_id) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO lab_test_requests (patient_id, test_id, requested_by, status, notes, created_at)
                    VALUES (?, ?, ?, 'pending', ?, NOW())
                ");
                $stmt->execute([$patient_id, $test_id, $_SESSION['user_id'], $notes]);
            }

            // Complete the consultation
            $stmt = $this->pdo->prepare("
                UPDATE consultations 
                SET status = 'completed', notes = CONCAT(COALESCE(notes, ''), ' - Sent to lab for tests')
                WHERE patient_id = ? AND doctor_id = ? AND status = 'in_progress'
            ");
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

            // Update workflow status
            $stmt = $this->pdo->prepare("
                UPDATE workflow_status 
                SET current_step = 'medicine_dispensing', medicine_prescribed = 1
                WHERE patient_id = ?
            ");
            $stmt->execute([$patient_id]);

            // Create medicine prescriptions
            foreach ($medicines as $medicine) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO prescriptions (patient_id, medicine_id, quantity, dosage, prescribed_by, notes, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $patient_id, 
                    $medicine['medicine_id'], 
                    $medicine['quantity'], 
                    $medicine['dosage'], 
                    $_SESSION['user_id'], 
                    $notes
                ]);
            }

            // Complete the consultation
            $stmt = $this->pdo->prepare("
                UPDATE consultations 
                SET status = 'completed', notes = CONCAT(COALESCE(notes, ''), ' - Prescribed medicines')
                WHERE patient_id = ? AND doctor_id = ? AND status = 'in_progress'
            ");
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
}
?>
