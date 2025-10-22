# Patient Registration Form Workflow

## Date: 2025-10-11

---

## Form Location

**File:** `views/receptionist/register_patient.php`  
**Submits to:** `/receptionist/register_patient` (POST)  
**Controller:** `ReceptionistController::register_patient()`

---

## What Happens When User Submits the Registration Form

### Step-by-Step Process

#### 1. **Form Submission** (User Action)
User fills out the patient registration form with:
- Personal info: First name, Last name, DOB, Gender
- Contact: Phone, Email, Address
- Emergency contact: Name and Phone
- Visit type: Consultation or Check-up
- Payment: Consultation fee (if consultation) and Payment method
- Vital signs (optional): Temperature, Blood pressure, Pulse, Weight, Height

#### 2. **Request Received** (Backend)
- Request hits: `index.php` → routes to `ReceptionistController::register_patient()`
- Method check: Only POST requests processed
- CSRF validation: Checks CSRF token from form

#### 3. **Data Validation** (ReceptionistController.php lines 151-186)
```php
// Normalize POST inputs
$visit_type = $_POST['visit_type'] ?? 'consultation';
$consultation_fee = $_POST['consultation_fee'] ?? null;
$payment_method = $_POST['payment_method'] ?? null;
// ... all form fields

// Validate consultation payment (if consultation visit)
if ($visit_type === 'consultation') {
    if (empty($consultation_fee) || empty($payment_method)) {
        throw new Exception('Consultation fee and payment method are required');
    }
}
```

**Validation Rules:**
- For consultation visits: Fee and payment method required
- Other fields: Sanitized but optional

#### 4. **Database Transaction Begins** (Line 187)
```php
$this->pdo->beginTransaction();
```
**Why?** Ensures all-or-nothing: either ALL data saved successfully or NONE saved.

---

### 5. **Patient Record Creation** (Lines 189-210)

#### INSERT INTO `patients` table:
```sql
INSERT INTO patients (
    registration_number, first_name, last_name,
    date_of_birth, gender, phone, email,
    address, emergency_contact_name,
    emergency_contact_phone, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
```

**What happens:**
- Generates unique registration number (e.g., `REG-20251011-0001`)
- Sanitizes all input fields (removes HTML, trims whitespace)
- Inserts patient into database
- Gets `$patient_id` from `lastInsertId()`

**Result:** Patient record created with unique ID

---

### 6. **Visit Record Creation** (Lines 212-215)

#### INSERT INTO `patient_visits` table:
```sql
INSERT INTO patient_visits (
    patient_id, visit_date, visit_type, 
    registered_by, status, created_at, updated_at
) VALUES (?, CURDATE(), ?, ?, 'active', NOW(), NOW())
```

**What happens:**
- Links visit to patient via `patient_id`
- Records visit date (today)
- Sets visit type (consultation/check-up)
- Records who registered (receptionist user_id)
- Sets status to 'active'
- Gets `$visit_id` from `lastInsertId()`

**Result:** Visit record created, patient now has active visit

---

### 7. **Payment Processing** (Lines 217-238, only if consultation)

#### A. Record Payment (INSERT INTO `payments`)
```sql
INSERT INTO payments (
    visit_id, patient_id, payment_type, amount, 
    payment_method, payment_status, reference_number, 
    collected_by, payment_date, notes
) VALUES (?, ?, 'registration', ?, ?, 'paid', NULL, ?, NOW(), ?)
```

**What happens:**
- Records payment of consultation fee
- Links to visit and patient
- Marks as 'paid' immediately
- Records payment method (cash/mpesa/card/insurance)
- Records who collected payment (receptionist)

**Result:** Payment recorded, patient has paid for consultation

#### B. Create Consultation Record (INSERT INTO `consultations`)
```sql
INSERT INTO consultations (
    visit_id, patient_id, doctor_id, 
    consultation_type, status, created_at
) VALUES (?, ?, 1, 'new', 'pending', NOW())
```

**What happens:**
- Creates consultation record for doctor to see
- Uses default doctor_id = 1 (can be reassigned)
- Sets status to 'pending' (waiting for doctor)
- Sets type to 'new' (new patient)

**Result:** Consultation created, visible to doctors

---

