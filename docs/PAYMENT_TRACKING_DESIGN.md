# Payment Tracking Design - Healthcare Management System

## Core Principle
**Payment is tracked ONLY in the `payments` table. Activity/workflow status is tracked in their respective tables.**

---

## Payment Table Design

### Structure
```
payments
├── visit_id (FK) - Links to patient_visits
├── patient_id (FK) - Links to patients
├── payment_type (ENUM)
│   ├── 'registration' - Patient registration fee
│   ├── 'consultation' - Doctor consultation fee
│   ├── 'lab_test' - Laboratory testing
│   ├── 'medicine' - Pharmacy/prescription
│   ├── 'minor_service' - Minor procedures
│   └── 'service' - Major services
├── item_id - References the specific item (test_id, medicine_id, service_id)
├── item_type - Type of item (lab_order, prescription, service)
├── amount - Amount to be paid
├── payment_method (ENUM) - cash, card, mobile_money, insurance
├── payment_status (ENUM) ⚠️ **ONLY TWO STATES**
│   ├── 'pending' - Awaiting accountant to receive payment
│   └── 'paid' - Accountant has received payment
├── reference_number - Invoice/receipt number
├── collected_by (FK) - User who recorded the payment
├── payment_date - When payment was recorded
└── notes - Additional info
```

### Payment Status Flow
```
[Doctor Creates Order]
        ↓
    'pending' (default)
        ↓
[Accountant Records Payment]
        ↓
    'paid' (updated by accountant)
```

---

## Activity Tables (Work Progress Tracking)

### Consultations Table
```
consultations
├── status (ENUM)
│   ├── 'pending' - Registered, awaiting doctor
│   ├── 'in_progress' - Doctor is attending (removed from waiting list)
│   └── 'completed' - Doctor finished, no more tests/medicines/services needed
└── ... doctor notes, diagnosis, etc.
```

### Lab Test Orders Table
```
lab_test_orders
├── status (ENUM)
│   ├── 'pending' - Ordered by doctor, awaiting lab tech
│   ├── 'completed' - Lab tech finished testing
│   └── 'cancelled' - Order cancelled
└── ... test details, assigned lab tech, etc.
```

### Prescriptions Table
```
prescriptions
├── status (ENUM)
│   ├── 'pending' - Prescribed by doctor, awaiting pharmacy
│   ├── 'dispensed' - Pharmacist gave medicine to patient
│   └── 'cancelled' - Prescription cancelled
└── ... dosage, frequency, quantity, etc.
```

### Service Orders Table
```
service_orders
├── status (ENUM)
│   ├── 'pending' - Ordered by doctor, awaiting staff
│   ├── 'completed' - Service performed
│   └── 'cancelled' - Service cancelled
└── ... service details, assigned staff, notes
```

---

## Complete Workflow Example

### Scenario: Doctor orders Lab Tests and Medicines

#### Step 1: Doctor Attends Patient
```php
// DoctorController::attend_patient()
UPDATE consultations SET status = 'in_progress' WHERE id = ?
// Patient disappears from "Waiting" list on dashboard
```

#### Step 2: Doctor Orders Lab Tests & Medicines
```php
// DoctorController::start_consultation()

// CREATE LAB ORDERS
INSERT INTO lab_test_orders (status='pending', ...)
// + CREATE PENDING PAYMENT
INSERT INTO payments (payment_type='lab_test', payment_status='pending', amount=...)

// CREATE PRESCRIPTIONS
INSERT INTO prescriptions (status='pending', ...)
// + CREATE PENDING PAYMENT
INSERT INTO payments (payment_type='medicine', payment_status='pending', amount=...)

// CONSULTATION REMAINS IN_PROGRESS
// (NOT marked as 'completed' because tests/medicines are pending)
UPDATE consultations SET status='in_progress' WHERE id = ?
```

**Database State:**
```
lab_test_orders.status = 'pending'    ← Lab needs to do work
prescriptions.status = 'pending'      ← Pharmacy needs to do work
payments.payment_status = 'pending'   ← Accountant needs to collect payment
consultations.status = 'in_progress'  ← Doctor finished attending
```

#### Step 3: Accountant Receives Payment
```php
// AccountantController::record_payment()
// Accountant enters cash/card/mobile payment details

UPDATE payments 
SET payment_status = 'paid', collected_by = accountant_id, payment_date = NOW()
WHERE id = ?
```

**Database State:**
```
payments.payment_status = 'paid'  ← Payment collected ✓
```

