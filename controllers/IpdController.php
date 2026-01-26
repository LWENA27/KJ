<?php
/**
 * IpdController
 * Handles In-Patient Department operations including admissions, bed management, 
 * progress notes, and medication administration
 */

require_once __DIR__ . '/../includes/BaseController.php';

class IpdController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        // Allow multiple roles to access IPD
        $this->requireRole(['nurse', 'receptionist', 'doctor', 'admin']);
    }

    /**
     * Dashboard - Show bed availability, active admissions, and statistics
     */
    public function dashboard() {
        try {
            // Get ward statistics
            $stmt = $this->db->query("
                SELECT 
                    id,
                    ward_name,
                    ward_type,
                    total_beds,
                    occupied_beds,
                    (total_beds - occupied_beds) as available_beds
                FROM ipd_wards
                WHERE is_active = 1
                ORDER BY ward_name
            ");
            $wards = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get active admissions count
            $stmt = $this->db->query("
                SELECT COUNT(*) as active_count 
                FROM ipd_admissions 
                WHERE status = 'active'
            ");
            $active_admissions = $stmt->fetch(PDO::FETCH_ASSOC)['active_count'];
            
            // Get today's admissions
            $stmt = $this->db->query("
                SELECT 
                    ia.id,
                    ia.admission_number,
                    ia.admission_datetime,
                    ia.admission_type,
                    p.first_name,
                    p.last_name,
                    p.registration_number,
                    ib.bed_number,
                    iw.ward_name
                FROM ipd_admissions ia
                JOIN patients p ON ia.patient_id = p.id
                JOIN ipd_beds ib ON ia.bed_id = ib.id
                JOIN ipd_wards iw ON ib.ward_id = iw.id
                WHERE DATE(ia.admission_datetime) = CURDATE()
                ORDER BY ia.admission_datetime DESC
            ");
            $todays_admissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get pending medications
            $stmt = $this->db->query("
                SELECT COUNT(*) as pending_count
                FROM ipd_medication_admin
                WHERE status = 'scheduled'
                AND scheduled_datetime <= NOW()
            ");
            $pending_medications = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];
            
            $this->render('ipd/dashboard', [
                'wards' => $wards,
                'active_admissions' => $active_admissions,
                'todays_admissions' => $todays_admissions,
                'pending_medications' => $pending_medications
            ]);
            
        } catch (Exception $e) {
            error_log("IPD dashboard error: " . $e->getMessage());
            $_SESSION['error'] = "Error loading dashboard";
            header('Location: /');
            exit;
        }
    }

    /**
     * Beds - Manage wards and bed inventory
     */
    public function beds() {
        try {
            $ward_id = $_GET['ward_id'] ?? null;
            
            // Get all wards
            $stmt = $this->db->query("
                SELECT * FROM ipd_wards 
                WHERE is_active = 1 
                ORDER BY ward_name
            ");
            $wards = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get beds for selected ward or all wards
            $query = "
                SELECT 
                    ib.*,
                    iw.ward_name,
                    iw.ward_type,
                    ia.patient_id,
                    p.first_name,
                    p.last_name,
                    p.registration_number
                FROM ipd_beds ib
                JOIN ipd_wards iw ON ib.ward_id = iw.id
                LEFT JOIN ipd_admissions ia ON ib.id = ia.bed_id AND ia.status = 'active'
                LEFT JOIN patients p ON ia.patient_id = p.id
                WHERE ib.is_active = 1
            ";
            
            if ($ward_id) {
                $query .= " AND ib.ward_id = ?";
                $stmt = $this->db->prepare($query . " ORDER BY ib.bed_number");
                $stmt->execute([$ward_id]);
            } else {
                $stmt = $this->db->query($query . " ORDER BY iw.ward_name, ib.bed_number");
            }
            
            $beds = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->render('ipd/beds', [
                'wards' => $wards,
                'beds' => $beds,
                'selected_ward_id' => $ward_id
            ]);
            
        } catch (Exception $e) {
            error_log("IPD beds error: " . $e->getMessage());
            $_SESSION['error'] = "Error loading beds";
            header('Location: ' . BASE_PATH . '/ipd/dashboard');
            exit;
        }
    }

    /**
     * Admit - Admit patient to IPD
     */
    public function admit($visit_id = null) {
        try {
            // If visit_id provided, get patient details
            $patient = null;
            $visit = null;
            
            if ($visit_id) {
                $stmt = $this->db->prepare("
                    SELECT pv.*, p.* 
                    FROM patient_visits pv
                    JOIN patients p ON pv.patient_id = p.id
                    WHERE pv.id = ?
                ");
                $stmt->execute([$visit_id]);
                $visit = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($visit) {
                    $patient = $visit;
                }
            }
            
            // Get available beds
            $stmt = $this->db->query("
                SELECT 
                    ib.id,
                    ib.bed_number,
                    ib.bed_type,
                    ib.daily_rate,
                    iw.ward_name,
                    iw.ward_type
                FROM ipd_beds ib
                JOIN ipd_wards iw ON ib.ward_id = iw.id
                WHERE ib.status = 'available'
                AND ib.is_active = 1
                ORDER BY iw.ward_name, ib.bed_number
            ");
            $available_beds = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get doctors for attending physician selection
            $stmt = $this->db->query("
                SELECT id, first_name, last_name 
                FROM users 
                WHERE role = 'doctor' 
                AND is_active = 1
                ORDER BY first_name, last_name
            ");
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // If POST, process admission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRF();
                
                $patient_id = $_POST['patient_id'] ?? null;
                $visit_id = $_POST['visit_id'] ?? null;
                $bed_id = $_POST['bed_id'] ?? null;
                $admission_type = $_POST['admission_type'] ?? 'planned';
                $admission_diagnosis = trim($_POST['admission_diagnosis'] ?? '');
                $attending_doctor = $_POST['attending_doctor'] ?? null;
                
                // Validate required fields
                if (empty($patient_id) || empty($bed_id) || empty($admission_diagnosis)) {
                    $_SESSION['error'] = "Patient, bed, and diagnosis are required";
                    header("Location: /ipd/admit" . ($visit_id ? "/$visit_id" : ""));
                    exit;
                }
                
                $this->db->beginTransaction();
                
                try {
                    // Generate admission number
                    $admission_number = 'IPD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    
                    // Insert admission
                    $stmt = $this->db->prepare("
                        INSERT INTO ipd_admissions (
                            patient_id, visit_id, bed_id, admission_number,
                            admission_datetime, admission_type, admission_diagnosis,
                            admitted_by, attending_doctor, status
                        ) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, 'active')
                    ");
                    $stmt->execute([
                        $patient_id, $visit_id, $bed_id, $admission_number,
                        $admission_type, $admission_diagnosis,
                        $_SESSION['user_id'], $attending_doctor
                    ]);
                    
                    $admission_id = $this->db->lastInsertId();
                    
                    // Update bed status
                    $stmt = $this->db->prepare("
                        UPDATE ipd_beds 
                        SET status = 'occupied' 
                        WHERE id = ?
                    ");
                    $stmt->execute([$bed_id]);
                    
                    // Update ward occupied count
                    $stmt = $this->db->prepare("
                        UPDATE ipd_wards 
                        SET occupied_beds = occupied_beds + 1 
                        WHERE id = (SELECT ward_id FROM ipd_beds WHERE id = ?)
                    ");
                    $stmt->execute([$bed_id]);
                    
                    // Create IPD visit record if not exists
                    if (!$visit_id) {
                        $stmt = $this->db->prepare("
                            INSERT INTO patient_visits (
                                patient_id, visit_date, visit_type, created_by
                            ) VALUES (?, NOW(), 'ipd', ?)
                        ");
                        $stmt->execute([$patient_id, $_SESSION['user_id']]);
                        $visit_id = $this->db->lastInsertId();
                        
                        // Update admission with visit_id
                        $stmt = $this->db->prepare("
                            UPDATE ipd_admissions 
                            SET visit_id = ? 
                            WHERE id = ?
                        ");
                        $stmt->execute([$visit_id, $admission_id]);
                    }
                    
                    $this->db->commit();
                    
                    $_SESSION['success'] = "Patient admitted successfully. Admission #: $admission_number";
                    header("Location: /ipd/view_admission/$admission_id");
                    exit;
                    
                } catch (Exception $e) {
                    $this->db->rollBack();
                    throw $e;
                }
            }
            
            $this->render('ipd/admit', [
                'patient' => $patient,
                'visit' => $visit,
                'available_beds' => $available_beds,
                'doctors' => $doctors
            ]);
            
        } catch (Exception $e) {
            error_log("IPD admit error: " . $e->getMessage());
            $_SESSION['error'] = "Error processing admission";
            header('Location: ' . BASE_PATH . '/ipd/dashboard');
            exit;
        }
    }

    /**
     * Admissions - List all admissions with filtering
     */
    public function admissions() {
        try {
            $status = $_GET['status'] ?? 'active';
            $search = $_GET['search'] ?? '';
            
            $query = "
                SELECT 
                    ia.*,
                    p.first_name,
                    p.last_name,
                    p.registration_number,
                    YEAR(CURDATE()) - YEAR(p.date_of_birth) - (DATE_FORMAT(p.date_of_birth, '%m%d') > DATE_FORMAT(CURDATE(), '%m%d')) as age,
                    p.gender,
                    ib.bed_number,
                    iw.ward_name,
                    u.first_name as doctor_first_name,
                    u.last_name as doctor_last_name
                FROM ipd_admissions ia
                JOIN patients p ON ia.patient_id = p.id
                JOIN ipd_beds ib ON ia.bed_id = ib.id
                JOIN ipd_wards iw ON ib.ward_id = iw.id
                LEFT JOIN users u ON ia.attending_doctor = u.id
                WHERE 1=1
            ";
            
            $params = [];
            
            if ($status !== 'all') {
                $query .= " AND ia.status = ?";
                $params[] = $status;
            }
            
            if (!empty($search)) {
                $query .= " AND (ia.admission_number LIKE ? OR p.registration_number LIKE ? OR p.first_name LIKE ? OR p.last_name LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $query .= " ORDER BY ia.admission_datetime DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $admissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->render('ipd/admissions', [
                'admissions' => $admissions,
                'status' => $status,
                'search' => $search
            ]);
            
        } catch (Exception $e) {
            error_log("IPD admissions error: " . $e->getMessage());
            $_SESSION['error'] = "Error loading admissions";
            header('Location: ' . BASE_PATH . '/ipd/dashboard');
            exit;
        }
    }

    /**
     * View Admission - Display full admission details with notes and medications
     */
    public function viewAdmission($admission_id) {
        try {
            // Get admission details
            $stmt = $this->db->prepare("
                SELECT 
                    ia.*,
                    p.*,
                    ib.bed_number,
                    ib.bed_type,
                    ib.daily_rate,
                    iw.ward_name,
                    iw.ward_type,
                    u1.first_name as admitted_by_first_name,
                    u1.last_name as admitted_by_last_name,
                    u2.first_name as doctor_first_name,
                    u2.last_name as doctor_last_name
                FROM ipd_admissions ia
                JOIN patients p ON ia.patient_id = p.id
                JOIN ipd_beds ib ON ia.bed_id = ib.id
                JOIN ipd_wards iw ON ib.ward_id = iw.id
                JOIN users u1 ON ia.admitted_by = u1.id
                LEFT JOIN users u2 ON ia.attending_doctor = u2.id
                WHERE ia.id = ?
            ");
            $stmt->execute([$admission_id]);
            $admission = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admission) {
                $_SESSION['error'] = "Admission not found";
                header('Location: ' . BASE_PATH . '/ipd/admissions');
                exit;
            }
            
            // Get progress notes
            $stmt = $this->db->prepare("
                SELECT 
                    ipn.*,
                    u.first_name,
                    u.last_name,
                    u.role
                FROM ipd_progress_notes ipn
                JOIN users u ON ipn.recorded_by = u.id
                WHERE ipn.admission_id = ?
                ORDER BY ipn.note_datetime DESC
            ");
            $stmt->execute([$admission_id]);
            $progress_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get medications
            $stmt = $this->db->prepare("
                SELECT 
                    ima.*,
                    m.name as medicine_name,
                    m.dosage_form,
                    u1.first_name as prescribed_by_first_name,
                    u1.last_name as prescribed_by_last_name,
                    u2.first_name as administered_by_first_name,
                    u2.last_name as administered_by_last_name
                FROM ipd_medication_admin ima
                JOIN medicines m ON ima.medicine_id = m.id
                JOIN users u1 ON ima.prescribed_by = u1.id
                LEFT JOIN users u2 ON ima.administered_by = u2.id
                WHERE ima.admission_id = ?
                ORDER BY ima.scheduled_datetime DESC
            ");
            $stmt->execute([$admission_id]);
            $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->render('ipd/view_admission', [
                'admission' => $admission,
                'progress_notes' => $progress_notes,
                'medications' => $medications
            ]);
            
        } catch (Exception $e) {
            error_log("View admission error: " . $e->getMessage());
            $_SESSION['error'] = "Error viewing admission";
            header('Location: ' . BASE_PATH . '/ipd/admissions');
            exit;
        }
    }

    /**
     * Record Progress Note - Add nurse/doctor observation
     */
    public function recordProgressNote($admission_id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRF();
                
                $note_type = $_POST['note_type'] ?? 'nurse';
                $temperature = $_POST['temperature'] ?? null;
                $bp_systolic = $_POST['bp_systolic'] ?? null;
                $bp_diastolic = $_POST['bp_diastolic'] ?? null;
                $pulse_rate = $_POST['pulse_rate'] ?? null;
                $respiratory_rate = $_POST['respiratory_rate'] ?? null;
                $oxygen_saturation = $_POST['oxygen_saturation'] ?? null;
                $progress_note = trim($_POST['progress_note'] ?? '');
                
                // Get patient_id from admission
                $stmt = $this->db->prepare("SELECT patient_id FROM ipd_admissions WHERE id = ?");
                $stmt->execute([$admission_id]);
                $patient_id = $stmt->fetchColumn();
                
                if (!$patient_id) {
                    $_SESSION['error'] = "Admission not found";
                    header("Location: /ipd/admissions");
                    exit;
                }
                
                // Insert progress note
                $stmt = $this->db->prepare("
                    INSERT INTO ipd_progress_notes (
                        admission_id, patient_id, note_datetime, note_type,
                        temperature, blood_pressure_systolic, blood_pressure_diastolic,
                        pulse_rate, respiratory_rate, oxygen_saturation,
                        progress_note, recorded_by
                    ) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $admission_id, $patient_id, $note_type,
                    $temperature, $bp_systolic, $bp_diastolic,
                    $pulse_rate, $respiratory_rate, $oxygen_saturation,
                    $progress_note, $_SESSION['user_id']
                ]);
                
                $_SESSION['success'] = "Progress note recorded successfully";
                header("Location: /ipd/view_admission/$admission_id");
                exit;
            }
            
            header("Location: /ipd/view_admission/$admission_id");
            exit;
            
        } catch (Exception $e) {
            error_log("Record progress note error: " . $e->getMessage());
            $_SESSION['error'] = "Error recording note";
            header("Location: /ipd/view_admission/$admission_id");
            exit;
        }
    }

    /**
     * Administer Medication - Mark medication as administered
     */
    public function administerMedication($medication_id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRF();
                
                $notes = trim($_POST['notes'] ?? '');
                
                // Update medication status
                $stmt = $this->db->prepare("
                    UPDATE ipd_medication_admin 
                    SET status = 'administered',
                        administered_by = ?,
                        administered_datetime = NOW(),
                        notes = ?
                    WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $notes, $medication_id]);
                
                // Get admission_id for redirect
                $stmt = $this->db->prepare("SELECT admission_id FROM ipd_medication_admin WHERE id = ?");
                $stmt->execute([$medication_id]);
                $admission_id = $stmt->fetchColumn();
                
                $_SESSION['success'] = "Medication administered successfully";
                header("Location: /ipd/view_admission/$admission_id");
                exit;
            }
            
            header("Location: /ipd/admissions");
            exit;
            
        } catch (Exception $e) {
            error_log("Administer medication error: " . $e->getMessage());
            $_SESSION['error'] = "Error administering medication";
            header("Location: /ipd/admissions");
            exit;
        }
    }

    /**
     * Discharge - Discharge patient from IPD
     */
    public function discharge($admission_id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRF();
                
                $discharge_diagnosis = trim($_POST['discharge_diagnosis'] ?? '');
                $discharge_summary = trim($_POST['discharge_summary'] ?? '');
                
                if (empty($discharge_diagnosis)) {
                    $_SESSION['error'] = "Discharge diagnosis is required";
                    header("Location: /ipd/view_admission/$admission_id");
                    exit;
                }
                
                $this->db->beginTransaction();
                
                try {
                    // Get bed_id before updating
                    $stmt = $this->db->prepare("SELECT bed_id FROM ipd_admissions WHERE id = ?");
                    $stmt->execute([$admission_id]);
                    $bed_id = $stmt->fetchColumn();
                    
                    // Update admission
                    $stmt = $this->db->prepare("
                        UPDATE ipd_admissions 
                        SET discharge_datetime = NOW(),
                            discharge_diagnosis = ?,
                            discharge_summary = ?,
                            discharged_by = ?,
                            status = 'discharged'
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $discharge_diagnosis, $discharge_summary,
                        $_SESSION['user_id'], $admission_id
                    ]);
                    
                    // Update bed status
                    $stmt = $this->db->prepare("
                        UPDATE ipd_beds 
                        SET status = 'available' 
                        WHERE id = ?
                    ");
                    $stmt->execute([$bed_id]);
                    
                    // Update ward occupied count
                    $stmt = $this->db->prepare("
                        UPDATE ipd_wards 
                        SET occupied_beds = occupied_beds - 1 
                        WHERE id = (SELECT ward_id FROM ipd_beds WHERE id = ?)
                    ");
                    $stmt->execute([$bed_id]);
                    
                    $this->db->commit();
                    
                    $_SESSION['success'] = "Patient discharged successfully";
                    header("Location: /ipd/admissions");
                    exit;
                    
                } catch (Exception $e) {
                    $this->db->rollBack();
                    throw $e;
                }
            }
            
            header("Location: /ipd/view_admission/$admission_id");
            exit;
            
        } catch (Exception $e) {
            error_log("Discharge error: " . $e->getMessage());
            $_SESSION['error'] = "Error discharging patient";
            header("Location: /ipd/view_admission/$admission_id");
            exit;
        }
    }
}