### 8. **Vital Signs Recording** (Lines 240-259, if provided)

#### Parse Blood Pressure
```php
if (!empty($blood_pressure) && strpos($blood_pressure, '/') !== false) {
    [$bp_systolic, $bp_diastolic] = explode('/', $blood_pressure);
}
```

**Example:** "120/80" → systolic=120, diastolic=80

#### Calculate BMI
```php
if (!empty($body_weight) && !empty($height)) {
    $height_m = floatval($height) / 100;
    $bmi = floatval($body_weight) / ($height_m * $height_m);
}
```

**Formula:** BMI = weight(kg) / (height(m))²

#### INSERT INTO `vital_signs`
```sql
INSERT INTO vital_signs (
    patient_id, visit_id, temperature, 
    blood_pressure_systolic, blood_pressure_diastolic,
    pulse_rate, weight, height, bmi, 
    recorded_by, recorded_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
```

**What happens:**
- Stores all vital signs
- Links to patient and visit
- Auto-calculates BMI if weight/height provided
- Records who took measurements (receptionist)

**Result:** Vital signs saved for doctor review

---

### 9. **Transaction Commit** (Line 263)
```php
$this->pdo->commit();
```

**What happens:**
- All database changes made permanent
- If ANY step fails, ROLLBACK (nothing saved)

**Result:** All data successfully saved to database

---

### 10. **Success Response** (Lines 265-267)
```php
$_SESSION['success'] = 'Patient registered successfully!';
$this->redirect('receptionist/patients');
```

**What happens:**
- Sets success message in session
- Redirects to patients list page

**User sees:**
- Green success notification: "Patient registered successfully!"
- Patient appears in patients list
- Can now be attended by doctor

---

### 11. **Error Handling** (Lines 268-273)
```php
catch (Exception $e) {
    $this->pdo->rollBack();
    error_log('Registration error: ' . $e->getMessage());
    $_SESSION['error'] = 'Registration failed: ' . $e->getMessage();
    $this->redirect('receptionist/register_patient');
}
```

**What happens if error:**
- Rollback transaction (no data saved)
- Log error to `logs/exceptions.log`
- Set error message in session
- Redirect back to registration form

**User sees:**
- Red error notification with error message
- Form still filled (can correct and resubmit)

---

## Database Tables Modified

| Table | Action | Data Stored |
|-------|--------|-------------|
| `patients` | INSERT | Patient demographics, contact info |
| `patient_visits` | INSERT | Visit record (active status) |
| `payments` | INSERT | Consultation payment (if consultation) |
| `consultations` | INSERT | Consultation record (if consultation) |
| `vital_signs` | INSERT | Temperature, BP, pulse, weight, height, BMI |

---

## Data Flow Diagram

```
User fills form
      ↓
POST /receptionist/register_patient
      ↓
[Validate CSRF token]
      ↓
[Validate required fields]
      ↓
[BEGIN TRANSACTION]
      ↓
[INSERT patients] → $patient_id
      ↓
[INSERT patient_visits] → $visit_id
      ↓
[If consultation:]
    [INSERT payments]
    [INSERT consultations]
      ↓
[If vitals provided:]
    [INSERT vital_signs]
      ↓
[COMMIT TRANSACTION]
      ↓
[Success message]
      ↓
Redirect to /receptionist/patients
```

---

## Validation Summary

### Required Fields (Always)
- First name
- Last name
- Phone or Email (at least one)

### Required Fields (Consultation Only)
- Consultation fee
- Payment method

### Optional Fields
- Date of birth
- Gender
- Address
- Emergency contact name
- Emergency contact phone
- All vital signs

