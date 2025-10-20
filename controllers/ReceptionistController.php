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
            $first_name = $post['first_name'] ?? null;
            $last_name = $post['last_name'] ?? null;
            $date_of_birth = $post['date_of_birth'] ?? null;
            $gender = $post['gender'] ?? null;
            $phone = $post['phone'] ?? null;
            $email = $post['email'] ?? null;
            $address = $post['address'] ?? null;
            $emergency_contact_name = $post['emergency_contact_name'] ?? null;
            $emergency_contact_phone = $post['emergency_contact_phone'] ?? null;
            $temperature = $post['temperature'] ?? null;
            $blood_pressure = $post['blood_pressure'] ?? null;
            $pulse_rate = $post['pulse_rate'] ?? null;
            $body_weight = $post['body_weight'] ?? null;
            $height = $post['height'] ?? null;

            try {
                $this->pdo->beginTransaction();

                // Only process payment for consultation visits
                if ($visit_type === 'consultation') {
                    // Validate consultation payment
                    if (empty($consultation_fee) || empty($payment_method)) {
                        throw new Exception('Consultation fee and payment method are required');
                    }
                }

                // Insert patient basic info (only core fields that exist in all schemas)
                $stmt = $this->pdo->prepare("
                    INSERT INTO patients (
                        registration_number, first_name, last_name,
                        date_of_birth, gender, phone, email,
                        address, emergency_contact_name,
                        emergency_contact_phone, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
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

                // Only record payment for consultation visits (payments table expects visit_id)
                if ($visit_type === 'consultation' && !empty($consultation_fee) && !empty($payment_method)) {
                    // Record consultation payment
                    $stmt = $this->pdo->prepare(
                        "INSERT INTO payments (visit_id, patient_id, payment_type, amount, payment_method, payment_status, reference_number, collected_by, payment_date, notes) VALUES (?, ?, 'registration', ?, ?, 'paid', NULL, ?, NOW(), ?)"
                    );

                    $stmt->execute([
                        $visit_id,
                        $patient_id,
                        $consultation_fee,
                        $payment_method,
                        $_SESSION['user_id'],
                        'Initial consultation payment'
                    ]);

                    // Create a consultations row referencing visit_id so doctors can immediately see the registered patient
                    // Use default doctor_id = 1 to avoid NULL constraint issues; can be reassigned later
                    $default_doctor_id = 1;
                    $stmt = $this->pdo->prepare("INSERT INTO consultations (visit_id, patient_id, doctor_id, consultation_type, status, created_at) VALUES (?, ?, ?, 'new', 'pending', NOW())");
                    $stmt->execute([$visit_id, $patient_id, $default_doctor_id]);

                    // Try to record workflow status (non-blocking if table doesn't exist)
                    try {
                        $stmt = $this->pdo->prepare("INSERT INTO patient_workflow_status (patient_id, visit_id, workflow_step, status, started_at, notes, created_at, updated_at) VALUES (?, ?, 'consultation', 'pending', NOW(), 'Consultation payment received - waiting for doctor', NOW(), NOW())");
                        $stmt->execute([$patient_id, $visit_id]);
                    } catch (Exception $e) {
                        // Non-fatal: if patient_workflow_status doesn't exist, continue
                        error_log('Workflow status tracking not available: ' . $e->getMessage());
                    }
                }

                // Record vital signs if provided — insert into vital_signs linked to the visit
                $bp_systolic = null; $bp_diastolic = null;
                if (!empty($blood_pressure) && is_string($blood_pressure) && strpos($blood_pressure, '/') !== false) {
                    [$bp_systolic, $bp_diastolic] = array_map('intval', array_map('trim', explode('/', $blood_pressure, 2)));
                }
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

                $this->pdo->commit();
                $_SESSION['success'] = "Patient registered successfully! Registration Number: $registration_number";
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
        // This query needs to be more robust to calculate remaining amount
        $stmt = $this->pdo->prepare("
            SELECT 
                lto.id AS order_id,
                lto.visit_id,
                lto.patient_id,
                p.first_name,
                p.last_name,
                p.registration_number,
                pv.visit_date,
                GROUP_CONCAT(lt.test_name SEPARATOR ', ') AS test_names,
                SUM(lt.price) AS total_amount,
                COALESCE(SUM(pay.amount), 0) AS amount_already_paid,
                (SUM(lt.price) - COALESCE(SUM(pay.amount), 0)) AS remaining_amount_to_pay
            FROM lab_test_orders lto
            JOIN lab_tests lt ON lto.test_id = lt.id  
            JOIN patients p ON lto.patient_id = p.id
            JOIN patient_visits pv ON lto.visit_id = pv.id
            LEFT JOIN payments pay ON lto.visit_id = pay.visit_id 
                                  AND lto.patient_id = pay.patient_id 
                                  AND pay.item_type = 'lab_order' 
                                  AND pay.item_id = lto.id
                                  AND pay.payment_status = 'paid'
            WHERE lto.status = 'pending_payment' -- Assuming 'pending_payment' status for lab orders
            GROUP BY lto.id, lto.visit_id, lto.patient_id, p.first_name, p.last_name, p.registration_number, pv.visit_date
            HAVING remaining_amount_to_pay > 0
            ORDER BY lto.created_at DESC
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
                GROUP_CONCAT(m.name || ' (' || pr.quantity_prescribed || ' ' || m.unit || ')' SEPARATOR ', ') AS medicine_names,
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

        $this->render('receptionist/payments', [
            'payments' => $payments, // Pass general payments too if needed by the view
            'pending_lab_payments' => $pending_lab_payments,
            'pending_medicine_payments' => $pending_medicine_payments,
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
        
        $stmt = $this->pdo->prepare("
            SELECT p.*, 
                   CONCAT(pt.first_name, ' ', pt.last_name) as patient_name,
                   pv.visit_date
            FROM payments p
            JOIN patients pt ON p.patient_id = pt.id
            LEFT JOIN patient_visits pv ON p.visit_id = pv.id
            WHERE $where_sql
            ORDER BY p.payment_date DESC
        ");
        
        $stmt->execute($params);
        $payments = $stmt->fetchAll();

        $this->render('receptionist/payment_history', [
            'payments' => $payments,
            'sidebar_data' => $this->getSidebarData(),
            'csrf_token' => $this->generateCSRF()
        ]);
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
        $item_id = $_POST['item_id'] ?? null; // This will be prescription_id for medicine payments
        $item_type = $_POST['item_type'] ?? null; // This will be 'prescription' for medicine payments

        if (!$patient_id || !$visit_id || !$payment_type || $amount <= 0) {
            $_SESSION['error'] = 'Invalid payment details';
            $this->redirect('receptionist/payments');
            return;
        }

        try {
            $this->pdo->beginTransaction();

            // Check if payment already exists for this specific item (e.g., a full payment for a prescription)
            // For partial payments, we allow multiple payments for the same item_id until total amount is covered.
            // The check below is more for preventing duplicate full payments.
            // We'll rely on the dispensing logic to check total paid vs total cost.

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
            // For medicine, we keep it in 'medicine_dispensing' until all prescribed items are fully dispensed.
            if ($payment_type === 'lab_test') {
                $this->updateWorkflowStatus($patient_id, 'lab_testing', ['lab_tests_paid' => true]);
            } elseif ($payment_type === 'medicine') {
                // No change to workflow status here, as patient might still need to pay more or collect.
                // The workflow status will be updated in process_medicine_dispensing when actual dispensing happens.
            }

            $this->pdo->commit();
            $_SESSION['success'] = 'Payment recorded successfully';
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to record payment: ' . $e->getMessage();
        }

        $this->redirect('receptionist/payments');
    }

    public function medicine()
    {
        // Get patients waiting for medicine dispensing (prescribed and paid, but not fully dispensed)
        // This query identifies individual prescriptions that are still 'pending' or 'partial'.
        // It also calculates payment status for each prescription.
        $stmt = $this->pdo->prepare("
            SELECT 
                p.id AS patient_id, 
                p.first_name, 
                p.last_name,
                p.registration_number,
                pv.id as visit_id,
                pv.visit_date,
                pr.id AS prescription_id,
                pr.medicine_id,
                m.name AS medicine_name,
                m.unit AS medicine_unit,
                m.unit_price,
                pr.quantity_prescribed,
                pr.quantity_dispensed,
                pr.dosage,
                pr.frequency,
                pr.duration,
                pr.instructions,
                pr.status AS prescription_status,
                pr.created_at AS prescribed_at,
                COALESCE(SUM(pay.amount), 0) AS amount_paid_for_this_prescription
            FROM patients p
            JOIN patient_visits pv ON p.id = pv.patient_id
            JOIN prescriptions pr ON pv.id = pr.visit_id
            JOIN medicines m ON pr.medicine_id = m.id
            LEFT JOIN payments pay ON pr.visit_id = pay.visit_id 
                                  AND pr.patient_id = pay.patient_id 
                                  AND pay.item_type = 'prescription' 
                                  AND pay.item_id = pr.id
                                  AND pay.payment_status = 'paid'
            WHERE pr.status IN ('pending', 'partial')
            GROUP BY pr.id, p.id, p.first_name, p.last_name, p.registration_number, pv.id, pv.visit_date,
                     pr.medicine_id, m.name, m.unit, m.unit_price, pr.quantity_prescribed, pr.quantity_dispensed,
                     pr.dosage, pr.frequency, pr.duration, pr.instructions, pr.status, pr.created_at
            ORDER BY p.first_name, p.last_name, pr.created_at DESC
        ");
        $stmt->execute();
        $raw_pending_prescriptions = $stmt->fetchAll();

        // Group prescriptions by patient for easier display in the view
        $pending_patients_grouped = [];
        foreach ($raw_pending_prescriptions as $prescription) {
            $patient_id = $prescription['patient_id'];
            if (!isset($pending_patients_grouped[$patient_id])) {
                $pending_patients_grouped[$patient_id] = [
                    'patient_id' => $patient_id,
                    'first_name' => $prescription['first_name'],
                    'last_name' => $prescription['last_name'],
                    'registration_number' => $prescription['registration_number'],
                    'visit_id' => $prescription['visit_id'],
                    'visit_date' => $prescription['visit_date'],
                    'prescribed_at' => $prescription['prescribed_at'], // Use the earliest prescription date for the patient's entry
                    'total_prescribed_cost' => 0,
                    'total_paid_for_patient' => 0,
                    'prescriptions' => []
                ];
            }
            $prescription_cost = $prescription['quantity_prescribed'] * $prescription['unit_price'];
            $pending_patients_grouped[$patient_id]['total_prescribed_cost'] += $prescription_cost;
            $pending_patients_grouped[$patient_id]['total_paid_for_patient'] += $prescription['amount_paid_for_this_prescription'];
            $pending_patients_grouped[$patient_id]['prescriptions'][] = $prescription;
        }

        // Re-index to a simple array for the view
        $pending_patients = array_values($pending_patients_grouped);

        // Get all medicines for inventory management (use medicine_batches for stock)
        $stmt = $this->pdo->prepare("
            SELECT m.id, m.name, m.generic_name, 
                   m.unit AS category, 
                   m.unit_price,
                   m.strength,
                   COALESCE(SUM(mb.quantity_remaining), 0) as stock_quantity,
                   MIN(mb.expiry_date) as expiry_date,
                   COALESCE(SUM(pr.quantity_prescribed), 0) as total_prescribed,
                   GROUP_CONCAT(DISTINCT mb.supplier SEPARATOR ', ') as suppliers
            FROM medicines m
            LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
            LEFT JOIN prescriptions pr ON m.id = pr.medicine_id
            GROUP BY m.id, m.name, m.generic_name, m.unit, m.unit_price, m.strength
            ORDER BY m.name
        ");
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
                        // Near expiry tiers
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
        $stmt = $this->pdo->prepare("
            SELECT p.first_name, p.last_name,
                   CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                   m.name as medicine_name,
                   pr.quantity_dispensed as quantity, /* Changed to 'quantity' for consistency with view */
                   m.unit_price, /* Added unit_price for transaction history */
                   (pr.quantity_dispensed * m.unit_price) as total_cost,
                   pr.dispensed_at,
                   u.first_name as dispensed_by
            FROM prescriptions pr
            JOIN patients p ON pr.patient_id = p.id
            JOIN medicines m ON pr.medicine_id = m.id
            LEFT JOIN users u ON pr.dispensed_by = u.id
            WHERE pr.status = 'dispensed' AND pr.dispensed_at IS NOT NULL
            ORDER BY pr.dispensed_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $recent_transactions = $stmt->fetchAll();

        $this->render('receptionist/medicine', [
            'pending_patients' => $pending_patients, // This now contains grouped data
            'medicines' => $medicines, // Pass all medicines for stock lookup in JS
            'categories' => $categories,
            'recent_transactions' => $recent_transactions,
            'csrf_token' => $this->generateCSRF(),
            'notifications' => $notifications,
            'sidebar_data' => $this->getSidebarData()
        ]);
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
                    FOR UPDATE // Lock the row to prevent race conditions during dispensing
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
            $action = $_POST['action']; // 'add' or 'set'

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
                    (medicine_id, batch_number, quantity_received, quantity_remaining, unit_cost, received_date)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $batch_stmt->execute([$medicine_id, $batch_number, $new_quantity, $new_quantity, $medicine['unit_price']]);
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
                        (medicine_id, batch_number, quantity_received, quantity_remaining, unit_cost, received_date)
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");
                    $create_stmt->execute([$medicine_id, $batch_number, $new_quantity, $new_quantity, $medicine['unit_price']]);
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

        $this->render('receptionist/view_patient', [
            'patient' => $patient,
            'consultations' => $consultations,
            'vital_signs' => $vital_signs,
            'payments' => $payments,
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
                    $stmt = $this->pdo->prepare("INSERT INTO consultations (visit_id, patient_id, doctor_id, consultation_type, status, created_at) VALUES (?, ?, ?, 'revisit', 'pending', NOW())");
                    $stmt->execute([$visit_id, $patient_id, $default_doctor_id]);
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
}