# Clinical Examination Form Submission Workflow

## Overview
This document explains what happens when a doctor submits the Clinical Examination form in the attend patient page.

## Form Location
**URL:** `/KJ/doctor/attend_patient/{patient_id}`  
**View File:** `views/doctor/attend_patient.php`  
**Form Action:** `/KJ/doctor/start_consultation`  
**Method:** POST  
**Handler:** `DoctorController::start_consultation()`

---

## Form Fields

### Clinical Examination Section
| Field Name | Input Name | Required | Description |
|------------|------------|----------|-------------|
| M/C - Main Complaint | `main_complaint` | Yes | Patient's main complaint and symptoms |
| O/E - On Examination | `on_examination` | Yes | Physical examination findings |
| Preliminary Diagnosis | `preliminary_diagnosis` | No | Initial working diagnosis |
| Final Diagnosis | `final_diagnosis` | No | Final confirmed diagnosis |

### Next Steps Decision (Radio Buttons)
| Option | Value | Triggers |
|--------|-------|----------|
| Send to Lab for Tests | `lab_tests` | Shows Lab Tests section |
| Prescribe Medicine | `medicine` | Shows Medicine section |
| Both Lab & Medicine | `both` | Shows both sections |
| Discharge Patient | `discharge` | No additional sections |

### Dynamic Sections (Based on Decision)

#### Lab Tests Section
- **Selected Tests:** `selected_tests` (JSON array of test IDs)
- **Lab Investigation Notes:** `lab_investigation`

#### Medicine Section  
- **Selected Medicines:** `selected_medicines` (JSON array of medicine objects)
- **Prescription Notes:** `prescription`

### Treatment Plan
- **Treatment Plan & Instructions:** `treatment_plan`

---

## Submission Process Flow

### Step 1: Form Validation (Frontend)
Function: `validateConsultationForm()` in JavaScript

Checks:
- Main Complaint and On Examination are filled
- If next step requires tests/medicines, at least one item is selected

### Step 2: Data Submission
**POST Request to:** `/KJ/doctor/start_consultation`

**Data Sent:**
```javascript
{
    csrf_token: "...",
    patient_id: 123,
    main_complaint: "...",
    on_examination: "...",
    preliminary_diagnosis: "...",
    final_diagnosis: "...",
    lab_investigation: "...",
    prescription: "...",
    treatment_plan: "...",
    selected_tests: "[1,3,5]",  // JSON array
    selected_medicines: "[{id:2,quantity:10,dosage:'500mg',instructions:'...'}]"  // JSON array
}
```

### Step 3: Server-Side Processing (`DoctorController::start_consultation()`)

#### 3.1 Initial Validation
```php
// Verify POST request
// Validate CSRF token
// Get patient_id and doctor_id
// Find latest visit_id for patient
```

#### 3.2 Consultation Access Check
```php
$can = $this->canAttend($visit_id);
// Checks if doctor has permission to attend this visit
// Verifies workflow status allows consultation
```

#### 3.3 Start/Resume Consultation
```php
$start = $this->startConsultation($visit_id, $doctor_id);
// Creates or resumes consultation record
// Returns consultation_id
```

#### 3.4 Database Transaction Begins
```php
$this->pdo->beginTransaction();
```

#### 3.5 Update Consultation Record
```sql
UPDATE consultations SET 
    main_complaint = ?,
    on_examination = ?,
    preliminary_diagnosis = ?,
    final_diagnosis = ?,
    lab_investigation = ?,
    prescription = ?,
    treatment_plan = ?,
    status = 'completed',
    completed_at = NOW(),
    updated_at = NOW()
WHERE id = ?
```

**What's Saved:**
- All clinical examination data
- Diagnosis information
- Treatment notes
- Marks consultation as 'completed'
- Records completion timestamp

#### 3.6 Process Lab Test Orders (If Selected)

**If `selected_tests` is not empty:**

1. **Find Lab Technician:**
   ```sql
   SELECT id FROM users 
   WHERE role = 'lab_technician' AND is_active = 1 
   LIMIT 1
   ```