#### Step 4: Lab Technician Completes Testing
```php
// LabController::submit_results()
UPDATE lab_test_orders SET status = 'completed' WHERE id = ?
```

**Database State:**
```
lab_test_orders.status = 'completed'  ← Work done ✓
```

#### Step 5: Pharmacist Dispenses Medicine
```php
// PharmacistController::dispense_medicine()
UPDATE prescriptions SET status = 'dispensed' WHERE id = ?
```

**Database State:**
```
prescriptions.status = 'dispensed'  ← Work done ✓
```

#### Step 6: System Marks Consultation as Completed
```php
// Can happen automatically or manually when all work is done
// Check: All related orders (lab/prescriptions/services) are completed
//        All related payments are 'paid'

UPDATE consultations SET status = 'completed' WHERE id = ?
```

---

## Key Rules

### ✅ DO:
1. **Doctor creates orders** with `status='pending'` in their respective tables
2. **Doctor creates payments** with `payment_status='pending'` when creating orders
3. **Accountant updates payments** to `payment_status='paid'` when receiving money
4. **Lab/Pharmacy updates orders** to `status='completed'` when work is done
5. **Query orders by their status** (lab_test_orders.status, prescriptions.status, etc.)
6. **Query payments by payment_status** (payments.payment_status = 'paid' for received payments)

### ❌ DON'T:
1. ❌ Don't create payments without specifying `payment_status`
2. ❌ Don't update `payment_status` to 'paid' in doctor/lab/pharmacy code
3. ❌ Don't use `consultations.status` to track payment (use `payments.payment_status`)
4. ❌ Don't use `payments.payment_status` to track work progress (use the activity table status)
5. ❌ Don't mark consultation as 'completed' if tests/medicines are still pending

---

## Accountability

- **Doctor's Responsibility:** Order tests/medicines, mark consultation progress
- **Accountant's Responsibility:** Receive payment, update `payment_status` to 'paid'
- **Lab/Pharmacy Responsibility:** Mark work as 'completed' when done
- **System Responsibility:** Create 'pending' payment records when orders are created

---

## Database Queries

### Find Pending Payments (Accountant View)
```sql
SELECT * FROM payments 
WHERE payment_status = 'pending'
ORDER BY payment_date DESC
```

### Find Patients Awaiting Lab Work
```sql
SELECT DISTINCT p.*, lto.status
FROM patients p
JOIN lab_test_orders lto ON p.id = lto.patient_id
WHERE lto.status = 'pending'
AND EXISTS (SELECT 1 FROM payments WHERE payment_status = 'paid' AND payment_type = 'lab_test')
```

### Find Patients with Unpaid Tests
```sql
SELECT DISTINCT p.*, COUNT(lto.id) as pending_tests
FROM patients p
JOIN lab_test_orders lto ON p.id = lto.patient_id
WHERE lto.status = 'pending'
AND NOT EXISTS (SELECT 1 FROM payments WHERE payment_status = 'paid' AND payment_type = 'lab_test' AND visit_id = lto.visit_id)
GROUP BY p.id
```

### Verify Payment Collection Before Proceeding
```sql
-- Check if payment is collected before allowing lab work
SELECT COUNT(*) as payment_count
FROM payments 
WHERE payment_type = 'lab_test' 
AND payment_status = 'paid'
AND visit_id = ?
```

---

## File Changes Summary

### DoctorController.php
- ✅ When doctor orders lab tests → Creates `payments` record with `payment_type='lab_test'` + `payment_status='pending'`
- ✅ When doctor orders medicines → Creates `payments` record with `payment_type='medicine'` + `payment_status='pending'`
- ✅ When doctor allocates services → Creates `payments` record with `payment_type='service'` + `payment_status='pending'`
- ✅ Consultation only marked 'completed' when NO pending tests/medicines/services

### AccountantController.php
- ✅ Correctly updates `payment_status = 'paid'` when receiving payment
- ✅ No other code should modify `payment_status`

---

## Testing Checklist

- [ ] Doctor orders lab tests → Pending payment created
- [ ] Doctor orders medicines → Pending payment created
- [ ] Accountant receives payment → payment_status updated to 'paid'
- [ ] Lab tech doesn't see tests until payment is 'paid'
- [ ] Patient doesn't appear in waiting list after doctor attends (consultation = 'in_progress')
- [ ] Consultation stays 'in_progress' until tests/medicines are ordered
- [ ] Consultation marked 'completed' only when selected "Discharge" (no items ordered)
