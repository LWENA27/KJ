# Doctor Consultation Form Bug Fixes

## Problem
Doctor's consultation form was not saving clinical examination data and lab test orders were not appearing in receptionist's pending payments page.

## Root Causes Identified

### Issue 1: JavaScript Validation Mismatch
**Problem:** Radio button value was `'lab_tests'` but JavaScript validation was checking for `'lab'`

**Location:** `views/doctor/attend_patient.php` line 207

**Before:**
```javascript
if ((nextStep.value === 'lab' || nextStep.value === 'both') && selectedTests.length === 0) {
    alert('Please select at least one lab test');
    return false;
}
```

**After:**
```javascript
if ((nextStep.value === 'lab_tests' || nextStep.value === 'both') && selectedTests.length === 0) {
    alert('Please select at least one lab test');
    return false;
}
```

**Impact:** Validation was passing even when no tests were selected, allowing form submission without proper data.

### Issue 2: Toggle Section Function Mismatch
**Problem:** The `toggleSection()` function was checking for `'lab'` but receiving `'lab_tests'`

**Location:** `views/doctor/attend_patient.php` line 267

**Before:**
```javascript
if (section === 'lab' || section === 'both') {
    labSection.classList.remove('hidden');
}
```

**After:**
```javascript
if (section === 'lab_tests' || section === 'both') {
    labSection.classList.remove('hidden');
}
```

**Impact:** Lab tests section was not showing when "Send to Lab for Tests" was selected.

### Issue 3: Radio Button onChange Handler
**Problem:** Radio button was calling `toggleSection('lab')` instead of `toggleSection('lab_tests')`

**Location:** `views/doctor/attend_patient.php` line 98

**Before:**
```html
<input type="radio" name="next_step" value="lab_tests" class="mr-2" onchange="toggleSection('lab')">
```

**After:**
```html
<input type="radio" name="next_step" value="lab_tests" class="mr-2" onchange="toggleSection('lab_tests')">
```

**Impact:** Lab tests section was not appearing when the radio button was clicked.

## Fixes Applied

### 1. Fixed Validation Logic
Updated the `validateConsultationForm()` function to correctly check for `'lab_tests'` value:
- Changed validation condition from `'lab'` to `'lab_tests'`
- Now properly validates when lab tests option is selected

### 2. Fixed Toggle Section Function
Updated the `toggleSection()` function to handle the correct value:
- Changed condition from `section === 'lab'` to `section === 'lab_tests'`
- Lab tests section now displays correctly

### 3. Fixed Radio Button Handler
Updated the radio button's `onchange` attribute:
- Changed from `toggleSection('lab')` to `toggleSection('lab_tests')`
- Ensures consistency between value and handler parameter

### 4. Added Debug Logging
Added logging to the `start_consultation` method to track data flow:

```php
// Debug logging
error_log("Consultation submission - Patient ID: $patient_id, Visit ID: $visit_id, Consultation ID: $consultation_id");
error_log("Selected tests: " . ($_POST['selected_tests'] ?? 'EMPTY'));
error_log("Selected medicines: " . ($_POST['selected_medicines'] ?? 'EMPTY'));
```

**Purpose:** Helps diagnose issues by logging:
- Patient, visit, and consultation IDs
- Selected tests JSON data
- Selected medicines JSON data

## How the Form Works Now

### Step 1: Doctor Fills Clinical Data
- Main Complaint (M/C) - Required
- On Examination (O/E) - Required
- Preliminary Diagnosis - Optional
- Final Diagnosis - Optional

### Step 2: Doctor Selects Next Step
Four radio button options:
1. **Send to Lab for Tests** (value: `lab_tests`)
2. **Prescribe Medicine** (value: `medicine`)
3. **Both Lab & Medicine** (value: `both`)
4. **Discharge Patient** (value: `discharge`)

### Step 3: Dynamic Sections Appear
Based on selection:
- **lab_tests** → Shows lab tests search and selection
- **medicine** → Shows medicine search and selection
- **both** → Shows both sections
- **discharge** → No additional sections

### Step 4: Select Lab Tests (if applicable)
- Search for tests by typing
- Click test to add to selection
- Selected tests appear in list
- Can remove tests by clicking X button
- JavaScript updates hidden field `selected_tests` with JSON array

### Step 5: Select Medicines (if applicable)
- Search for medicines by typing
- Click medicine to add to selection
- Enter quantity, dosage, and instructions
- Selected medicines appear in list
- Can remove medicines by clicking X button
- JavaScript updates hidden field `selected_medicines` with JSON array

### Step 6: Form Validation
Before submission, validates:
- Main complaint and examination are filled
- Next step is selected
- If lab_tests selected → at least one test must be selected
- If medicine selected → at least one medicine must be selected
- Medicine quantities within stock limits
- Dosage and instructions are provided for all medicines