2. **Create Lab Test Orders:**
   ```sql
   INSERT INTO lab_test_orders 
   (visit_id, patient_id, consultation_id, test_id, ordered_by, assigned_to, status, created_at) 
   VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
   ```
   - Creates one record per selected test
   - Links to visit, patient, and consultation
   - Sets `ordered_by` = doctor_id
   - Sets `assigned_to` = technician_id
   - Status starts as 'pending'

3. **Update Workflow:**
   ```php
   $this->updateWorkflowStatus($patient_id, 'pending_payment', 
       ['lab_tests_ordered' => true]);
   ```
   - Changes patient workflow status to 'pending_payment'
   - Patient must go to receptionist to pay for tests

#### 3.7 Process Medicine Prescriptions (If Selected)

**If `selected_medicines` is not empty:**

1. **Create Prescriptions:**
   ```sql
   INSERT INTO prescriptions 
   (visit_id, patient_id, consultation_id, doctor_id, medicine_id, 
    quantity_prescribed, dosage, frequency, duration, instructions, status, created_at) 
   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
   ```
   - Creates one record per selected medicine
   - Links to visit, patient, consultation, and doctor
   - Includes dosage instructions
   - Status starts as 'pending'

2. **Update Workflow:**
   ```php
   $this->updateWorkflowStatus($patient_id, 'pending_payment', 
       ['medicine_prescribed' => true]);
   ```
   - Changes patient workflow status to 'pending_payment'
   - Patient must go to receptionist to pay for medicines

#### 3.8 Handle Discharge (No Tests or Medicines)

**If both `selected_tests` and `selected_medicines` are empty:**

```php
$this->updateWorkflowStatus($patient_id, 'completed');
```
- Patient visit is marked as completed
- No payment required
- Patient is discharged

#### 3.9 Commit Transaction
```php
$this->pdo->commit();
$_SESSION['success'] = 'Consultation completed successfully';
```

#### 3.10 Error Handling
```php
catch (Exception $e) {
    $this->pdo->rollBack();
    $_SESSION['error'] = 'Failed to complete consultation: ' . $e->getMessage();
}
```

#### 3.11 Redirect
```php
$this->redirect('doctor/view_patient/' . $patient_id);
```
- Returns to patient view page
- Shows success or error message

---

## Database Tables Updated

### 1. `consultations` Table
**Updated Fields:**
- `main_complaint`
- `on_examination`
- `preliminary_diagnosis`
- `final_diagnosis`
- `lab_investigation`
- `prescription`
- `treatment_plan`
- `status` → 'completed'
- `completed_at` → NOW()
- `updated_at` → NOW()

### 2. `lab_test_orders` Table (if tests selected)
**New Records Created:**
- One record per selected test
- Links: visit_id, patient_id, consultation_id, test_id
- `ordered_by` = doctor_id
- `assigned_to` = technician_id
- `status` = 'pending'

### 3. `prescriptions` Table (if medicines selected)
**New Records Created:**
- One record per selected medicine
- Links: visit_id, patient_id, consultation_id, doctor_id, medicine_id
- Includes: quantity_prescribed, dosage, frequency, duration, instructions
- `status` = 'pending'

### 4. `patient_visits` Table (workflow status update)
**Updated Fields:**
- `status` → 'pending_payment' (if tests or medicines ordered)
- `status` → 'completed' (if discharged without orders)
- `updated_at` → NOW()

---

## Workflow State Transitions

### Scenario 1: Lab Tests Only
```
consultation → pending_payment (lab tests) → [receptionist payment] → lab_testing → results_review → completed
```

### Scenario 2: Medicines Only
```
consultation → pending_payment (medicines) → [receptionist payment] → medicine_dispensing → completed
```

### Scenario 3: Both Lab & Medicine
```
consultation → pending_payment (both) → [receptionist payment for tests] → lab_testing → 
[receptionist payment for medicines] → medicine_dispensing → completed
```

### Scenario 4: Discharge (No Orders)
```
consultation → completed
```

---

## Receptionist Integration

After doctor submits the form with lab tests or medicines:

### Receptionist's Pending Payments Page
**URL:** `/KJ/receptionist/payments`

**Displays:**