### Auto-Generated
- Registration number (REG-YYYYMMDD-####)
- Patient ID (auto-increment)
- Visit ID (auto-increment)
- Timestamps (created_at, updated_at)

---

## Success Criteria

✅ **Registration Successful When:**
1. All required fields provided
2. CSRF token valid
3. Database inserts succeed
4. Transaction commits without errors

❌ **Registration Fails When:**
1. CSRF token invalid/missing
2. Required fields missing (consultation fee, payment method)
3. Database constraint violation (duplicate registration number)
4. Any database error during transaction

---

## After Registration: Patient Journey

### 1. **Immediate State**
- Patient: registered, has ID and registration number
- Visit: active, linked to patient
- Payment: recorded (if consultation)
- Consultation: pending, waiting for doctor

### 2. **Next Steps**
- **Receptionist:** Can see patient in patients list
- **Doctor:** Can see patient in "Pending Patients" list
- **Workflow:** Patient ready for consultation (if paid)

### 3. **Workflow Status**
```
Registration (✅ COMPLETED)
    ↓
Consultation (⏳ PENDING) ← Current step
    ↓
Lab Tests (if needed)
    ↓
Medicine Dispensing (if prescribed)
    ↓
Completion
```

---

## Security Features

### 1. **CSRF Protection**
- Every form has CSRF token
- Token validated before processing
- Prevents cross-site request forgery

### 2. **Input Sanitization**
```php
$this->sanitize($input)
// Removes HTML tags
// Trims whitespace
// Converts special characters to HTML entities
```

### 3. **Database Transactions**
- All-or-nothing approach
- Prevents partial data if error occurs
- Maintains data integrity

### 4. **Prepared Statements**
```php
$stmt->execute([...]);
// Prevents SQL injection
// Auto-escapes special characters
```

### 5. **Role-Based Access**
- Only receptionists can access registration
- Checked in controller: `$this->requireRole('receptionist')`

---

## Performance Considerations

### Database Queries
- **5-6 INSERTs per registration** (depending on consultation/vitals)
- All wrapped in transaction (atomic)
- Auto-commit after all inserts

### Response Time
- **Expected:** 50-200ms
- **Includes:** Validation + 5 INSERTs + Redirect

### Optimization Opportunities
1. Batch insert (if registering multiple patients)
2. Cache registration number sequence
3. Index on registration_number for faster lookups

---

## Error Messages

| Error | Cause | User Action |
|-------|-------|-------------|
| "CSRF token validation failed" | Expired session / tampered form | Refresh page, try again |
| "Consultation fee and payment method are required" | Missing payment info | Fill payment fields |
| "Registration failed: Unknown column 'medical_history'" | Database schema mismatch | **FIXED** - Core fields only now |
| "Duplicate entry for 'registration_number'" | Race condition (rare) | Auto-retry with new number |

---

## Logging

### Success Log
```
Location: logs/user_actions.log
Format: [timestamp] Receptionist {user_id} registered patient {patient_id}
```

### Error Log
```
Location: logs/exceptions.log
Format: [timestamp] Registration error: {error_message}
```

---

## Testing Checklist

### Minimum Valid Registration
- [x] First name: "John"
- [x] Last name: "Doe"
- [x] Phone: "0712345678"
- [x] Visit type: "consultation"
- [x] Fee: "3000"
- [x] Payment: "cash"

### With All Fields
- [x] All demographics
- [x] Email and address
- [x] Emergency contacts
- [x] All vital signs

### Error Cases
- [x] Missing CSRF token → Error
- [x] Missing fee (consultation) → Error
- [x] Invalid BP format (e.g., "120-80") → Handles gracefully
- [x] Database down → Rollback, error message

---

## Recent Fix (2025-10-11)

### Problem
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'medical_history'
```

### Root Cause
Code was trying to insert `medical_history` and `occupation` columns that don't exist in current database schema.

### Solution
✅ Removed `medical_history` and `occupation` from INSERT statement
✅ Now only inserts core fields that exist in ALL database schemas:
- registration_number
- first_name, last_name
- date_of_birth, gender
- phone, email, address
- emergency_contact_name, emergency_contact_phone

### Result
Registration now works with existing database schema without requiring schema changes.

---

## Summary

**When user submits registration form:**
1. ✅ CSRF validated
2. ✅ Patient record created
3. ✅ Visit record created
4. ✅ Payment recorded (if consultation)
5. ✅ Consultation created (if consultation)
6. ✅ Vital signs saved (if provided)
7. ✅ Success message shown
8. ✅ Redirected to patients list

**Database state after successful registration:**
- 1 new patient
- 1 active visit
- 1 payment (if consultation)
- 1 pending consultation (if consultation)
- 1 vital signs record (if vitals provided)

**User can now:**
- View patient in patients list
- Doctor can attend patient (if consultation paid)
- Patient has complete registration record
