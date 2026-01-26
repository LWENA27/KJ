<?php
/**
 * RadiologistController
 * Handles radiology department operations including test orders, result recording, and image management
 */

require_once __DIR__ . '/../includes/BaseController.php';

class RadiologistController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        $this->requireRole(['radiologist', 'admin']);
    }

    // Backward-compatible action aliases (snake_case) used by routing links
    public function perform_test($order_id) { return $this->performTest($order_id); }
    public function record_result($order_id) { return $this->recordResult($order_id); }
    public function view_result($result_id) { return $this->viewResult($result_id); }

    /**
     * Dashboard - Show pending orders, today's schedule, and statistics
     */
    public function dashboard() {
        try {
            $radiologist_id = $_SESSION['user_id'];
            
            // Get pending orders count
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as pending_count 
                FROM radiology_test_orders 
                WHERE status IN ('pending', 'scheduled') 
                AND (assigned_to = ? OR assigned_to IS NULL)
            ");
            $stmt->execute([$radiologist_id]);
            $pending_count = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];
            
            // Get today's scheduled tests
            $stmt = $this->db->prepare("
                SELECT 
                    rto.id,
                    rto.scheduled_datetime,
                    rto.priority,
                    rto.status,
                    p.first_name,
                    p.last_name,
                    p.registration_number,
                    rt.test_name,
                    rt.estimated_duration
                FROM radiology_test_orders rto
                JOIN patients p ON rto.patient_id = p.id
                JOIN radiology_tests rt ON rto.test_id = rt.id
                WHERE DATE(rto.scheduled_datetime) = CURDATE()
                AND rto.assigned_to = ?
                ORDER BY rto.scheduled_datetime ASC
            ");
            $stmt->execute([$radiologist_id]);
            $todays_schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get in-progress orders
            $stmt = $this->db->prepare("
                SELECT 
                    rto.id,
                    rto.created_at,
                    p.first_name,
                    p.last_name,
                    p.registration_number,
                    rt.test_name
                FROM radiology_test_orders rto
                JOIN patients p ON rto.patient_id = p.id
                JOIN radiology_tests rt ON rto.test_id = rt.id
                WHERE rto.status = 'in_progress'
                AND rto.assigned_to = ?
                ORDER BY rto.updated_at DESC
            ");
            $stmt->execute([$radiologist_id]);
            $in_progress = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get statistics
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_completed,
                    SUM(CASE WHEN DATE(rr.completed_at) = CURDATE() THEN 1 ELSE 0 END) as today_completed,
                    SUM(CASE WHEN DATE(rr.completed_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as week_completed
                FROM radiology_results rr
                WHERE rr.radiologist_id = ?
            ");
            $stmt->execute([$radiologist_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->render('radiologist/dashboard', [
                'pending_count' => $pending_count,
                'todays_schedule' => $todays_schedule,
                'in_progress' => $in_progress,
                'stats' => $stats
            ]);
            
        } catch (Exception $e) {
            error_log("Radiologist dashboard error: " . $e->getMessage());
            $_SESSION['error'] = "Error loading dashboard";
            header('Location: /');
            exit;
        }
    }

    /**
     * Orders - List all radiology orders with filtering
     */
    public function orders() {
        try {
            $status = $_GET['status'] ?? 'all';
            $priority = $_GET['priority'] ?? 'all';
            $search = $_GET['search'] ?? '';
            
            $query = "
                SELECT 
                    rto.id,
                    rto.created_at,
                    rto.scheduled_datetime,
                    rto.priority,
                    rto.status,
                    rto.clinical_notes,
                    p.first_name,
                    p.last_name,
                    p.registration_number,
                    YEAR(CURDATE()) - YEAR(p.date_of_birth) - (DATE_FORMAT(p.date_of_birth, '%m%d') > DATE_FORMAT(CURDATE(), '%m%d')) as age,
                    p.gender,
                    rt.test_name,
                    rt.test_code,
                    rt.estimated_duration,
                    u.first_name as doctor_first_name,
                    u.last_name as doctor_last_name
                FROM radiology_test_orders rto
                JOIN patients p ON rto.patient_id = p.id
                JOIN radiology_tests rt ON rto.test_id = rt.id
                LEFT JOIN users u ON rto.ordered_by = u.id
                WHERE 1=1
            ";
            
            $params = [];
            
            if ($status !== 'all') {
                $query .= " AND rto.status = ?";
                $params[] = $status;
            }
            
            if ($priority !== 'all') {
                $query .= " AND rto.priority = ?";
                $params[] = $priority;
            }
            
            if (!empty($search)) {
                $query .= " AND (p.registration_number LIKE ? OR p.first_name LIKE ? OR p.last_name LIKE ? OR rt.test_name LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $query .= " ORDER BY 
                CASE rto.priority 
                    WHEN 'stat' THEN 1 
                    WHEN 'urgent' THEN 2 
                    ELSE 3 
                END,
                rto.created_at DESC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->render('radiologist/orders', [
                'orders' => $orders,
                'status' => $status,
                'priority' => $priority,
                'search' => $search
            ]);
            
        } catch (Exception $e) {
            error_log("Radiologist orders error: " . $e->getMessage());
            $_SESSION['error'] = "Error loading orders";
            header('Location: ' . BASE_PATH . '/radiologist/dashboard');
            exit;
        }
    }

    /**
     * Perform Test - Start test execution and mark as in-progress
     */
    public function performTest($order_id) {
        try {
            // Get order details
            $stmt = $this->db->prepare("
                SELECT 
                    rto.*,
                    p.first_name,
                    p.last_name,
                    p.registration_number,
                    p.date_of_birth,
                    p.gender,
                    rt.test_name,
                    rt.preparation_instructions,
                    rt.requires_contrast
                FROM radiology_test_orders rto
                JOIN patients p ON rto.patient_id = p.id
                JOIN radiology_tests rt ON rto.test_id = rt.id
                WHERE rto.id = ?
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                $_SESSION['error'] = "Order not found";
                header('Location: ' . BASE_PATH . '/radiologist/orders');
                exit;
            }
            
            // If POST, update status to in-progress
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRF();
                
                $stmt = $this->db->prepare("
                    UPDATE radiology_test_orders 
                    SET status = 'in_progress',
                        assigned_to = ?
                    WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $order_id]);
                
                $_SESSION['success'] = "Test started successfully";
                header("Location: /radiologist/record_result/$order_id");
                exit;
            }
            
            $this->render('radiologist/perform_test', [
                'order' => $order
            ]);
            
        } catch (Exception $e) {
            error_log("Perform test error: " . $e->getMessage());
            $_SESSION['error'] = "Error performing test";
            header('Location: ' . BASE_PATH . '/radiologist/orders');
            exit;
        }
    }

    /**
     * Record Result - Record test findings, impressions, and upload images
     */
    public function recordResult($order_id) {
        try {
            // Get order details
            $stmt = $this->db->prepare("
                SELECT 
                    rto.*,
                    p.first_name,
                    p.last_name,
                    p.registration_number,
                    YEAR(CURDATE()) - YEAR(p.date_of_birth) - (DATE_FORMAT(p.date_of_birth, '%m%d') > DATE_FORMAT(CURDATE(), '%m%d')) as age,
                    p.gender,
                    rt.test_name,
                    rt.test_code
                FROM radiology_test_orders rto
                JOIN patients p ON rto.patient_id = p.id
                JOIN radiology_tests rt ON rto.test_id = rt.id
                WHERE rto.id = ?
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                $_SESSION['error'] = "Order not found";
                header('Location: ' . BASE_PATH . '/radiologist/orders');
                exit;
            }
            
            // Check if result already exists
            $stmt = $this->db->prepare("SELECT * FROM radiology_results WHERE order_id = ?");
            $stmt->execute([$order_id]);
            $existing_result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If POST, save the result
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRF();
                
                $findings = trim($_POST['findings'] ?? '');
                $impression = trim($_POST['impression'] ?? '');
                $recommendations = trim($_POST['recommendations'] ?? '');
                $is_normal = isset($_POST['is_normal']) ? 1 : 0;
                $is_critical = isset($_POST['is_critical']) ? 1 : 0;
                $radiologist_notes = trim($_POST['radiologist_notes'] ?? '');
                
                // Validate required fields
                if (empty($findings) || empty($impression)) {
                    $_SESSION['error'] = "Findings and Impression are required";
                    header("Location: /radiologist/record_result/$order_id");
                    exit;
                }
                
                // Handle image upload
                $images_path = null;
                if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../storage/radiology_images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['images']['name'], PATHINFO_EXTENSION);
                    $new_filename = 'rad_' . $order_id . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['images']['tmp_name'], $upload_path)) {
                        $images_path = 'storage/radiology_images/' . $new_filename;
                    }
                }
                
                $this->db->beginTransaction();
                
                try {
                    if ($existing_result) {
                        // Update existing result
                        $stmt = $this->db->prepare("
                            UPDATE radiology_results 
                            SET findings = ?,
                                impression = ?,
                                recommendations = ?,
                                is_normal = ?,
                                is_critical = ?,
                                radiologist_notes = ?,
                                images_path = COALESCE(?, images_path),
                                completed_at = NOW()
                            WHERE order_id = ?
                        ");
                        $stmt->execute([
                            $findings, $impression, $recommendations,
                            $is_normal, $is_critical, $radiologist_notes,
                            $images_path, $order_id
                        ]);
                    } else {
                        // Insert new result
                        $stmt = $this->db->prepare("
                            INSERT INTO radiology_results (
                                order_id, patient_id, test_id, findings, impression,
                                recommendations, images_path, is_normal, is_critical,
                                radiologist_id, radiologist_notes, completed_at
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([
                            $order_id, $order['patient_id'], $order['test_id'],
                            $findings, $impression, $recommendations, $images_path,
                            $is_normal, $is_critical, $_SESSION['user_id'], $radiologist_notes
                        ]);
                    }
                    
                    // Update order status to completed
                    $stmt = $this->db->prepare("
                        UPDATE radiology_test_orders 
                        SET status = 'completed'
                        WHERE id = ?
                    ");
                    $stmt->execute([$order_id]);
                    
                    $this->db->commit();
                    
                    $_SESSION['success'] = "Result recorded successfully";
                    header("Location: /radiologist/orders");
                    exit;
                    
                } catch (Exception $e) {
                    $this->db->rollBack();
                    throw $e;
                }
            }
            
            $this->render('radiologist/record_result', [
                'order' => $order,
                'existing_result' => $existing_result
            ]);
            
        } catch (Exception $e) {
            error_log("Record result error: " . $e->getMessage());
            $_SESSION['error'] = "Error recording result";
            header('Location: ' . BASE_PATH . '/radiologist/orders');
            exit;
        }
    }

    /**
     * View Result - Display completed test result
     */
    public function viewResult($result_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    rr.*,
                    rto.clinical_notes,
                    rto.priority,
                    p.first_name,
                    p.last_name,
                    p.registration_number,
                    YEAR(CURDATE()) - YEAR(p.date_of_birth) - (DATE_FORMAT(p.date_of_birth, '%m%d') > DATE_FORMAT(CURDATE(), '%m%d')) as age,
                    p.gender,
                    rt.test_name,
                    rt.test_code,
                    u.first_name as radiologist_first_name,
                    u.last_name as radiologist_last_name
                FROM radiology_results rr
                JOIN radiology_test_orders rto ON rr.order_id = rto.id
                JOIN patients p ON rr.patient_id = p.id
                JOIN radiology_tests rt ON rr.test_id = rt.id
                JOIN users u ON rr.radiologist_id = u.id
                WHERE rr.id = ?
            ");
            $stmt->execute([$result_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                $_SESSION['error'] = "Result not found";
                header('Location: ' . BASE_PATH . '/radiologist/orders');
                exit;
            }
            
            $this->render('radiologist/view_result', [
                'result' => $result
            ]);
            
        } catch (Exception $e) {
            error_log("View result error: " . $e->getMessage());
            $_SESSION['error'] = "Error viewing result";
            header('Location: ' . BASE_PATH . '/radiologist/orders');
            exit;
        }
    }

    /**
     * Search for radiology tests (AJAX endpoint for doctor consultation form)
     * Returns JSON array of matching tests
     */
    public function search_tests() {
        header('Content-Type: application/json');
        
        try {
            $query = $_GET['q'] ?? '';
            
            if (strlen($query) < 2) {
                echo json_encode([]);
                return;
            }
            
            $searchTerm = "%$query%";
            
            $stmt = $this->pdo->prepare("
                SELECT id, test_code, test_name, description, price, is_active
                FROM radiology_tests
                WHERE (test_code LIKE ? OR test_name LIKE ? OR description LIKE ?)
                  AND is_active = 1
                LIMIT 20
            ");
            
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log('[search_tests] Query: ' . $query . ', Results: ' . count($tests));
            
            echo json_encode($tests);
            
        } catch (Exception $e) {
            error_log('[search_tests] Error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Search failed']);
        }
    }
}
