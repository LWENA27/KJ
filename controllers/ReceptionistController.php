<?php
require_once __DIR__ . '/../includes/BaseController.php';

class ReceptionistController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->requireRole('receptionist');
    }

    public function dashboard()
    {
        // Get today's appointments
        $stmt = $this->pdo->prepare("
            SELECT c.*, p.first_name, p.last_name, u.first_name as doctor_first, u.last_name as doctor_last,
                   COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date
            FROM consultations c
            JOIN patients p ON c.patient_id = p.id
            JOIN users u ON c.doctor_id = u.id
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            WHERE DATE(COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) = CURDATE()
            ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at)
        ");
        $stmt->execute();
        $appointments = $stmt->fetchAll();

        // Get recent patients
        $recent_patients = $this->pdo->query("
            SELECT registration_number, first_name, last_name, phone, created_at
            FROM patients
            ORDER BY created_at DESC
            LIMIT 5
        ")->fetchAll();

        // Get today's payment summary
        $stmt = $this->pdo->prepare("
            SELECT 
                COALESCE(SUM(amount), 0) as total_today,
                COUNT(*) as payment_count
            FROM payments 
            WHERE DATE(payment_date) = CURDATE()
        ");
        $stmt->execute();
        $payments_today = $stmt->fetch();

        // Get yesterday's payment for comparison
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total_yesterday
            FROM payments 
            WHERE DATE(payment_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
        ");
        $stmt->execute();
        $payments_yesterday = $stmt->fetchColumn();

        // Calculate percentage change
        $percentage_change = 0;
        if ($payments_yesterday > 0) {
            $percentage_change = (($payments_today['total_today'] - $payments_yesterday) / $payments_yesterday) * 100;
        } elseif ($payments_today['total_today'] > 0) {
            $percentage_change = 100; // 100% increase from 0
        }

        $this->render('receptionist/dashboard', [
            'appointments' => $appointments,
            'recent_patients' => $recent_patients,
            'payments_today' => $payments_today,
            'percentage_change' => $percentage_change,
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    public function patients()
    {
        // Use inline derived table instead of view (compatible with existing DB)
        $patients = $this->pdo->query("SELECT p.*,
                lv.visit_id AS latest_visit_id,
                lv.status AS workflow_status,
                CASE
                    -- If no payment, show registration step
                    WHEN lv.status = 'active' AND (SELECT COUNT(*) FROM payments pay WHERE pay.visit_id = lv.visit_id AND pay.payment_type = 'registration' AND pay.payment_status = 'paid') = 0 THEN 'registration'
                    -- If payment made but no consultation started, show consultation_registration (waiting for doctor)
                    WHEN lv.status = 'active' AND (SELECT COUNT(*) FROM consultations con WHERE con.visit_id = lv.visit_id AND con.status IN ('pending','in_progress')) > 0 AND (SELECT COUNT(*) FROM consultations con2 WHERE con2.visit_id = lv.visit_id AND con2.status = 'completed') = 0 THEN 'consultation_registration'
                    -- If lab tests ordered but not all completed, show lab_tests
                    WHEN lv.status = 'active' AND (SELECT COUNT(*) FROM lab_test_orders lto WHERE lto.visit_id = lv.visit_id AND lto.status IN ('pending','sample_collected','in_progress')) > 0 THEN 'lab_tests'
                    -- If prescriptions pending, show medicine_dispensing
                    WHEN lv.status = 'active' AND (SELECT COUNT(*) FROM prescriptions pr WHERE pr.visit_id = lv.visit_id AND pr.status = 'pending') > 0 THEN 'medicine_dispensing'
                    -- If visit active but nothing pending, show results_review
                    WHEN lv.status = 'active' THEN 'results_review'
                    -- If visit completed, show completed
                    ELSE 'completed'
                END AS current_step,
                (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = lv.visit_id AND pay.payment_type = 'registration' AND pay.payment_status = 'paid'),1,0)) AS consultation_registration_paid,
                (SELECT IF(EXISTS(SELECT 1 FROM payments pay WHERE pay.visit_id = lv.visit_id AND pay.payment_type = 'lab_test' AND pay.payment_status = 'paid'),1,0)) AS lab_tests_paid,
                (SELECT IF(EXISTS(SELECT 1 FROM prescriptions pr WHERE pr.visit_id = lv.visit_id AND pr.status = 'pending'),1,0)) AS medicine_prescribed,
                (SELECT IF(EXISTS(SELECT 1 FROM prescriptions pr2 WHERE pr2.visit_id = lv.visit_id AND pr2.status = 'dispensed'),1,0)) AS medicine_dispensed,
                (SELECT IF(EXISTS(SELECT 1 FROM lab_test_orders lto WHERE lto.visit_id = lv.visit_id AND lto.status IN ('pending','sample_collected','in_progress')),1,0)) AS lab_tests_required,
                0 AS final_payment_collected
            FROM patients p
            LEFT JOIN (
                SELECT pv.patient_id, pv.id AS visit_id, pv.status
                FROM patient_visits pv
                JOIN (SELECT patient_id, MAX(created_at) AS latest FROM patient_visits GROUP BY patient_id) latest
                ON latest.patient_id = pv.patient_id AND latest.latest = pv.created_at
            ) lv ON lv.patient_id = p.id
            ORDER BY
                CASE
                    WHEN lv.status = 'completed' THEN 1
                    WHEN (SELECT IF(EXISTS(SELECT 1 FROM prescriptions prx WHERE prx.visit_id = lv.visit_id AND prx.status = 'dispensed'),1,0)) = 0 AND (SELECT IF(EXISTS(SELECT 1 FROM prescriptions prx2 WHERE prx2.visit_id = lv.visit_id AND prx2.status = 'pending'),1,0)) = 1 THEN 2
                    WHEN (SELECT IF(EXISTS(SELECT 1 FROM payments payx WHERE payx.visit_id = lv.visit_id AND payx.payment_status = 'paid'),1,0)) = 0 AND ((SELECT IF(EXISTS(SELECT 1 FROM prescriptions prx3 WHERE prx3.visit_id = lv.visit_id AND prx3.status = 'pending'),1,0)) = 1 OR (SELECT IF(EXISTS(SELECT 1 FROM lab_test_orders ltox WHERE ltox.visit_id = lv.visit_id AND ltox.status IN ('pending','sample_collected','in_progress')),1,0)) = 1) THEN 3
                    ELSE 4
                END,
                p.created_at DESC
        ")->fetchAll();

        $this->render('receptionist/patients', [
            'patients' => $patients,
            'csrf_token' => $this->generateCSRF(),
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    private function generateRegistrationNumber()
    {
        try {
            $year = date('Y');
            $pattern = "KJ{$year}%";

            $stmt = $this->pdo->prepare("
            SELECT COALESCE(MAX(CAST(SUBSTR(registration_number, -4) AS UNSIGNED)), 0) + 1 AS next_sequence
            FROM patients 
            WHERE registration_number LIKE ?
        ");

            if (!$stmt->execute([$pattern])) {
                throw new PDOException('Failed to fetch last registration number sequence.');
            }

            $next_sequence = $stmt->fetchColumn();
            if ($next_sequence === false) {
                $next_sequence = 1;
            }

            return sprintf("KJ%s%04d", $year, $next_sequence);
        } catch (PDOException $e) {
            // Fallback to 1 on error, or log the error as needed
            error_log('Error generating registration number: ' . $e->getMessage());
            $year = date('Y');
            return sprintf("KJ%s%04d", $year, 1);
        }
    }
    public function register_patient()
    {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();

            // Normalize POST inputs to avoid undefined array key notices
            $post = array_map(function($v){ return $v; }, $_POST);
            $visit_type = $post['visit_type'] ?? 'consultation';
            $consultation_fee = $post['consultation_fee'] ?? null;
            $payment_method = $post['payment_method'] ?? null;
            $payment_method_lab = $post['payment_method_lab'] ?? null;
            $first_name = $post['first_name'] ?? null;
            $last_name = $post['last_name'] ?? null;
            $date_of_birth = $post['date_of_birth'] ?? null;
            $gender = $post['gender'] ?? null;
            $phone = $post['phone'] ?? null;
            $email = $post['email'] ?? null;
            $address = $post['address'] ?? null;
            $occupation = $post['occupation'] ?? null;
            $emergency_contact_name = $post['emergency_contact_name'] ?? null;
            $emergency_contact_phone = $post['emergency_contact_phone'] ?? null;
            $temperature = $post['temperature'] ?? null;
            $blood_pressure = $post['blood_pressure'] ?? null;
            $pulse_rate = $post['pulse_rate'] ?? null;
            $body_weight = $post['body_weight'] ?? null;
            $height = $post['height'] ?? null;

            try {
                // For consultation visits, only require vital signs (no payment - that's handled by Accountant)
                if ($visit_type === 'consultation') {
                    // Require basic vital signs for consultation registrations
                    // Expect blood_pressure in the form "systolic/diastolic"
                    $bp_ok = !empty($blood_pressure) && is_string($blood_pressure) && strpos($blood_pressure, '/') !== false;
                    if (empty($temperature) || !$bp_ok || empty($pulse_rate)) {
                        throw new Exception('Temperature, blood pressure (systolic/diastolic) and pulse rate are required for consultation registration');
                    }
                    // Basic numeric validation
                    if (!is_numeric($temperature) || !is_numeric($pulse_rate)) {
                        throw new Exception('Temperature and pulse rate must be numeric');
                    }
                }

                $this->pdo->beginTransaction();

                // Insert patient basic info (only core fields that exist in all schemas)
                $stmt = $this->pdo->prepare("
                    INSERT INTO patients (
                        registration_number, first_name, last_name,
                        date_of_birth, gender, phone, email,
                        address, occupation, emergency_contact_name,
                        emergency_contact_phone, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");

                $registration_number = $this->generateRegistrationNumber();

                // Execute patient insert with only core columns
                $stmt->execute([
                    $registration_number,
                    $this->sanitize($first_name),
                    $this->sanitize($last_name),
                    $date_of_birth,
                    $this->sanitize($gender),
                    $this->sanitize($phone),
                    $this->sanitize($email),
                    $this->sanitize($address),
                    $this->sanitize($occupation),
                    $this->sanitize($emergency_contact_name),
                    $this->sanitize($emergency_contact_phone)
                ]);

                $patient_id = $this->pdo->lastInsertId();

                // Get the next visit number for this patient
                $stmt = $this->pdo->prepare("SELECT COALESCE(MAX(visit_number), 0) + 1 as next_visit_number FROM patient_visits WHERE patient_id = ?");
                $stmt->execute([$patient_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $visit_number = $result['next_visit_number'];

                // Create a patient_visits row for this registration (visit-centric model)
                $stmt = $this->pdo->prepare("INSERT INTO patient_visits (patient_id, visit_number, visit_date, visit_type, registered_by, status, created_at, updated_at) VALUES (?, ?, CURDATE(), ?, ?, 'active', NOW(), NOW())");
                $stmt->execute([$patient_id, $visit_number, $visit_type, $_SESSION['user_id']]);
                $visit_id = $this->pdo->lastInsertId();

                // For consultation visits, create the consultation record (payment will be handled by Accountant)
                if ($visit_type === 'consultation') {
                    // Create a consultations row referencing visit_id so doctors can see the registered patient
                    // Payment is not recorded here - Accountant will process payment separately
                    // Patient will need to pay at Accountant desk before being seen by doctor
                    $default_doctor_id = 1;
                    $stmt = $this->pdo->prepare("INSERT INTO consultations (visit_id, patient_id, doctor_id, consultation_type, status, created_at) VALUES (?, ?, ?, 'new', 'pending', NOW())");
                    $stmt->execute([$visit_id, $patient_id, $default_doctor_id]);
                }

                // Handle lab-only visit: create lab_test_orders (payment handled by Accountant)
                if ($visit_type === 'lab_test') {
                    // Selected tests are expected as an array of test IDs
                    $selected_tests = $_POST['selected_tests'] ?? [];
                    if (!is_array($selected_tests)) {
                        // If a comma-separated string was submitted, convert to array
                        $selected_tests = array_filter(array_map('trim', explode(',', (string)$selected_tests)));
                    }

                    if (!empty($selected_tests)) {
                        // Insert lab test orders
                        $stmtOrder = $this->pdo->prepare("INSERT INTO lab_test_orders (visit_id, patient_id, test_id, ordered_by, status, created_at, updated_at) VALUES (?, ?, ?, ?, 'pending', NOW(), NOW())");
                        $total_lab_amount = 0;
                        $stmtPrice = $this->pdo->prepare("SELECT price FROM lab_tests WHERE id = ? LIMIT 1");
                        foreach ($selected_tests as $test_id) {
                            $tid = intval($test_id);
                            if ($tid <= 0) continue;
                            $stmtOrder->execute([$visit_id, $patient_id, $tid, $_SESSION['user_id']]);
                            // Sum price for reference (payment recorded later by Accountant)
                            $stmtPrice->execute([$tid]);
                            $price = (float)$stmtPrice->fetchColumn();
                            $total_lab_amount += $price;
                        }
                        // Note: Payment is NOT recorded here - Accountant will handle lab test payment
                    }
                }

                // Record vital signs if provided — insert into vital_signs linked to the visit
                $bp_systolic = null; $bp_diastolic = null;
                if (!empty($blood_pressure) && is_string($blood_pressure) && strpos($blood_pressure, '/') !== false) {
                    [$bp_systolic, $bp_diastolic] = array_map('intval', array_map('trim', explode('/', $blood_pressure, 2)));
                }
                // Record vital signs if provided — insert into vital_signs linked to the visit
                // Skip recording vital signs for lab-only visits
                if ($visit_type !== 'lab_test') {
                    if (!empty($temperature) || !empty($bp_systolic) || !empty($bp_diastolic) || !empty($pulse_rate) || !empty($body_weight) || !empty($height)) {
                        $stmt = $this->pdo->prepare("INSERT INTO vital_signs (visit_id, patient_id, temperature, blood_pressure_systolic, blood_pressure_diastolic, pulse_rate, weight, height, recorded_by, recorded_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                        $stmt->execute([
                            $visit_id,
                            $patient_id,
                            !empty($temperature) ? $temperature : null,
                            $bp_systolic ?: null,
                            $bp_diastolic ?: null,
                            !empty($pulse_rate) ? $pulse_rate : null,
                            !empty($body_weight) ? $body_weight : null,
                            !empty($height) ? $height : null,
                            $_SESSION['user_id']
                        ]);
                    }
                }

                $this->pdo->commit();
                
                // Build success message based on visit type
                $success_message = "Patient registered successfully! Registration Number: $registration_number";
                if ($visit_type === 'consultation') {
                    $success_message .= " - Please direct patient to Accountant for consultation fee payment.";
                } else if ($visit_type === 'lab_test') {
                    $success_message .= " - Please direct patient to Accountant for lab test payment.";
                }
                
                $_SESSION['success'] = $success_message;

                // Always redirect to patients list - payment is handled by Accountant
                $this->redirect('receptionist/patients');
            } catch (Exception $e) {
                $this->pdo->rollBack();
                $_SESSION['error'] = 'Registration failed: ' . $e->getMessage();
            }
        }

        $this->render('receptionist/register_patient', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * AJAX endpoint used by receptionists to search lab tests by name/code.
     * GET param: q
     */
    public function search_lab_tests() {
        $q = trim($_GET['q'] ?? '');
        header('Content-Type: application/json');
        try {
            if ($q === '') {
                echo json_encode([]);
                return;
            }
            $stmt = $this->pdo->prepare("SELECT id, test_name, price, test_code FROM lab_tests WHERE is_active = 1 AND (test_name LIKE ? OR test_code LIKE ?) ORDER BY test_name LIMIT 30");
            $like = '%' . $q . '%';
            $stmt->execute([$like, $like]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($rows);
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }

    public function appointments()
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*, 
                   p.first_name, 
                   p.last_name, 
                   u.first_name as doctor_first, 
                   u.last_name as doctor_last,
                   pv.visit_date,
                   COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date
            FROM consultations c
            JOIN patients p ON c.patient_id = p.id
            JOIN users u ON c.doctor_id = u.id
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at) DESC
        ");
        $stmt->execute();
        $appointments = $stmt->fetchAll();

        $this->render('receptionist/appointments', [
            'appointments' => $appointments,
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    public function payments()
    {
        // Get all payment records (paid and pending) - this query is not fully shown, assuming it's fine for general payments
        $stmt = $this->pdo->prepare("
            SELECT p.*, pat.first_name, pat.last_name, pat.registration_number, u.first_name as collected_by_name
            FROM payments p
            JOIN patients pat ON p.patient_id = pat.id
            LEFT JOIN users u ON p.collected_by = u.id
            ORDER BY p.payment_status ASC, p.payment_date DESC
        ");
        $stmt->execute();
        $payments = $stmt->fetchAll();

        // Get pending lab test payments (tests ordered but not paid)
        // Group by visit to show all tests for a patient together
        $stmt = $this->pdo->prepare("
            SELECT 
                MIN(lto.id) AS order_id,
                lto.visit_id,
                lto.patient_id,
                p.first_name,
                p.last_name,
                p.registration_number,
                pv.visit_date,
                GROUP_CONCAT(lt.test_name SEPARATOR ', ') AS test_names,
                SUM(lt.price) AS total_amount,
                COALESCE(SUM(pay.amount), 0) AS amount_already_paid,
                (SUM(lt.price) - COALESCE(SUM(pay.amount), 0)) AS remaining_amount_to_pay,
                COUNT(lto.id) as test_count,
                MAX(lto.created_at) AS last_order_created
            FROM lab_test_orders lto
            JOIN lab_tests lt ON lto.test_id = lt.id  
            JOIN patients p ON lto.patient_id = p.id
            LEFT JOIN patient_visits pv ON lto.visit_id = pv.id
            LEFT JOIN payments pay ON lto.visit_id = pay.visit_id 
                                  AND lto.patient_id = pay.patient_id 
                                  AND pay.payment_type = 'lab_test'
                                  AND pay.payment_status = 'paid'
            WHERE lto.status = 'pending'
            GROUP BY lto.visit_id, lto.patient_id, p.first_name, p.last_name, p.registration_number, pv.visit_date
            HAVING remaining_amount_to_pay > 0
            ORDER BY pv.visit_date DESC, last_order_created DESC
        ");
        $stmt->execute();
        $pending_lab_payments = $stmt->fetchAll();

        // Get pending medicine payments (medicines prescribed but not paid)
        // This query is crucial for partial payments
        $stmt = $this->pdo->prepare("
            SELECT 
                pr.id AS prescription_id,
                pr.visit_id,
                pr.patient_id,
                p.first_name,
                p.last_name,
                p.registration_number,
                pv.visit_date,
                GROUP_CONCAT(CONCAT(m.name, ' (', pr.quantity_prescribed, ' ', m.unit, ')') SEPARATOR ', ') AS medicine_names,
                SUM(pr.quantity_prescribed * m.unit_price) AS total_cost_of_prescription,
                COALESCE(SUM(pay.amount), 0) AS amount_already_paid,
                (SUM(pr.quantity_prescribed * m.unit_price) - COALESCE(SUM(pay.amount), 0)) AS remaining_amount_to_pay
            FROM prescriptions pr
            JOIN medicines m ON pr.medicine_id = m.id
            JOIN patients p ON pr.patient_id = p.id
            JOIN patient_visits pv ON pr.visit_id = pv.id
            LEFT JOIN payments pay ON pr.visit_id = pay.visit_id 
                                  AND pr.patient_id = pay.patient_id 
                                  AND pay.item_type = 'prescription' 
                                  AND pay.item_id = pr.id
                                  AND pay.payment_status = 'paid'
            WHERE pr.status IN ('pending', 'partial')
            GROUP BY pr.id, pr.visit_id, pr.patient_id, p.first_name, p.last_name, p.registration_number, pv.visit_date
            HAVING remaining_amount_to_pay > 0
            ORDER BY pr.created_at DESC
        ");
        $stmt->execute();
        $pending_medicine_payments = $stmt->fetchAll();

        // Get pending service payments (services that haven't been paid for, regardless of service status)
        $stmt = $this->pdo->prepare("
            SELECT 
                so.id AS order_id,
                so.visit_id,
                so.patient_id,
                p.first_name,
                p.last_name,
                p.registration_number,
                pv.visit_date,
                s.service_name,
                s.price AS amount,
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM payments 
                        WHERE item_id = so.id 
                        AND item_type = 'service_order' 
                        AND payment_status = 'paid'
                    ) THEN 'paid'
                    ELSE 'pending'
                END as payment_status,
                (SELECT id FROM payments 
                 WHERE item_id = so.id 
                 AND item_type = 'service_order' 
                 AND payment_status = 'paid' 
                 LIMIT 1) AS payment_id,
                (SELECT COUNT(*) FROM payments 
                 WHERE item_id = so.id 
                 AND item_type = 'service_order' 
                 AND payment_status = 'paid') as paid_count
            FROM service_orders so
            JOIN services s ON so.service_id = s.id
            JOIN patients p ON so.patient_id = p.id
            LEFT JOIN patient_visits pv ON so.visit_id = pv.id
            HAVING paid_count = 0
            ORDER BY so.created_at DESC
        ");
        
        \Logger::debug('Fetching pending service payments...');
        $stmt->execute();
        $pending_service_payments = $stmt->fetchAll();
        \Logger::debug('Found ' . count($pending_service_payments) . ' pending service payments');
        foreach ($pending_service_payments as $payment) {
            \Logger::debug(sprintf(
                "Service payment: Patient=%s, Service=%s, Status=%s, Paid_Count=%d",
                $payment['registration_number'],
                $payment['service_name'],
                $payment['payment_status'],
                $payment['paid_count']
            ));
        }
        $stmt->execute();
        $pending_service_payments = $stmt->fetchAll();

        $this->render('receptionist/payments', [
            'payments' => $payments, // Pass general payments too if needed by the view
            'pending_lab_payments' => $pending_lab_payments,
            'pending_medicine_payments' => $pending_medicine_payments,
            'pending_service_payments' => $pending_service_payments,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function payment_history()
    {
        // Build WHERE clause based on filters
        $where_clauses = ["p.payment_status = 'paid'"];
        $params = [];

        // Search by patient name
        if (!empty($_GET['search'])) {
            $where_clauses[] = "(pt.first_name LIKE ? OR pt.last_name LIKE ?)";
            $search_term = '%' . $_GET['search'] . '%';
            $params[] = $search_term;
            $params[] = $search_term;
        }

        // Filter by payment type
        if (!empty($_GET['payment_type'])) {
            $where_clauses[] = "p.payment_type = ?";
            $params[] = $_GET['payment_type'];
        }

        // Filter by payment method
        if (!empty($_GET['payment_method'])) {
            $where_clauses[] = "p.payment_method = ?";
            $params[] = $_GET['payment_method'];
        }

        // Build final query
        $where_sql = implode(' AND ', $where_clauses);

        // Check if caller asked for grouped view (by visit or by date)
        $group_by = $_GET['group_by'] ?? '';

        // If filtering by visit_id directly (from group links), add clause
        if (!empty($_GET['visit_id'])) {
            $where_clauses[] = 'p.visit_id = ?';
            $params[] = $_GET['visit_id'];
            $where_sql = implode(' AND ', $where_clauses);
        }

        if ($group_by === 'visit') {
            // Group payments by visit (per patient visit)
            $group_params = [];
            $group_where = "payments.payment_status = 'paid'";
            if (!empty($_GET['search'])) {
                $group_where .= " AND (patients.first_name LIKE ? OR patients.last_name LIKE ? )";
                $sterm = '%' . $_GET['search'] . '%';
                $group_params[] = $sterm;
                $group_params[] = $sterm;
            }
            if (!empty($_GET['payment_type'])) {
                $group_where .= " AND payments.payment_type = ?";
                $group_params[] = $_GET['payment_type'];
            }
            if (!empty($_GET['payment_method'])) {
                $group_where .= " AND payments.payment_method = ?";
                $group_params[] = $_GET['payment_method'];
            }

            $stmt = $this->pdo->prepare("\n                SELECT pv.id AS visit_id,\n                       pv.visit_date,\n                       patients.id AS patient_id,\n                       CONCAT(patients.first_name, ' ', patients.last_name) AS patient_name,\n                       SUM(payments.amount) AS total_paid,\n                       COUNT(payments.id) AS payments_count\n                FROM payments\n                JOIN patient_visits pv ON payments.visit_id = pv.id\n                JOIN patients ON payments.patient_id = patients.id\n                WHERE $group_where\n                GROUP BY pv.id, patients.id, pv.visit_date, patients.first_name, patients.last_name\n                ORDER BY pv.visit_date DESC\n            ");
            $stmt->execute($group_params);
            $grouped_results = $stmt->fetchAll();

            $this->render('receptionist/payment_history', [
                'group_by' => 'visit',
                'grouped_results' => $grouped_results,
                'payments' => [],
                'sidebar_data' => $this->getSidebarData(),
                'csrf_token' => $this->generateCSRF()
            ]);
            return;
        } elseif ($group_by === 'date') {
            // Group payments by payment date (per patient)
            $group_params = [];
            $group_where = "payments.payment_status = 'paid'";
            if (!empty($_GET['search'])) {
                $group_where .= " AND (patients.first_name LIKE ? OR patients.last_name LIKE ? )";
                $sterm = '%' . $_GET['search'] . '%';
                $group_params[] = $sterm;
                $group_params[] = $sterm;
            }
            if (!empty($_GET['payment_type'])) {
                $group_where .= " AND payments.payment_type = ?";
                $group_params[] = $_GET['payment_type'];
            }
            if (!empty($_GET['payment_method'])) {
                $group_where .= " AND payments.payment_method = ?";
                $group_params[] = $_GET['payment_method'];
            }

            $stmt = $this->pdo->prepare("\n                SELECT DATE(payments.payment_date) AS payment_date,\n                       patients.id AS patient_id,\n                       CONCAT(patients.first_name, ' ', patients.last_name) AS patient_name,\n                       SUM(payments.amount) AS total_paid,\n                       COUNT(payments.id) AS payments_count\n                FROM payments\n                JOIN patients ON payments.patient_id = patients.id\n                WHERE $group_where\n                GROUP BY DATE(payments.payment_date), patients.id, patients.first_name, patients.last_name\n                ORDER BY payment_date DESC\n            ");
            $stmt->execute($group_params);
            $grouped_results = $stmt->fetchAll();

            $this->render('receptionist/payment_history', [
                'group_by' => 'date',
                'grouped_results' => $grouped_results,
                'payments' => [],
                'sidebar_data' => $this->getSidebarData(),
                'csrf_token' => $this->generateCSRF()
            ]);
            return;
        }

        // Default: return individual payment records
        $stmt = $this->pdo->prepare("\n            SELECT p.*, \n                   CONCAT(pt.first_name, ' ', pt.last_name) as patient_name,\n                   pv.visit_date\n            FROM payments p\n            JOIN patients pt ON p.patient_id = pt.id\n            LEFT JOIN patient_visits pv ON p.visit_id = pv.id\n            WHERE $where_sql\n            ORDER BY p.payment_date DESC\n        ");

        $stmt->execute($params);
        $payments = $stmt->fetchAll();

        $this->render('receptionist/payment_history', [
            'payments' => $payments,
            'sidebar_data' => $this->getSidebarData(),
            'csrf_token' => $this->generateCSRF(),
            'group_by' => ''
        ]);
        return;
    }

    public function process_final_payment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('receptionist/patients');
        }

        $this->validateCSRF();

        $patient_id = $_POST['patient_id'];
        $amount = floatval($_POST['amount']);
        $payment_method = $_POST['payment_method'];

        if (!$patient_id || $amount <= 0) {
            $_SESSION['error'] = 'Invalid payment details';
            $this->redirect('receptionist/patients');
        }

        try {
            $this->pdo->beginTransaction();

            // Find latest visit for patient
            $stmt = $this->pdo->prepare("SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$patient_id]);
            $visit = $stmt->fetch();
            if (!$visit) {
                throw new Exception('Patient visit not found');
            }
            $visit_id = $visit['id'];

            // Insert payment record linked to visit
            $stmt = $this->pdo->prepare("\
                INSERT INTO payments (visit_id, patient_id, payment_type, amount, payment_method, payment_status, reference_number, collected_by, payment_date, notes)
                VALUES (?, ?, 'registration', ?, ?, 'paid', NULL, ?, NOW(), ?)
            ");
            $stmt->execute([$visit_id, $patient_id, $amount, $payment_method, $_SESSION['user_id'], 'Final payment']);

            // Update visit status to completed
            $this->updateWorkflowStatus($patient_id, 'completed', ['final_payment_collected' => true]);

            $this->pdo->commit();
            $_SESSION['success'] = 'Final payment processed successfully';
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to process payment: ' . $e->getMessage();
        }

        $this->redirect('receptionist/patients');
    }

   public function record_payment()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = 'Invalid request method';
        $this->redirect('receptionist/payments');
        return;
    }

    $this->validateCSRF();

    $patient_id = $_POST['patient_id'] ?? null;
    $visit_id = $_POST['visit_id'] ?? null;
    $payment_type = $_POST['payment_type'] ?? null;
    $amount = floatval($_POST['amount'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $reference_number = $_POST['reference_number'] ?? null;
    $item_id = $_POST['item_id'] ?? null;
    $item_type = $_POST['item_type'] ?? null;

    // Debug log the payment information
    error_log(sprintf(
        "Recording payment - Patient: %d, Visit: %d, Type: %s, Amount: %f, Method: %s, ItemID: %s, ItemType: %s",
        $patient_id, $visit_id, $payment_type, $amount, $payment_method, $item_id, $item_type
    ));

    if (!$patient_id || !$visit_id || !$payment_type || $amount <= 0) {
        $_SESSION['error'] = 'Invalid payment details';
        $this->redirect('receptionist/payments');
        return;
    }

    try {
        $this->pdo->beginTransaction();

        error_log(sprintf(
            "Recording payment: Patient=%d, Visit=%d, Type=%s, Amount=%f, ItemID=%s, ItemType=%s",
            $patient_id, $visit_id, $payment_type, $amount, $item_id, $item_type
        ));

        // Insert payment record
        $stmt = $this->pdo->prepare("
            INSERT INTO payments 
            (visit_id, patient_id, payment_type, item_id, item_type, amount, payment_method, payment_status, 
             reference_number, collected_by, payment_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'paid', ?, ?, NOW())
        ");
        $stmt->execute([
            $visit_id,
            $patient_id,
            $payment_type,
            $item_id,
            $item_type,
            $amount,
            $payment_method,
            $reference_number,
            $_SESSION['user_id']
        ]);

        // Update workflow status based on payment type
        if ($payment_type === 'lab_test') {
            $this->updateWorkflowStatus($patient_id, 'lab_testing', ['lab_tests_paid' => true]);
        } elseif ($payment_type === 'medicine') {
            // Keep in medicine_dispensing status until actual dispensing happens
            $this->updateWorkflowStatus($patient_id, 'medicine_dispensing', ['medicine_payment_received' => true]);
        } elseif ($payment_type === 'service') {
            // For service payments, verify the service order exists
            if (empty($item_id)) {
                throw new Exception("Missing service order ID");
            }

            error_log(sprintf(
                "Processing service payment - OrderID: %s, PatientID: %d",
                $item_id, $patient_id
            ));
            
            $stmt = $this->pdo->prepare("
                SELECT so.id, so.service_id, so.visit_id, so.patient_id,
                       s.price, s.service_name
                FROM service_orders so
                JOIN services s ON so.service_id = s.id
                WHERE so.id = ? AND so.patient_id = ?
            ");
            $stmt->execute([$item_id, $patient_id]);
            $service_order = $stmt->fetch();
            
            if (!$service_order) {
                error_log("WARNING: Service order not found for ID: " . $item_id);
                throw new Exception("Service order not found");
            }
            
            error_log(sprintf(
                "Found service order - ID: %d, Service: %s, Price: %f",
                $service_order['id'],
                $service_order['service_name'],
                $service_order['price']
            ));
            
            // Payment record was already inserted above - no need to insert again
            error_log("Payment already recorded for service order in main insertion");
        }

        $this->pdo->commit();
        $_SESSION['success'] = 'Payment recorded successfully';
        
    } catch (Exception $e) {
        $this->pdo->rollBack();
        $_SESSION['error'] = 'Failed to record payment: ' . $e->getMessage();
        error_log('Payment recording error: ' . $e->getMessage());
    }

    $this->redirect('receptionist/payments');
}

    public function process_medicine_dispensing()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('receptionist/medicine');
            return;
        }

        $this->validateCSRF();

        $patient_id = $_POST['patient_id'] ?? null;
        // The 'dispensed_items' array should contain prescription_id => quantity_to_dispense
        $dispensed_items = $_POST['dispensed_items'] ?? []; 

        // Debug logging
        error_log("Processing medicine dispensing for patient: $patient_id");
        error_log("Dispensed items: " . print_r($dispensed_items, true));

        if (!$patient_id || empty($dispensed_items)) {
            $_SESSION['error'] = 'Invalid patient data or no medicines selected for dispensing.';
            $this->redirect('receptionist/medicine');
            return;
        }

        try {
            $this->pdo->beginTransaction();

            $visit_id = null; // To be determined from the first prescription processed

            foreach ($dispensed_items as $prescription_id => $quantity_to_dispense) {
                $quantity_to_dispense = intval($quantity_to_dispense);

                if ($quantity_to_dispense <= 0) {
                    continue; // Skip if nothing is to be dispensed for this item
                }

                // Get prescription details, including current dispensed quantity, medicine info, and stock
                $stmt = $this->pdo->prepare("
                    SELECT pr.id, pr.patient_id, pr.visit_id, pr.medicine_id, pr.quantity_prescribed, pr.quantity_dispensed,
                           m.name as medicine_name, m.unit_price,
                           COALESCE(SUM(mb.quantity_remaining), 0) as stock_quantity
                    FROM prescriptions pr
                    JOIN medicines m ON pr.medicine_id = m.id
                    LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
                    WHERE pr.id = ?
                    GROUP BY pr.id, pr.patient_id, pr.visit_id, pr.medicine_id, pr.quantity_prescribed, pr.quantity_dispensed, m.name, m.unit_price
                    FOR UPDATE
                ");
                $stmt->execute([$prescription_id]);
                $prescription = $stmt->fetch();

                if (!$prescription) {
                    throw new Exception("Prescription ID $prescription_id not found.");
                }

                // Set visit_id from the first prescription
                if ($visit_id === null) {
                    $visit_id = $prescription['visit_id'];
                }

                $already_dispensed = $prescription['quantity_dispensed'];
                $quantity_prescribed = $prescription['quantity_prescribed'];
                $medicine_name = $prescription['medicine_name'];
                $unit_price = $prescription['unit_price'];
                $stock_quantity = $prescription['stock_quantity'];

                $remaining_to_prescribe = $quantity_prescribed - $already_dispensed;

                // Calculate total amount paid for this specific prescription
                // Sum all payments linked to this prescription_id and visit_id
                $stmt_paid = $this->pdo->prepare("
                    SELECT COALESCE(SUM(amount), 0) as total_paid
                    FROM payments
                    WHERE visit_id = ? AND patient_id = ? AND payment_type = 'medicine' AND item_id = ? AND item_type = 'prescription' AND payment_status = 'paid'
                ");
                $stmt_paid->execute([$prescription['visit_id'], $prescription['patient_id'], $prescription_id]);
                $total_paid_for_prescription = $stmt_paid->fetchColumn();

                // Calculate the maximum quantity the patient has paid for
                // If unit_price is 0, assume infinite paid quantity to avoid division by zero.
                $max_paid_quantity = ($unit_price > 0) ? floor($total_paid_for_prescription / $unit_price) : PHP_INT_MAX;

                // Determine the actual maximum quantity that can be dispensed in this transaction
                // It's the minimum of (remaining prescribed, available stock, paid quantity)
                $max_dispensable_quantity = min($remaining_to_prescribe, $stock_quantity, $max_paid_quantity);

                if ($quantity_to_dispense > $max_dispensable_quantity) {
                    throw new Exception("Cannot dispense $quantity_to_dispense of $medicine_name. Max dispensable based on stock, payment, and remaining prescription is $max_dispensable_quantity.");
                }

                // If the quantity to dispense is valid, proceed
                $new_dispensed_quantity = $already_dispensed + $quantity_to_dispense;

                // Deduct from medicine stock using FEFO (First-Expiry-First-Out)
                $remaining_for_deduction = $quantity_to_dispense;
                $batch_stmt = $this->pdo->prepare("
                    SELECT id, quantity_remaining
                    FROM medicine_batches
                    WHERE medicine_id = ? AND quantity_remaining > 0
                    ORDER BY expiry_date ASC, created_at ASC
                ");
                $batch_stmt->execute([$prescription['medicine_id']]);
                $batches = $batch_stmt->fetchAll();

                foreach ($batches as $batch) {
                    if ($remaining_for_deduction <= 0) break;

                    $deduct = min($remaining_for_deduction, $batch['quantity_remaining']);
                    $update_stmt = $this->pdo->prepare("
                        UPDATE medicine_batches
                        SET quantity_remaining = quantity_remaining - ?
                        WHERE id = ?
                    ");
                    $update_stmt->execute([$deduct, $batch['id']]);
                    $remaining_for_deduction -= $deduct;
                }
                error_log("Updated stock for medicine ID {$prescription['medicine_id']} by deducting $quantity_to_dispense");

                // Update the prescription record
                $new_status = 'partial';
                if ($new_dispensed_quantity >= $quantity_prescribed) {
                    $new_status = 'dispensed'; // Fully dispensed
                } elseif ($new_dispensed_quantity > 0) {
                    $new_status = 'partial'; // Partially dispensed
                } else {
                    $new_status = 'pending'; // No change if 0 dispensed (should be caught by $quantity_to_dispense <= 0 check)
                }

                $update_prescription_stmt = $this->pdo->prepare("
                    UPDATE prescriptions
                    SET quantity_dispensed = ?,
                        status = ?,
                        dispensed_by = ?,
                        dispensed_at = NOW(),
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $update_prescription_stmt->execute([
                    $new_dispensed_quantity,
                    $new_status,
                    $_SESSION['user_id'],
                    $prescription_id
                ]);
                error_log("Updated prescription ID $prescription_id: quantity_dispensed=$new_dispensed_quantity, status=$new_status");
            }

            // After processing all items, check if the patient's overall medicine dispensing for the visit is complete
            // This means all prescriptions for the current visit are either 'dispensed' or 'cancelled'
            if ($visit_id) {
                $pending_prescriptions_for_visit_stmt = $this->pdo->prepare("
                    SELECT COUNT(id) FROM prescriptions
                    WHERE visit_id = ? AND status IN ('pending', 'partial')
                ");
                $pending_prescriptions_for_visit_stmt->execute([$visit_id]);
                $remaining_pending_count = $pending_prescriptions_for_visit_stmt->fetchColumn();

                if ($remaining_pending_count === 0) {
                    // All prescriptions for this visit are now fully dispensed or cancelled
                    $this->updateWorkflowStatus($patient_id, 'completed', [
                        'medicine_dispensed' => true,
                        'medicine_dispensed_by' => $_SESSION['user_id'],
                        'medicine_dispensed_at' => date('Y-m-d H:i:s')
                    ]);
                    error_log("Workflow status updated to 'completed' for patient $patient_id (all medicines dispensed)");
                } else {
                    // Still some pending/partial prescriptions, keep workflow status as 'medicine_dispensing'
                    $this->updateWorkflowStatus($patient_id, 'medicine_dispensing', [
                        'medicine_dispensed' => false, // Still pending
                        'medicine_dispensed_by' => $_SESSION['user_id'], // Last person to touch it
                        'medicine_dispensed_at' => date('Y-m-d H:i:s')
                    ]);
                    error_log("Workflow status updated to 'medicine_dispensing' for patient $patient_id (some medicines still pending)");
                }
            }

            $this->pdo->commit();
            $_SESSION['success'] = 'Medicines dispensed successfully (partially or fully).';
            error_log("Medicine dispensing completed successfully for patient $patient_id");
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error in medicine dispensing: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to dispense medicines: ' . $e->getMessage();
        }

        $this->redirect('receptionist/medicine');
    }

    public function force_complete_medicine($patient_id)
    {
        if (!$patient_id) {
            $this->redirect('receptionist/medicine'); // Changed from dispense_medicines
        }

        try {
            // Mark all pending/partial prescriptions for this patient's latest visit as 'cancelled' or 'dispensed' (if 0 quantity)
            // This is a "force complete" so we assume any remaining are not being dispensed.
            $stmt_visit = $this->pdo->prepare("SELECT id FROM patient_visits WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt_visit->execute([$patient_id]);
            $visit = $stmt_visit->fetch();

            if ($visit) {
                $this->pdo->beginTransaction();
                $update_prescriptions_stmt = $this->pdo->prepare("
                    UPDATE prescriptions
                    SET status = 'cancelled', notes = 'Force completed by receptionist', updated_at = NOW()
                    WHERE visit_id = ? AND status IN ('pending', 'partial')
                ");
                $update_prescriptions_stmt->execute([$visit['id']]);

                $this->updateWorkflowStatus($patient_id, 'completed', [
                    'medicine_dispensed' => true,
                    'medicine_dispensed_by' => $_SESSION['user_id'],
                    'medicine_dispensed_at' => date('Y-m-d H:i:s')
                ]);
                $this->pdo->commit();
            } else {
                throw new Exception("No active visit found for patient to force complete medicine.");
            }

            $_SESSION['success'] = 'Patient medicine dispensing completed (forced)';
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to complete medicine dispensing: ' . $e->getMessage();
        }

        $this->redirect('receptionist/medicine');
    }


    public function add_medicine()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('receptionist/medicine');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);

        try {
            $name = trim($_POST['name']);
            $generic_name = trim($_POST['generic_name'] ?? '');
            $category = trim($_POST['category']); // UI category; we map to supplier field
            $supplier = trim($_POST['supplier'] ?? $category);
            $expiry_date = trim($_POST['expiry_date'] ?? '');
            $unit_price = floatval($_POST['unit_price']);
            $stock_quantity = intval($_POST['stock_quantity']);
            $description = trim($_POST['description'] ?? '');

            if (empty($name) || empty($category) || $unit_price <= 0 || $stock_quantity < 0) {
                throw new Exception('Please fill all required fields with valid values');
            }

            // Validate expiry date format (YYYY-MM-DD) or allow empty
            if ($expiry_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry_date)) {
                throw new Exception('Invalid expiry date format');
            }

            // Insert medicine record (no stock_quantity column in canonical schema)
            $stmt = $this->pdo->prepare("
                INSERT INTO medicines (name, generic_name, description, unit_price, supplier, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $generic_name, $description, $unit_price, ($supplier ?: null)]);
            
            $medicine_id = $this->pdo->lastInsertId();
            
            // Create initial batch if stock_quantity provided
            if ($stock_quantity > 0) {
                $batch_number = 'BATCH-' . date('Ymd') . '-' . str_pad($medicine_id, 4, '0', STR_PAD_LEFT);
                $batch_stmt = $this->pdo->prepare("
                    INSERT INTO medicine_batches 
                    (medicine_id, batch_number, quantity_received, quantity_remaining, unit_cost, expiry_date, received_date)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $batch_stmt->execute([$medicine_id, $batch_number, $stock_quantity, $stock_quantity, $unit_price, ($expiry_date ?: null)]);
            }

            $_SESSION['success'] = 'Medicine added successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to add medicine: ' . $e->getMessage();
        }

        $this->redirect('receptionist/medicine');
    }

    public function update_medicine_stock()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('receptionist/medicine');
            return;
        }

        $this->validateCSRF($_POST['csrf_token']);

        try {
            $medicine_id = intval($_POST['medicine_id']);
            $new_quantity = intval($_POST['new_quantity']);
            $action = $_POST['stock_action']; // 'add' or 'set'

            if ($medicine_id <= 0 || $new_quantity < 0) {
                throw new Exception('Invalid medicine ID or quantity');
            }

            // Get medicine details for batch creation
            $med_stmt = $this->pdo->prepare("SELECT unit_price FROM medicines WHERE id = ?");
            $med_stmt->execute([$medicine_id]);
            $medicine = $med_stmt->fetch();
            
            if (!$medicine) {
                throw new Exception('Medicine not found');
            }

            if ($action === 'add') {
                // Create new batch for added quantity
                $batch_number = 'BATCH-' . date('Ymd-His') . '-' . str_pad($medicine_id, 4, '0', STR_PAD_LEFT);
                $batch_stmt = $this->pdo->prepare("
                    INSERT INTO medicine_batches 
                    (medicine_id, batch_number, quantity_received, quantity_remaining, cost_price, expiry_date, received_date, received_by, status)
                    VALUES (?, ?, ?, ?, ?, DATE_ADD(CURDATE(), INTERVAL 2 YEAR), NOW(), ?, 'active')
                ");
                $batch_stmt->execute([$medicine_id, $batch_number, $new_quantity, $new_quantity, $medicine['unit_price'], $_SESSION['user_id'] ?? 1]);
            } else {
                // 'set' action: adjust most recent batch
                $batch_stmt = $this->pdo->prepare("
                    SELECT id FROM medicine_batches 
                    WHERE medicine_id = ? 
                    ORDER BY received_date DESC, id DESC 
                    LIMIT 1
                ");
                $batch_stmt->execute([$medicine_id]);
                $batch = $batch_stmt->fetch();
                
                if ($batch) {
                    $update_stmt = $this->pdo->prepare("
                        UPDATE medicine_batches 
                        SET quantity_remaining = ? 
                        WHERE id = ?
                    ");
                    $update_stmt->execute([$new_quantity, $batch['id']]);
                } else {
                    // No batches exist, create one
                    $batch_number = 'BATCH-' . date('Ymd-His') . '-' . str_pad($medicine_id, 4, '0', STR_PAD_LEFT);
                    $create_stmt = $this->pdo->prepare("
                        INSERT INTO medicine_batches 
                        (medicine_id, batch_number, quantity_received, quantity_remaining, cost_price, expiry_date, received_date, received_by, status)
                        VALUES (?, ?, ?, ?, ?, DATE_ADD(CURDATE(), INTERVAL 2 YEAR), NOW(), ?, 'active')
                    ");
                    $create_stmt->execute([$medicine_id, $batch_number, $new_quantity, $new_quantity, $medicine['unit_price'], $_SESSION['user_id'] ?? 1]);
                }
            }

            $_SESSION['success'] = 'Medicine stock updated successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update stock: ' . $e->getMessage();
        }

        $this->redirect('receptionist/medicine');
    }

    private function getSidebarData()
    {
        // Get pending patients count (patients with active visits)
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT v.patient_id) as pending_patients
            FROM patient_visits v
            WHERE v.status NOT IN ('completed','cancelled')
        ");
        $stmt->execute();
        $pending_patients = $stmt->fetchColumn();

        // Get today's appointments count (all statuses for today)
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as upcoming_appointments
            FROM consultations c
            JOIN patient_visits pv ON c.visit_id = pv.id
            WHERE pv.visit_date = CURDATE()
        ");
        $stmt->execute();
        $upcoming_appointments = $stmt->fetchColumn();

        // Get low stock medicines count (using medicine_batches for stock tracking)
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT m.id) as low_stock_medicines
            FROM medicines m
            LEFT JOIN medicine_batches mb ON mb.medicine_id = m.id
            GROUP BY m.id
            HAVING SUM(COALESCE(mb.quantity_remaining, 0)) <= 10 
               OR MIN(mb.expiry_date) <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ");
        $stmt->execute();
        $low_stock_medicines = $stmt->fetchColumn();

        return [
            'pending_patients' => $pending_patients ?: 0,
            'upcoming_appointments' => $upcoming_appointments ?: 0,
            'low_stock_medicines' => $low_stock_medicines ?: 0
        ];
    }

    public function reports()
    {
        // Daily revenue report
        $daily_revenue_stmt = $this->pdo->prepare("
            SELECT 
                DATE(payment_date) as date,
                COUNT(*) as payment_count,
                SUM(amount) as total_amount
            FROM payments 
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(payment_date)
            ORDER BY date DESC
        ");
        $daily_revenue_stmt->execute();
        $daily_revenue = $daily_revenue_stmt->fetchAll();

        // Patient statistics
        $patient_stats_stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_patients,
                COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as new_today,
                COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as new_week,
                COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as new_month
            FROM patients
        ");
        $patient_stats_stmt->execute();
        $patient_stats = $patient_stats_stmt->fetch();

        // Appointment statistics
        $appointment_stats_stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_appointments,
                COUNT(CASE WHEN DATE(COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) = CURDATE() THEN 1 END) as today,
                COUNT(CASE WHEN DATE(COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as this_week,
                COUNT(CASE WHEN c.status = 'completed' THEN 1 END) as completed,
                COUNT(CASE WHEN c.status = 'cancelled' THEN 1 END) as cancelled
            FROM consultations c
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
        ");
        $appointment_stats_stmt->execute();
        $appointment_stats = $appointment_stats_stmt->fetch();

        // Payment method breakdown
        $payment_methods_stmt = $this->pdo->prepare("
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount
            FROM payments 
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY payment_method
            ORDER BY total_amount DESC
        ");
        $payment_methods_stmt->execute();
        $payment_methods = $payment_methods_stmt->fetchAll();

        // Top doctors by consultations
        $top_doctors_stmt = $this->pdo->prepare("
            SELECT 
                CONCAT(u.first_name, ' ', u.last_name) as doctor_name,
                COUNT(c.id) as appointment_count,
                COUNT(CASE WHEN c.status = 'completed' THEN 1 END) as completed_count
            FROM users u
            LEFT JOIN consultations c ON u.id = c.doctor_id
            WHERE u.role = 'doctor'
            GROUP BY u.id, u.first_name, u.last_name
            ORDER BY appointment_count DESC
            LIMIT 5
        ");
        $top_doctors_stmt->execute();
        $top_doctors = $top_doctors_stmt->fetchAll();

        // Medicine inventory status (using medicine_batches)
        $medicine_stats_stmt = $this->pdo->prepare("
            SELECT 
                COUNT(DISTINCT m.id) as total_medicines,
                COUNT(DISTINCT CASE WHEN total_stock <= 10 THEN m.id END) as low_stock,
                COUNT(DISTINCT CASE WHEN earliest_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN m.id END) as expiring_soon,
                COALESCE(SUM(total_stock * m.unit_price), 0) as total_inventory_value
            FROM medicines m
            LEFT JOIN (
                SELECT medicine_id, 
                       SUM(quantity_remaining) as total_stock,
                       MIN(expiry_date) as earliest_expiry
                FROM medicine_batches
                GROUP BY medicine_id
            ) mb ON m.id = mb.medicine_id
        ");
        $medicine_stats_stmt->execute();
        $medicine_stats = $medicine_stats_stmt->fetch();

        $this->render('receptionist/reports', [
            'daily_revenue' => $daily_revenue,
            'patient_stats' => $patient_stats,
            'appointment_stats' => $appointment_stats,
            'payment_methods' => $payment_methods,
            'top_doctors' => $top_doctors,
            'medicine_stats' => $medicine_stats,
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    /**
     * Show tasks assigned to the current receptionist (or staff user)
     */
    public function tasks()
    {
        $user_id = $_SESSION['user_id'];

        // Get tasks from service_orders where this user is assigned
        try {
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
            $tasks = $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('tasks() service_orders query failed: ' . $e->getMessage());
            $tasks = [];
        }

        $this->render('receptionist/tasks', [
            'tasks' => $tasks,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    /**
     * AJAX endpoint to update a task status (start / in_progress / completed / cancelled)
     * Expects POST: task_id, status, notes (optional)
     */
    public function update_task_status()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $this->validateCSRF($_POST['csrf_token'] ?? null);

        $task_id = filter_input(INPUT_POST, 'task_id', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (!$task_id || $status === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid parameters']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            $allowed = ['pending', 'in_progress', 'completed', 'cancelled'];
            if (!in_array($status, $allowed)) {
                throw new Exception('Invalid status');
            }

            // Update service_order status
            $update_fields = ['status' => $status, 'updated_at' => 'NOW()'];
            
            // Add notes if provided
            if ($notes !== '') {
                $timestamp = date('Y-m-d H:i:s');
                $note_entry = "[{$timestamp}] {$notes}";
                $update_fields['notes'] = "CONCAT(COALESCE(notes, ''), '{$note_entry}', '\n')";
            }
            
            // Set performed_at when completed
            if ($status === 'completed') {
                $update_fields['performed_at'] = 'NOW()';
            }

            $stmt = $this->pdo->prepare("
                UPDATE service_orders 
                SET status = ?,
                    notes = CONCAT(COALESCE(notes, ''), ?),
                    performed_at = CASE WHEN ? = 'completed' THEN NOW() ELSE performed_at END,
                    updated_at = NOW()
                WHERE id = ? 
                AND performed_by = ?
            ");
            $note_entry = $notes !== '' ? "[" . date('Y-m-d H:i:s') . "] " . $notes . "\n" : '';
            $stmt->execute([$status, $note_entry, $status, $task_id, $_SESSION['user_id']]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Task not found or not assigned to you');
            }

            $this->pdo->commit();
            echo json_encode(['ok' => true]);
            exit;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('update_task_status error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public function view_patient($patient_id = null) {
        // Accept either path param or ?id= fallback
        if ($patient_id === null) {
            $patient_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
        }
        if (!$patient_id) {
            $this->redirect('receptionist/patients');
        }

        // Get patient details
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch();

        if (!$patient) {
            $this->redirect('receptionist/patients');
        }

        // Get existing consultations (basic info for receptionist)
        $stmt = $this->pdo->prepare("
            SELECT c.*, pv.visit_date, u.first_name as doctor_first, u.last_name as doctor_last
            FROM consultations c
            LEFT JOIN patient_visits pv ON c.visit_id = pv.id
            LEFT JOIN users u ON c.doctor_id = u.id
            WHERE c.patient_id = ? 
            ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at) DESC
            LIMIT 5
        ");
        $stmt->execute([$patient_id]);
        $consultations = $stmt->fetchAll();

        // Get all vital signs for this patient (for each consultation/visit)
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT vs.*, 
                   pv.visit_date, 
                   c.id as consultation_id,
                   vs.visit_id
            FROM vital_signs vs
            LEFT JOIN patient_visits pv ON vs.visit_id = pv.id
            LEFT JOIN consultations c ON (
                (vs.visit_id = c.visit_id AND vs.patient_id = c.patient_id) OR
                (vs.patient_id = c.patient_id AND DATE(vs.recorded_at) = DATE(c.created_at))
            )
            WHERE vs.patient_id = ?
            ORDER BY vs.recorded_at DESC
        ");
        $stmt->execute([$patient_id]);
        $vital_signs = $stmt->fetchAll();

        // Normalize and augment vital signs so the view's JS can read common keys
        $vital_signs_processed = [];
        if (!empty($vital_signs) && is_array($vital_signs)) {
            foreach ($vital_signs as $vs) {
                $p = $vs;
                // Build combined blood_pressure if systolic/diastolic present
                if (!empty($vs['blood_pressure_systolic']) && !empty($vs['blood_pressure_diastolic'])) {
                    $p['blood_pressure'] = $vs['blood_pressure_systolic'] . '/' . $vs['blood_pressure_diastolic'];
                } elseif (!empty($vs['blood_pressure'])) {
                    $p['blood_pressure'] = $vs['blood_pressure'];
                }
                // Normalize pulse field name
                if (!empty($vs['pulse_rate'])) {
                    $p['pulse'] = $vs['pulse_rate'];
                } elseif (!empty($vs['pulse'])) {
                    $p['pulse'] = $vs['pulse'];
                }
                // Normalize temperature
                if (empty($p['temperature']) && !empty($vs['temp'])) {
                    $p['temperature'] = $vs['temp'];
                }
                // Ensure consultation_id exists for mapping in the view
                if (empty($p['consultation_id']) && !empty($p['visit_id'])) {
                    $stmt2 = $this->pdo->prepare("SELECT id FROM consultations WHERE visit_id = ? LIMIT 1");
                    $stmt2->execute([$p['visit_id']]);
                    $crow = $stmt2->fetch();
                    if ($crow && !empty($crow['id'])) {
                        $p['consultation_id'] = $crow['id'];
                    }
                }
                $vital_signs_processed[] = $p;
            }
        }
        // Replace original for view consumption
        $vital_signs = $vital_signs_processed;

        // Get payment history for this patient
        $stmt = $this->pdo->prepare("
            SELECT p.*, pv.visit_date
            FROM payments p
            LEFT JOIN patient_visits pv ON p.visit_id = pv.id
            WHERE p.patient_id = ?
            ORDER BY p.payment_date DESC
            LIMIT 10
        ");
        $stmt->execute([$patient_id]);
        $payments = $stmt->fetchAll();

        // Get lab test orders for this patient (for real-time tracking)
        $stmt = $this->pdo->prepare("
            SELECT lto.*, lt.test_name, lt.test_code, lr.result_value, lr.result_text, lr.completed_at as result_completed_at
            FROM lab_test_orders lto
            JOIN lab_tests lt ON lto.test_id = lt.id
            LEFT JOIN lab_results lr ON lto.id = lr.order_id
            WHERE lto.patient_id = ?
            ORDER BY lto.created_at DESC
        ");
        $stmt->execute([$patient_id]);
        $lab_orders = $stmt->fetchAll();

        // Build a normalized map of latest lab results by test name for this patient
        $lab_results_map = [];
        $stmt = $this->pdo->prepare("\n                SELECT lr.*, lt.test_name\n                FROM lab_results lr\n                JOIN lab_test_orders lto ON lr.order_id = lto.id\n                JOIN lab_tests lt ON lr.test_id = lt.id\n                WHERE lto.patient_id = ?\n                ORDER BY lt.test_name ASC, lr.completed_at DESC\n            ");
        $stmt->execute([$patient_id]);
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

        $this->render('receptionist/view_patient', [
            'patient' => $patient,
            'consultations' => $consultations,
            'vital_signs' => $vital_signs,
            'payments' => $payments,
            'lab_orders' => $lab_orders,
            'lab_results_map' => $lab_results_map,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
    
    /**
     * Create a new visit for an existing patient (Patient Revisit)
     */
    public function create_revisit() {
        // Ensure only receptionists can create revisits
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'receptionist') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        // Handle POST request for creating a revisit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate CSRF token - more lenient approach
                $csrf_token = $_POST['csrf_token'] ?? '';
                if (empty($csrf_token)) {
                    throw new Exception('CSRF token is required');
                }
                
                if (!isset($_SESSION['csrf_token'])) {
                    // Generate a new token for the session if missing
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                
                if ($csrf_token !== $_SESSION['csrf_token']) {
                    // For now, let's be more lenient and regenerate the token
                    $_SESSION['csrf_token'] = $csrf_token;
                }

                // Get and validate input
                $patient_id = filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT);
                $visit_type = $this->sanitize($_POST['visit_type'] ?? 'consultation');
                $consultation_fee = filter_input(INPUT_POST, 'consultation_fee', FILTER_VALIDATE_FLOAT);
                $payment_method = $this->sanitize($_POST['payment_method'] ?? '');

                // Validate required fields
                if (!$patient_id) {
                    throw new Exception('Invalid patient ID');
                }

                // Verify patient exists
                $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
                $stmt->execute([$patient_id]);
                $patient = $stmt->fetch();
                
                if (!$patient) {
                    throw new Exception('Patient not found');
                }

                // Check if patient already has an active visit today to prevent duplicates
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) as active_visits 
                    FROM patient_visits 
                    WHERE patient_id = ? 
                    AND visit_date = CURDATE() 
                    AND status = 'active'
                ");
                $stmt->execute([$patient_id]);
                $active_count = $stmt->fetch()['active_visits'];
                
                if ($active_count > 0) {
                    throw new Exception('Patient already has an active visit today. Please check the existing visit instead of creating a new one.');
                }

                $this->pdo->beginTransaction();

                // Get the next visit number for this patient
                $stmt = $this->pdo->prepare("SELECT COALESCE(MAX(visit_number), 0) + 1 as next_visit_number FROM patient_visits WHERE patient_id = ?");
                $stmt->execute([$patient_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $visit_number = $result['next_visit_number'];

                // Create new visit record
                $stmt = $this->pdo->prepare("INSERT INTO patient_visits (patient_id, visit_number, visit_date, visit_type, registered_by, status, created_at, updated_at) VALUES (?, ?, CURDATE(), ?, ?, 'active', NOW(), NOW())");
                $stmt->execute([$patient_id, $visit_number, $visit_type, $_SESSION['user_id']]);
                $visit_id = $this->pdo->lastInsertId();

                // Record payment if provided
                if ($visit_type === 'consultation' && !empty($consultation_fee) && !empty($payment_method)) {
                    $stmt = $this->pdo->prepare(
                        "INSERT INTO payments (visit_id, patient_id, payment_type, amount, payment_method, payment_status, reference_number, collected_by, payment_date, notes) VALUES (?, ?, 'registration', ?, ?, 'paid', NULL, ?, NOW(), ?)"
                    );

                    $stmt->execute([
                        $visit_id,
                        $patient_id,
                        $consultation_fee,
                        $payment_method,
                        $_SESSION['user_id'],
                        "Revisit payment - Visit #{$visit_number}"
                    ]);

                    // Create consultation record for doctor queue
                    $default_doctor_id = 1;
                    $stmt = $this->pdo->prepare("INSERT INTO consultations (visit_id, patient_id, doctor_id, consultation_type, status, created_at) VALUES (?, ?, ?, 'new', 'pending', NOW())");
                    $stmt->execute([$visit_id, $patient_id, $default_doctor_id]);
                }

                // Record vital signs if provided (optional) - tie to this visit
                $temperature = isset($_POST['temperature']) && $_POST['temperature'] !== '' ? floatval($_POST['temperature']) : null;
                $bp_raw = isset($_POST['blood_pressure']) && $_POST['blood_pressure'] !== '' ? trim($_POST['blood_pressure']) : null; // expected format '120/80'
                $bp_systolic = null; $bp_diastolic = null;
                if ($bp_raw) {
                    if (preg_match('/^(\d{2,3})\s*\/\s*(\d{2,3})$/', $bp_raw, $m)) {
                        $bp_systolic = intval($m[1]);
                        $bp_diastolic = intval($m[2]);
                    }
                }
                $pulse_rate = isset($_POST['pulse_rate']) && $_POST['pulse_rate'] !== '' ? intval($_POST['pulse_rate']) : null;
                $weight = isset($_POST['body_weight']) && $_POST['body_weight'] !== '' ? floatval($_POST['body_weight']) : null;
                $height = isset($_POST['height']) && $_POST['height'] !== '' ? floatval($_POST['height']) : null;

                if ($temperature !== null || $bp_systolic !== null || $bp_diastolic !== null || $pulse_rate !== null || $weight !== null || $height !== null) {
                    $stmt = $this->pdo->prepare("INSERT INTO vital_signs (visit_id, patient_id, temperature, blood_pressure_systolic, blood_pressure_diastolic, pulse_rate, weight, height, recorded_by, recorded_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([
                        $visit_id,
                        $patient_id,
                        $temperature,
                        $bp_systolic,
                        $bp_diastolic,
                        $pulse_rate,
                        $weight,
                        $height,
                        $_SESSION['user_id']
                    ]);
                }

                $this->pdo->commit();
                
                // Determine next step message based on visit type and payment
                $next_step_message = '';
                if ($visit_type === 'consultation' && !empty($consultation_fee) && !empty($payment_method)) {
                    $next_step_message = ' Patient is now in the doctor\'s queue for consultation.';
                } elseif ($visit_type === 'consultation') {
                    $next_step_message = ' Payment required before consultation.';
                } elseif ($visit_type === 'lab_only') {
                    $next_step_message = ' Patient can proceed to lab for tests.';
                } else {
                    $next_step_message = ' Patient can proceed to the appropriate department.';
                }
                
                // Return success response
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => "Patient revisit created successfully! Visit #{$visit_number}." . $next_step_message,
                    'visit_id' => $visit_id,
                    'visit_number' => $visit_number,
                    'patient_name' => $patient['first_name'] . ' ' . $patient['last_name'],
                    'visit_type' => $visit_type,
                    'has_payment' => !empty($consultation_fee) && !empty($payment_method),
                    'in_doctor_queue' => ($visit_type === 'consultation' && !empty($consultation_fee) && !empty($payment_method))
                ]);
                exit;

            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                
                error_log("Revisit creation error: " . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
            return;
        }

        // If GET request, show the revisit form
        $patient_id = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT);
        $patient = null;
        $next_visit_number = 1;
        
        // If patient ID is provided, fetch patient details
        if ($patient_id) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
                $stmt->execute([$patient_id]);
                $patient = $stmt->fetch();
                
                if ($patient) {
                    // Get next visit number
                    $stmt = $this->pdo->prepare("SELECT COALESCE(MAX(visit_number), 0) + 1 as next_visit_number FROM patient_visits WHERE patient_id = ?");
                    $stmt->execute([$patient_id]);
                    $result = $stmt->fetch();
                    $next_visit_number = $result['next_visit_number'];
                }
            } catch (Exception $e) {
                error_log("Error fetching patient for revisit: " . $e->getMessage());
                $patient = null;
            }
        }
        
        $csrf_token = $this->generateCSRF();
        
        $this->render('receptionist/create_revisit', [
            'csrf_token' => $csrf_token,
            'patient_id' => $patient_id,
            'patient' => $patient,
            'next_visit_number' => $next_visit_number
        ]);
    }
    
    /**
     * Get complete patient visit history with proper visit numbers
     */
    public function get_patient_history() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'receptionist') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        $patient_id = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT);
        
        if (!$patient_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid patient ID']);
            return;
        }

        try {
            // Get patient info
            $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = ?");
            $stmt->execute([$patient_id]);
            $patient = $stmt->fetch();
            
            if (!$patient) {
                echo json_encode(['success' => false, 'message' => 'Patient not found']);
                return;
            }

            // Get complete visit history with all related data
            $stmt = $this->pdo->prepare("
                SELECT 
                    p.registration_number,
                    CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                    v.id as visit_id,
                    v.visit_number,
                    v.visit_date,
                    v.visit_type,
                    v.status as visit_status,
                    c.id as consultation_id,
                    c.diagnosis,
                    c.chief_complaint,
                    c.status as consultation_status,
                    CONCAT(doc.first_name, ' ', doc.last_name) AS doctor_name,
                    GROUP_CONCAT(DISTINCT lt.test_name ORDER BY lt.test_name) AS tests_ordered,
                    GROUP_CONCAT(DISTINCT m.name ORDER BY m.name) AS medicines_prescribed,
                    COALESCE(SUM(pay.amount), 0) AS total_paid,
                    COUNT(DISTINCT lto.id) as total_lab_tests,
                    COUNT(DISTINCT pr.id) as total_prescriptions
                FROM patient_visits v
                LEFT JOIN patients p ON v.patient_id = p.id
                LEFT JOIN consultations c ON v.id = c.visit_id
                LEFT JOIN users doc ON c.doctor_id = doc.id
                LEFT JOIN lab_test_orders lto ON v.id = lto.visit_id
                LEFT JOIN lab_tests lt ON lto.test_id = lt.id
                LEFT JOIN prescriptions pr ON v.id = pr.visit_id
                LEFT JOIN medicines m ON pr.medicine_id = m.id
                LEFT JOIN payments pay ON v.id = pay.visit_id
                WHERE v.patient_id = ?
                GROUP BY v.id, v.visit_number, c.id
                ORDER BY v.visit_number ASC
            ");
            $stmt->execute([$patient_id]);
            $visits = $stmt->fetchAll();

            // Get vital signs for each visit
            $stmt = $this->pdo->prepare("
                SELECT 
                    vs.*,
                    v.visit_number
                FROM vital_signs vs
                JOIN patient_visits v ON vs.visit_id = v.id
                WHERE vs.patient_id = ?
                ORDER BY v.visit_number ASC, vs.recorded_at DESC
            ");
            $stmt->execute([$patient_id]);
            $vital_signs = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'patient' => $patient,
                'visits' => $visits,
                'vital_signs' => $vital_signs,
                'total_visits' => count($visits)
            ]);

        } catch (Exception $e) {
            error_log("Error getting patient history: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error retrieving patient history']);
        }
    }

    /**
     * Display medicine dispensing page (moved grouping logic here)
     */
    public function medicine()
    {
        // Handle POST requests for medicine actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();

            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'dispense_patient_medicine':
                    $this->handleDispensePatientMedicine();
                    return;

                case 'add_medicine':
                    $this->add_medicine();
                    return;

                case 'update_medicine_stock':
                    $this->update_medicine_stock();
                    return;

                default:
                    $_SESSION['error'] = 'Unknown action';
                    $this->redirect('receptionist/medicine');
                    return;
            }
        }

        // GET request - display the medicine page
        // Query pending prescriptions and total medicine payments per patient
        $stmt = $this->pdo->prepare("
            SELECT 
                pr.id as prescription_id,
                pr.visit_id,
                pr.patient_id,
                pr.quantity_prescribed,
                pr.quantity_dispensed,
                pr.created_at as prescribed_at,
                p.first_name,
                p.last_name,
                p.registration_number,
                pr.medicine_id,
                m.unit_price,
                m.name as medicine_name,
                (
                    SELECT COALESCE(SUM(pay.amount), 0)
                    FROM payments pay
                    WHERE pay.patient_id = pr.patient_id 
                    AND pay.payment_type = 'medicine'
                    AND pay.payment_status = 'paid'
                ) as total_medicine_payments,
                (pr.quantity_prescribed * m.unit_price) as prescription_cost
            FROM prescriptions pr
            JOIN patients p ON pr.patient_id = p.id
            JOIN medicines m ON pr.medicine_id = m.id
            WHERE pr.status IN ('pending', 'partial')
            ORDER BY pr.created_at ASC
        ");
        $stmt->execute();
        $raw_pending_prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group prescriptions by patient for easier display in the view
        $pending_patients_grouped = [];
        foreach ($raw_pending_prescriptions as $prescription) {
            $patient_id = $prescription['patient_id'];
            if (!isset($pending_patients_grouped[$patient_id])) {
                $pending_patients_grouped[$patient_id] = [
                    'id' => $patient_id,
                    'patient_id' => $patient_id,
                    'first_name' => $prescription['first_name'],
                    'last_name' => $prescription['last_name'],
                    'registration_number' => $prescription['registration_number'],
                    'visit_id' => $prescription['visit_id'],
                    'prescribed_at' => $prescription['prescribed_at'],
                    'total_cost' => 0, // This will be total cost of prescribed medicines
                    'total_paid' => $prescription['total_medicine_payments'], // Total amount paid for medicines
                    'medicine_count' => 0,
                    'prescriptions' => []
                ];
            }
            // Add prescription cost to total
            $pending_patients_grouped[$patient_id]['total_cost'] += $prescription['prescription_cost'];
            $pending_patients_grouped[$patient_id]['prescriptions'][] = $prescription;
            $pending_patients_grouped[$patient_id]['medicine_count'] = count($pending_patients_grouped[$patient_id]['prescriptions']);
        }

        $pending_patients = array_values($pending_patients_grouped);

        // Get all medicines for inventory management (use medicine_batches for stock)
        $stmt = $this->pdo->prepare("\n            SELECT m.id, m.name, m.generic_name, \n                   m.unit AS category, \n                   m.unit_price,\n                   m.strength,\n                   COALESCE(SUM(mb.quantity_remaining), 0) as stock_quantity,\n                   MIN(mb.expiry_date) as expiry_date,\n                   COALESCE(SUM(pr.quantity_prescribed), 0) as total_prescribed,\n                   GROUP_CONCAT(DISTINCT mb.supplier SEPARATOR ', ') as suppliers\n            FROM medicines m\n            LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id\n            LEFT JOIN prescriptions pr ON m.id = pr.medicine_id\n            GROUP BY m.id, m.name, m.generic_name, m.unit, m.unit_price, m.strength\n            ORDER BY m.name\n        ");
        $stmt->execute();
        $medicines = $stmt->fetchAll();

        // Build expiry notifications (expired or within 60 days)
        $notifications = [];
        $today = new DateTime('today');
        foreach ($medicines as $med) {
            if (!empty($med['expiry_date'])) {
                try {
                    $exp = new DateTime($med['expiry_date']);
                    $diff = (int)$today->diff($exp)->format('%r%a'); // negative if expired
                    if ($diff < 0) {
                        $notifications[] = [
                            'type' => 'error',
                            'icon' => 'fa-skull-crossbones',
                            'title' => 'Medicine expired',
                            'message' => $med['name'] . ' expired ' . abs($diff) . ' day(s) ago',
                        ];
                    } elseif ($diff <= 60) {
                        $tier = $diff <= 7 ? 'warning' : ($diff <= 30 ? 'warning' : 'info');
                        $notifications[] = [
                            'type' => $tier,
                            'icon' => 'fa-exclamation-triangle',
                            'title' => 'Medicine near expiry',
                            'message' => $med['name'] . ' expires in ' . $diff . ' day(s)',
                        ];
                    }
                } catch (Exception $e) {
                    // ignore bad date
                }
            }
        }

        // Get medicine categories (using unit types as categories)
        $categories = $this->pdo->query("SELECT DISTINCT unit FROM medicines WHERE unit IS NOT NULL ORDER BY unit")->fetchAll(PDO::FETCH_COLUMN);

        // Get recent medicine transactions (recently dispensed prescriptions)
        $stmt = $this->pdo->prepare("\n            SELECT p.first_name, p.last_name,\n                   CONCAT(p.first_name, ' ', p.last_name) as patient_name,\n                   m.name as medicine_name,\n                   pr.quantity_dispensed as quantity,\n                   m.unit_price,\n                   (pr.quantity_dispensed * m.unit_price) as total_cost,\n                   pr.dispensed_at,\n                   u.first_name as dispensed_by\n            FROM prescriptions pr\n            JOIN patients p ON pr.patient_id = p.id\n            JOIN medicines m ON pr.medicine_id = m.id\n            LEFT JOIN users u ON pr.dispensed_by = u.id\n            WHERE pr.status = 'dispensed' AND pr.dispensed_at IS NOT NULL\n            ORDER BY pr.dispensed_at DESC\n            LIMIT 10\n        ");
        $stmt->execute();
        $recent_transactions = $stmt->fetchAll();

        // NOTE: keep $pending_patients computed above from grouped prescriptions.
        // Removed duplicated/overwriting query that used an undefined $this->db property.

        // Pass to view (existing render call - extend data passed)
        $this->render('receptionist/medicine', [
            'pending_patients' => $pending_patients,
            'medicines' => $medicines,
            'categories' => $categories,
            'recent_transactions' => $recent_transactions,
            'csrf_token' => $this->generateCSRF(),
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    /**
     * Handle dispensing all medicines for a patient
     */
   
    private function handleDispensePatientMedicine()
    {
        $patient_id = $_POST['patient_id'] ?? null;

        if (!$patient_id) {
            $_SESSION['error'] = 'Patient ID is required';
            $this->redirect('receptionist/medicine');
            return;
        }

        // Get all pending prescriptions for this patient
        $stmt = $this->pdo->prepare("
            SELECT pr.id, pr.quantity_prescribed, pr.quantity_dispensed
            FROM prescriptions pr
            WHERE pr.patient_id = ? AND pr.status IN ('pending', 'partial')
        ");
        $stmt->execute([$patient_id]);
        $prescriptions = $stmt->fetchAll();

        if (empty($prescriptions)) {
            $_SESSION['error'] = 'No pending prescriptions found for this patient';
            $this->redirect('receptionist/medicine');
            return;
        }

        // Build dispensed_items array with remaining quantities to dispense
        $dispensed_items = [];
        foreach ($prescriptions as $prescription) {
            $remaining = $prescription['quantity_prescribed'] - $prescription['quantity_dispensed'];
            if ($remaining > 0) {
                $dispensed_items[$prescription['id']] = $remaining;
            }
        }

        if (empty($dispensed_items)) {
            $_SESSION['error'] = 'No medicines remaining to dispense for this patient';
            $this->redirect('receptionist/medicine');
            return;
        }

        // Set the POST data for process_medicine_dispensing
        $_POST['dispensed_items'] = $dispensed_items;

        // Call the existing dispensing method
        $this->process_medicine_dispensing();
    }

   public function prescription_details() 
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    $patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;
    if (!$patient_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing patient_id']);
        exit;
    }

    try {
        // Get medicines prescribed for this patient that are pending/partial
        $stmt = $this->pdo->prepare("
            SELECT 
                m.name as medicine_name,
                pr.quantity_prescribed,
                m.unit_price,
                (pr.quantity_prescribed * m.unit_price) as medicine_cost
            FROM prescriptions pr
            JOIN medicines m ON pr.medicine_id = m.id
            WHERE pr.patient_id = ?
            AND pr.status IN ('pending', 'partial')
            ORDER BY pr.created_at DESC
        ");
        
        $stmt->execute([$patient_id]);
        $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total_cost = array_sum(array_column($medicines, 'medicine_cost'));

        header('Content-Type: application/json');
        echo json_encode([
            'medicines' => $medicines,
            'total_cost' => $total_cost
        ]);
        exit;

    } catch (Exception $e) {
        error_log("Error in prescription_details: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}
}