#### Pending Lab Test Payments (Red Table)
```sql
SELECT 
    pt.id as patient_id,
    CONCAT(pt.first_name, ' ', pt.last_name) as patient_name,
    pv.id as visit_id,
    GROUP_CONCAT(lt.test_name SEPARATOR ', ') as tests,
    SUM(lt.price) as total_amount
FROM lab_test_orders lto
JOIN patients pt ON lto.patient_id = pt.id
JOIN patient_visits pv ON lto.visit_id = pv.id
JOIN lab_tests lt ON lto.test_id = lt.id
LEFT JOIN payments pay ON pay.visit_id = pv.id AND pay.payment_type = 'lab_test'
WHERE pay.id IS NULL
GROUP BY lto.patient_id, pv.id
```

#### Pending Medicine Payments (Orange Table)
```sql
SELECT 
    pt.id as patient_id,
    CONCAT(pt.first_name, ' ', pt.last_name) as patient_name,
    pv.id as visit_id,
    GROUP_CONCAT(m.medicine_name SEPARATOR ', ') as medicines,
    SUM(m.unit_price * pr.quantity_prescribed) as total_amount
FROM prescriptions pr
JOIN patients pt ON pr.patient_id = pt.id
JOIN patient_visits pv ON pr.visit_id = pv.id
JOIN medicines m ON pr.medicine_id = m.id
LEFT JOIN payments pay ON pay.visit_id = pv.id AND pay.payment_type = 'medicine'
WHERE pay.id IS NULL
GROUP BY pr.patient_id, pv.id
```

### Payment Recording Process
1. Receptionist clicks "Record Payment" button
2. Modal shows patient name, payment type, and amount
3. Receptionist selects payment method (cash/card/mobile_money/insurance)
4. System records payment in `payments` table
5. Workflow status updates to next stage (lab_testing or medicine_dispensing)

---

## Success/Error Messages

### Success Messages
- **Consultation completed successfully** - All data saved, patient ready for next step

### Error Messages
- **No visit found for this patient** - Patient doesn't have an active visit
- **Cannot start consultation: [reason]** - Workflow status doesn't allow consultation
- **Failed to start consultation: [message]** - Error creating consultation record
- **Failed to complete consultation: [exception]** - Database error during transaction

---

## Data Flow Diagram

```
Doctor Submits Form
        ↓
Validate CSRF & Patient
        ↓
Check Visit Access
        ↓
Start/Resume Consultation
        ↓
    ┌───────────────────┐
    │  BEGIN TRANSACTION│
    └───────────────────┘
        ↓
Update Consultation Record
    (main_complaint, diagnosis, etc.)
        ↓
    ┌─────────────────────────┐
    │  If Lab Tests Selected  │
    └─────────────────────────┘
        ↓
Create lab_test_orders records
Update workflow: pending_payment
        ↓
    ┌─────────────────────────┐
    │ If Medicines Selected   │
    └─────────────────────────┘
        ↓
Create prescriptions records
Update workflow: pending_payment
        ↓
    ┌─────────────────────────┐
    │  If Nothing Selected    │
    └─────────────────────────┘
        ↓
Update workflow: completed
        ↓
    ┌───────────────────┐
    │  COMMIT TRANSACTION│
    └───────────────────┘
        ↓
Redirect to view_patient
        ↓
Show Success Message
```

---

## Key Features

### Transaction Safety
- All database operations wrapped in transaction
- Rollback on any error
- Ensures data consistency

### Workflow Management
- Automatic status updates based on next steps
- Integrates with receptionist payment system
- Tracks patient journey through system

### Data Validation
- CSRF protection
- Required field validation
- Access permission checks
- Visit status verification

### User Experience
- Clear success/error messages
- Redirect to patient view page
- Session messages persist across redirect

---

## Testing Checklist

1. ✅ Submit with only clinical data (discharge) → Status: completed
2. ✅ Submit with lab tests → Creates lab_test_orders, Status: pending_payment
3. ✅ Submit with medicines → Creates prescriptions, Status: pending_payment
4. ✅ Submit with both → Creates both records, Status: pending_payment
5. ✅ Verify records appear in receptionist pending payments
6. ✅ Record payment → Workflow advances to next stage
7. ✅ Check error handling with invalid data
8. ✅ Verify CSRF token validation
9. ✅ Test with multiple tests/medicines
10. ✅ Confirm consultation record updates correctly

---

## Date Documented
October 11, 2025
