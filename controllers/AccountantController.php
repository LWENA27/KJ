<?php
require_once __DIR__ . '/../includes/BaseController.php';

/**
 * AccountantController
 * Handles all payment/accounting related functions
 * Separated from ReceptionistController for role-based access control
 */
class AccountantController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        // Allow access for accountant role, or admin, or receptionist with payment permissions
        $this->requireRole(['accountant', 'admin']);
    }

    public function dashboard()
    {
        // Get today's payment summary
        $stmt = $this->pdo->prepare("
            SELECT 
                COALESCE(SUM(amount), 0) as total_today,
                COUNT(*) as payment_count
            FROM payments 
            WHERE DATE(payment_date) = CURDATE() AND payment_status = 'paid'
        ");
        $stmt->execute();
        $payments_today = $stmt->fetch();

        // Get yesterday's payment for comparison
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total_yesterday
            FROM payments 
            WHERE DATE(payment_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND payment_status = 'paid'
        ");
        $stmt->execute();
        $payments_yesterday = $stmt->fetchColumn();

        // Calculate percentage change
        $percentage_change = 0;
        if ($payments_yesterday > 0) {
            $percentage_change = (($payments_today['total_today'] - $payments_yesterday) / $payments_yesterday) * 100;
        } elseif ($payments_today['total_today'] > 0) {
            $percentage_change = 100;
        }

        // Get pending payments count
        $pending_count = $this->getPendingPaymentsCount();

        // Get weekly revenue
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as weekly_total
            FROM payments 
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND payment_status = 'paid'
        ");
        $stmt->execute();
        $weekly_revenue = $stmt->fetchColumn();

        // Get monthly revenue
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as monthly_total
            FROM payments 
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND payment_status = 'paid'
        ");
        $stmt->execute();
        $monthly_revenue = $stmt->fetchColumn();

        // Get revenue by payment type
        $stmt = $this->pdo->prepare("
            SELECT payment_type, SUM(amount) as total
            FROM payments 
            WHERE DATE(payment_date) = CURDATE() AND payment_status = 'paid'
            GROUP BY payment_type
        ");
        $stmt->execute();
        $revenue_by_type = $stmt->fetchAll();

        // Get recent payments
        $stmt = $this->pdo->prepare("
            SELECT p.*, 
                   CONCAT(pat.first_name, ' ', pat.last_name) as patient_name,
                   pat.registration_number
            FROM payments p
            JOIN patients pat ON p.patient_id = pat.id
            WHERE p.payment_status = 'paid'
            ORDER BY p.payment_date DESC
            LIMIT 10
        ");
        $stmt->execute();
        $recent_payments = $stmt->fetchAll();

        $this->render('accountant/dashboard', [
            'payments_today' => $payments_today,
            'percentage_change' => $percentage_change,
            'pending_count' => $pending_count,
            'weekly_revenue' => $weekly_revenue,
            'monthly_revenue' => $monthly_revenue,
            'revenue_by_type' => $revenue_by_type,
            'recent_payments' => $recent_payments,
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    public function payments()
    {
        // Get all payment records (paid and pending)
        $stmt = $this->pdo->prepare("
            SELECT p.*, pat.first_name, pat.last_name, pat.registration_number, u.first_name as collected_by_name
            FROM payments p
            JOIN patients pat ON p.patient_id = pat.id
            LEFT JOIN users u ON p.collected_by = u.id
            ORDER BY p.payment_status ASC, p.payment_date DESC
        ");
        $stmt->execute();
        $payments = $stmt->fetchAll();

        // Get pending lab test payments
        $stmt = $this->pdo->prepare("
            SELECT 
                lto.visit_id,
                lto.patient_id,
                p.first_name,
                p.last_name,
                p.registration_number,
                pv.visit_date,
                COUNT(DISTINCT lto.id) as test_count,
                SUM(lt.price) as total_amount,
                COALESCE(SUM(CASE WHEN pay.payment_status = 'paid' THEN pay.amount ELSE 0 END), 0) as paid_amount,
                (SUM(lt.price) - COALESCE(SUM(CASE WHEN pay.payment_status = 'paid' THEN pay.amount ELSE 0 END), 0)) as remaining_amount_to_pay,
                MAX(lto.created_at) AS last_order_created
            FROM lab_test_orders lto
            JOIN lab_tests lt ON lto.test_id = lt.id  
            JOIN patients p ON lto.patient_id = p.id
            LEFT JOIN patient_visits pv ON lto.visit_id = pv.id
            LEFT JOIN payments pay ON lto.visit_id = pay.visit_id 
                                  AND pay.payment_type = 'lab_test'
                                  AND pay.payment_status = 'paid'
            WHERE lto.status = 'pending'
            GROUP BY lto.visit_id, lto.patient_id, p.first_name, p.last_name, p.registration_number, pv.visit_date
            HAVING remaining_amount_to_pay > 0
            ORDER BY pv.visit_date DESC, last_order_created DESC
        ");
        $stmt->execute();
        $pending_lab_payments = $stmt->fetchAll();

        // Get pending medicine payments
        $stmt = $this->pdo->prepare("
            SELECT 
                pr.id as prescription_id,
                pr.visit_id,
                pr.patient_id,
                p.first_name,
                p.last_name,
                p.registration_number,
                pv.visit_date,
                m.name as medicine_name,
                pr.quantity_prescribed,
                m.unit_price,
                (pr.quantity_prescribed * m.unit_price) as total_cost,
                COALESCE(SUM(pay.amount), 0) as paid_amount,
                (SUM(pr.quantity_prescribed * m.unit_price) - COALESCE(SUM(pay.amount), 0)) AS remaining_amount_to_pay
            FROM prescriptions pr
            JOIN medicines m ON pr.medicine_id = m.id
            JOIN patients p ON pr.patient_id = p.id
            JOIN patient_visits pv ON pr.visit_id = pv.id
            LEFT JOIN payments pay ON pr.visit_id = pay.visit_id 
                                  AND pay.payment_type = 'medicine'
                                  AND pay.payment_status = 'paid'
            WHERE pr.status IN ('pending', 'partial')
            GROUP BY pr.id, pr.visit_id, pr.patient_id, p.first_name, p.last_name, p.registration_number, pv.visit_date, m.name, pr.quantity_prescribed, m.unit_price
            HAVING remaining_amount_to_pay > 0
            ORDER BY pr.created_at DESC
        ");
        $stmt->execute();
        $pending_medicine_payments = $stmt->fetchAll();

        // Get pending service payments
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
        $stmt->execute();
        $pending_service_payments = $stmt->fetchAll();

        $this->render('accountant/payments', [
            'payments' => $payments,
            'pending_lab_payments' => $pending_lab_payments,
            'pending_medicine_payments' => $pending_medicine_payments,
            'pending_service_payments' => $pending_service_payments,
            'csrf_token' => $this->generateCSRF(),
            'sidebar_data' => $this->getSidebarData()
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

        // Filter by date range
        if (!empty($_GET['date_from'])) {
            $where_clauses[] = "DATE(p.payment_date) >= ?";
            $params[] = $_GET['date_from'];
        }

        if (!empty($_GET['date_to'])) {
            $where_clauses[] = "DATE(p.payment_date) <= ?";
            $params[] = $_GET['date_to'];
        }

        $where_sql = implode(' AND ', $where_clauses);

        // Get payments
        $stmt = $this->pdo->prepare("
            SELECT p.*, 
                   CONCAT(pt.first_name, ' ', pt.last_name) as patient_name,
                   pt.registration_number,
                   pv.visit_date,
                   u.first_name as collected_by_first,
                   u.last_name as collected_by_last
            FROM payments p
            JOIN patients pt ON p.patient_id = pt.id
            LEFT JOIN patient_visits pv ON p.visit_id = pv.id
            LEFT JOIN users u ON p.collected_by = u.id
            WHERE $where_sql
            ORDER BY p.payment_date DESC
        ");
        $stmt->execute($params);
        $payments = $stmt->fetchAll();

        // Calculate totals
        $total_amount = array_sum(array_column($payments, 'amount'));

        $this->render('accountant/payment_history', [
            'payments' => $payments,
            'total_amount' => $total_amount,
            'sidebar_data' => $this->getSidebarData(),
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function record_payment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method';
            $this->redirect('accountant/payments');
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

        if (!$patient_id || !$visit_id || !$payment_type || $amount <= 0) {
            $_SESSION['error'] = 'Invalid payment details';
            $this->redirect('accountant/payments');
            return;
        }

        try {
            $this->pdo->beginTransaction();

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
                $this->updateWorkflowStatus($patient_id, 'medicine_dispensing', ['medicine_payment_received' => true]);
            }

            $this->pdo->commit();
            $_SESSION['success'] = 'Payment recorded successfully';
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error'] = 'Failed to record payment: ' . $e->getMessage();
            error_log('Payment recording error: ' . $e->getMessage());
        }

        $this->redirect('accountant/payments');
    }

    public function reports()
    {
        // Daily revenue report (last 30 days)
        $stmt = $this->pdo->prepare("
            SELECT 
                DATE(payment_date) as date,
                COUNT(*) as payment_count,
                SUM(amount) as total_amount
            FROM payments 
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND payment_status = 'paid'
            GROUP BY DATE(payment_date)
            ORDER BY date DESC
        ");
        $stmt->execute();
        $daily_revenue = $stmt->fetchAll();

        // Revenue by payment type
        $stmt = $this->pdo->prepare("
            SELECT 
                payment_type,
                COUNT(*) as payment_count,
                SUM(amount) as total_amount
            FROM payments 
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND payment_status = 'paid'
            GROUP BY payment_type
            ORDER BY total_amount DESC
        ");
        $stmt->execute();
        $revenue_by_type = $stmt->fetchAll();

        // Revenue by payment method
        $stmt = $this->pdo->prepare("
            SELECT 
                payment_method,
                COUNT(*) as payment_count,
                SUM(amount) as total_amount
            FROM payments 
            WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND payment_status = 'paid'
            GROUP BY payment_method
            ORDER BY total_amount DESC
        ");
        $stmt->execute();
        $revenue_by_method = $stmt->fetchAll();

        // Top collectors
        $stmt = $this->pdo->prepare("
            SELECT 
                u.first_name,
                u.last_name,
                COUNT(p.id) as payment_count,
                SUM(p.amount) as total_collected
            FROM payments p
            JOIN users u ON p.collected_by = u.id
            WHERE p.payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND p.payment_status = 'paid'
            GROUP BY u.id, u.first_name, u.last_name
            ORDER BY total_collected DESC
            LIMIT 10
        ");
        $stmt->execute();
        $top_collectors = $stmt->fetchAll();

        // Outstanding payments
        $outstanding = $this->getPendingPaymentsCount();

        $this->render('accountant/reports', [
            'daily_revenue' => $daily_revenue,
            'revenue_by_type' => $revenue_by_type,
            'revenue_by_method' => $revenue_by_method,
            'top_collectors' => $top_collectors,
            'outstanding_count' => $outstanding,
            'sidebar_data' => $this->getSidebarData()
        ]);
    }

    private function getPendingPaymentsCount()
    {
        // Count pending lab payments
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT lto.visit_id) 
            FROM lab_test_orders lto
            LEFT JOIN payments pay ON lto.visit_id = pay.visit_id 
                AND pay.payment_type = 'lab_test' 
                AND pay.payment_status = 'paid'
            WHERE lto.status = 'pending' AND pay.id IS NULL
        ");
        $stmt->execute();
        $pending_lab = $stmt->fetchColumn();

        // Count pending medicine payments
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT pr.visit_id) 
            FROM prescriptions pr
            LEFT JOIN payments pay ON pr.visit_id = pay.visit_id 
                AND pay.payment_type = 'medicine' 
                AND pay.payment_status = 'paid'
            WHERE pr.status IN ('pending', 'partial') AND pay.id IS NULL
        ");
        $stmt->execute();
        $pending_medicine = $stmt->fetchColumn();

        return $pending_lab + $pending_medicine;
    }

    private function getSidebarData()
    {
        $pending_count = $this->getPendingPaymentsCount();

        // Get today's revenue
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as today_total
            FROM payments 
            WHERE DATE(payment_date) = CURDATE() AND payment_status = 'paid'
        ");
        $stmt->execute();
        $today_revenue = $stmt->fetchColumn();

        return [
            'pending_payments' => $pending_count,
            'today_revenue' => $today_revenue
        ];
    }
}