### Step 7: Form Submission
POST request to `/KJ/doctor/start_consultation` with:
```javascript
{
    csrf_token: "...",
    patient_id: 123,
    main_complaint: "...",
    on_examination: "...",
    preliminary_diagnosis: "...",
    final_diagnosis: "...",
    treatment_plan: "...",
    selected_tests: "[1, 3, 5]",  // JSON array of test IDs
    selected_medicines: "[{id:2, quantity:10, dosage:'500mg', ...}]"  // JSON array
}
```

### Step 8: Server Processing
1. **Validate CSRF token**
2. **Get patient_id and doctor_id**
3. **Find latest visit_id**
4. **Check if doctor can attend visit**
5. **Start/resume consultation** (gets consultation_id)
6. **Begin transaction**
7. **Update consultation record** with clinical data
8. **Process lab test orders:**
   - Decode JSON array
   - Find available lab technician
   - Create lab_test_orders records
   - Update workflow to 'pending_payment'
9. **Process medicine prescriptions:**
   - Decode JSON array
   - Create prescriptions records
   - Update workflow to 'pending_payment'
10. **Commit transaction**
11. **Set success message**
12. **Redirect to view_patient page**

### Step 9: Receptionist Can See Pending Payments
After doctor submits:
1. Lab test orders appear in `/KJ/receptionist/payments`
2. Display in red "Pending Lab Test Payments" table
3. Shows patient name, tests ordered, visit date, amount
4. "Record Payment" button available

Medicine prescriptions also appear:
1. Display in orange "Pending Medicine Payments" table
2. Shows patient name, medicines prescribed, visit date, amount
3. "Record Payment" button available

## Testing Checklist

- [x] Fixed JavaScript validation for `lab_tests` value
- [x] Fixed `toggleSection()` function to handle `lab_tests`
- [x] Fixed radio button onChange handler
- [x] Added debug logging to track data flow
- [ ] Test: Select "Send to Lab for Tests" → Lab section should appear
- [ ] Test: Select at least one lab test → Form should submit
- [ ] Test: Submit form → Check logs for selected_tests data
- [ ] Test: Check database for lab_test_orders records
- [ ] Test: Check receptionist pending payments page
- [ ] Test: Verify lab test orders appear with correct patient info
- [ ] Test: Verify amounts calculate correctly
- [ ] Test: Test medicine prescription flow similarly
- [ ] Test: Test "Both Lab & Medicine" option
- [ ] Test: Test "Discharge Patient" option

## Verification Steps

### 1. Check PHP Error Log
```bash
Get-Content "c:\xampp\htdocs\KJ\logs\php_errors.log" -Tail 20
```
Look for consultation submission logs with patient/visit/consultation IDs and selected tests data.

### 2. Check Database for Lab Test Orders
```sql
SELECT COUNT(*) FROM lab_test_orders;
SELECT * FROM lab_test_orders ORDER BY created_at DESC LIMIT 5;
```
Should see new records after doctor submits consultation.

### 3. Check Pending Payments Query
```sql
SELECT DISTINCT
    lto.patient_id,
    pv.id as visit_id,
    pt.first_name,
    pt.last_name,
    GROUP_CONCAT(DISTINCT lt.test_name SEPARATOR ', ') as test_names,
    SUM(lt.price) as total_amount
FROM lab_test_orders lto
JOIN patients pt ON lto.patient_id = pt.id
JOIN patient_visits pv ON lto.visit_id = pv.id
JOIN lab_tests lt ON lto.test_id = lt.id
LEFT JOIN payments pay ON pay.visit_id = pv.id 
    AND pay.payment_type = 'lab_test' 
    AND pay.payment_status = 'paid'
WHERE pay.id IS NULL
GROUP BY lto.patient_id, pv.id;
```
Should return unpaid lab test orders.

### 4. Check Receptionist Pending Payments Page
1. Navigate to http://localhost/KJ/receptionist/payments
2. Should see:
   - Pending Lab Tests count
   - Pending Medicines count  
   - Red table with lab test orders
   - Orange table with medicine prescriptions
   - "Record Payment" buttons

## Files Modified

1. **views/doctor/attend_patient.php**
   - Line 98: Fixed radio button onChange handler
   - Line 207: Fixed validation condition
   - Line 267: Fixed toggleSection function

2. **controllers/DoctorController.php**
   - Line 318-320: Added debug logging

## Related Documentation

- CLINICAL_EXAMINATION_WORKFLOW.md - Complete workflow documentation
- PAYMENT_PAGES_REDESIGN.md - Receptionist pending payments page design

## Date Fixed
October 11, 2025
