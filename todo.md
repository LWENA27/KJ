
- **Backup payments**: create a SQL dump first.
```powershell
mysqldump -u root -p zahanati payments > payments_backup.sql
```

- **(Optional) Create a system user**: reserve a stable collector id (example uses id = 0).
```sql
INSERT INTO users (id, first_name, last_name, email, role, password_hash)
VALUES (0, 'System', 'Collector', 'system@local', 'system', '');
```

- **Map orphan payment collectors to system user**:
```sql
UPDATE payments
SET collected_by = 0
WHERE collected_by IS NOT NULL
  AND collected_by NOT IN (SELECT id FROM users);
```

- **Verify orphan fix**:
```sql
SELECT DISTINCT collected_by FROM payments ORDER BY collected_by LIMIT 50;
SELECT id, visit_id, patient_id, amount, collected_by, payment_date
FROM payments
WHERE collected_by IS NOT NULL AND collected_by NOT IN (SELECT id FROM users)
ORDER BY payment_date DESC LIMIT 50;
```

- **Add short-window duplicate-prevention to `AccountantController::record_payment()`** (edit AccountantController.php):
  - Insert before the payment INSERT:
```php
// Short-window duplicate prevention (10s)
$dupStmt = $this->pdo->prepare("
  SELECT id FROM payments
  WHERE visit_id = ? AND payment_type = ? AND COALESCE(item_id,'') = COALESCE(?, '')
    AND amount = ? AND collected_by = ? AND payment_status = 'paid'
    AND payment_date >= DATE_SUB(NOW(), INTERVAL 10 SECOND)
  LIMIT 1
");
$dupStmt->execute([$visit_id, $payment_type, $item_id, $amount, $_SESSION['user_id']]);
if ($dupStmt->fetch()) {
  $_SESSION['info'] = 'Duplicate payment detected and prevented';
  $this->redirect('accountant/payments');
  return;
}
```

- **Improve pending-payment queries in `AccountantController::payments()`**:
  - Replace lab pending aggregation with a per-visit paid-sum subquery (aggregate payments by visit):
```sql
-- in PHP prepare() string: aggregate paid_amount per visit
LEFT JOIN (
  SELECT visit_id, SUM(amount) AS paid_amount
  FROM payments
  WHERE payment_type = 'lab_test' AND payment_status = 'paid'
  GROUP BY visit_id
) pay ON pay.visit_id = lto.visit_id
```
  - For medicines, match payments to prescription `item_id`:
```sql
LEFT JOIN payments pay ON pr.id = pay.item_id
  AND pay.payment_type = 'medicine' AND pay.payment_status = 'paid'
```

- **Receptionist behavior (auto-paid registration and per-test lab payments)** — edit ReceptionistController.php in the `register_patient()` flow:
  - After creating the consultation (consultation visit), auto-create a zero-amount paid `registration` payment (system collector = 0) with a short duplicate check:
```php
// auto-paid registration
$payment_type = 'registration';
$amount = 0;
$collected_by = 0; // system
$stmtDup = $this->pdo->prepare("SELECT id FROM payments WHERE visit_id = ? AND payment_type = ? AND amount = ? AND payment_status = 'paid' LIMIT 1");
$stmtDup->execute([$visit_id, $payment_type, $amount]);
if (!$stmtDup->fetch()) {
  $stmtPay = $this->pdo->prepare("INSERT INTO payments (visit_id, patient_id, payment_type, amount, payment_method, payment_status, reference_number, collected_by, payment_date, notes) VALUES (?, ?, ?, ?, ?, 'paid', ?, ?, NOW(), ?)");
  $stmtPay->execute([$visit_id, $patient_id, $payment_type, $amount, 'cash', null, $collected_by, 'Auto-paid free registration']);
}
```
  - For `lab_test` visit, create one pending `payments` row per selected test (per-test duplicate check):
```php
// inside loop over selected test ids
$stmtDup = $this->pdo->prepare("SELECT id FROM payments WHERE visit_id = ? AND payment_type = 'lab_test' AND item_id = ? AND payment_status = 'pending' LIMIT 1");
$stmtDup->execute([$visit_id, $test_id]);
if (!$stmtDup->fetch()) {
  $stmtPay = $this->pdo->prepare("INSERT INTO payments (visit_id, patient_id, payment_type, item_id, item_type, amount, payment_method, payment_status, reference_number, collected_by, payment_date, notes) VALUES (?, ?, 'lab_test', ?, 'lab_order', ?, ?, 'pending', ?, NULL, NOW(), ?)");
  $stmtPay->execute([$visit_id, $patient_id, $test_id, $price, 'cash', null, 'Pending lab test payment']);
}
```

- **Run diagnostics to find remaining NULL/duplicate issues**:
```sql
-- payments with empty or NULL payment_type
SELECT * FROM payments WHERE payment_type IS NULL OR payment_type = '' ORDER BY payment_date DESC LIMIT 200;

-- grouped duplicates by visit/payment_type/item/amount/collector
SELECT visit_id, payment_type, COALESCE(item_id,'') AS item_id, amount, collected_by, COUNT(*) as cnt, MIN(payment_date) as first_seen, MAX(payment_date) as last_seen
FROM payments
GROUP BY visit_id, payment_type, COALESCE(item_id,''), amount, collected_by
HAVING cnt > 1
ORDER BY last_seen DESC
LIMIT 200;
```

- **Hardening & follow-ups**:
  - Add a `SYSTEM_USER_ID` constant in your config and replace magic `0` occurrences in code with that constant.
  - After confirming app-level protection, enforce DB constraints (example: make `payment_type` NOT NULL and/or set `payments.collected_by` NOT NULL DEFAULT 0) with ALTER TABLE — do this only after mapping/cleaning existing rows and a DB backup.
  - Consider adding a unique partial index to help detect duplicates (MySQL functional limitations may apply — test before applying in prod).

- **Verify in the app**:
  - Reload `Accountant` → `Pending Payments` (page is payments.php) and confirm:
    - free consultation registrations are not listed as pending (they produce a paid zero-amount row),
    - lab tests show remaining_amount_to_pay and test counts correctly,
    - recording a payment from the modal does not create duplicate paid rows within 10s.

If you want, I can:
- produce the exact patch files (I already applied these changes in the workspace), or
- run the diagnostic SELECTs for you if you want me to execute them (you’ll need to paste results).