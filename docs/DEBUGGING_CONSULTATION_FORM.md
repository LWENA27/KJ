# Debugging Steps for Consultation Form Issue

## Problem
Doctor consultation form is not saving data and lab tests are not appearing in receptionist pending payments.

## Debugging Steps

### Step 1: Run Debug Script
1. Open browser and navigate to: `http://localhost/KJ/tmp/debug_consultation.php`
2. This will test:
   - Database connection
   - Lab tests availability
   - Patient and visit data
   - Lab test order creation
   - Pending payments query

### Step 2: Test Form with Browser Console Open
1. Open browser and press `F12` to open Developer Tools
2. Go to "Console" tab
3. Navigate to: `http://localhost/KJ/doctor/attend_patient/2` (or any patient ID)
4. Fill in the form:
   - **Main Complaint:** "Headache and fever"
   - **On Examination:** "Temperature elevated, BP normal"
5. Select "Send to Lab for Tests" radio button
   - Watch console - should see: "Toggle section called with: lab_tests"
6. Search for a lab test (type "blood" or "test")
   - Watch console - should see test search results
7. Click on a test to add it
   - Watch console - should see: "Adding test: {test object}"
   - Should see: "Test added. Total selected tests: 1"
   - Should see: "Updated selectedTests hidden field: [test_id]"
8. Click "Complete Consultation" button
   - Watch console - should see: "=== FORM VALIDATION STARTED ==="
   - Should see: "Next step value: lab_tests"
   - Should see: "Selected tests: [{test_object}]"
   - Should see: "Validation passed! Form will submit."

### Step 3: Check if Form Actually Submits
1. After clicking submit, page should redirect to patient view
2. Check URL - should be: `http://localhost/KJ/doctor/view_patient/X`
3. Look for success or error message at top of page

### Step 4: Check PHP Error Log
```powershell
Get-Content "c:\xampp\htdocs\KJ\logs\php_errors.log" -Tail 30
```
Look for lines with:
- "Consultation submission - Patient ID:"
- "Selected tests:"
- Any error messages

### Step 5: Check Database
```powershell
cd c:\xampp\mysql\bin
.\mysql.exe -u root -e "USE zahanati; SELECT * FROM lab_test_orders ORDER BY created_at DESC LIMIT 3;"
```
Should see new lab test order records if form submitted successfully.

### Step 6: Check Receptionist Pending Payments
1. Login as receptionist
2. Navigate to: `http://localhost/KJ/receptionist/payments`
3. Should see patient in "Pending Lab Test Payments" red table

## Common Issues and Solutions

### Issue 1: Lab Section Not Appearing
**Symptom:** When selecting "Send to Lab for Tests", the lab tests section doesn't show up
**Check Console:** Look for "Toggle section called with: lab_tests"
**Solution:** Already fixed in code - `toggleSection` now handles 'lab_tests' value

### Issue 2: Tests Not Being Added
**Symptom:** Click on test but it doesn't appear in selected list
**Check Console:** Look for "Adding test:" and "Test added"
**Possible Causes:**
- Test search API not returning results
- Test object missing required fields
- JavaScript error preventing addition

### Issue 3: Form Validation Failing
**Symptom:** Form doesn't submit when clicking "Complete Consultation"
**Check Console:** Look for validation messages
**Possible Causes:**
- Next step not selected
- Tests not selected when lab_tests option chosen
- Hidden field not populated

### Issue 4: Form Submits But No Lab Orders Created
**Symptom:** Form submits successfully but no lab orders in database
**Check:**
1. PHP error log for consultation submission messages
2. Check if `selected_tests` POST data is empty
3. Check if JSON decode is working
4. Check if transaction is being rolled back due to error

### Issue 5: Lab Orders Created But Not Showing in Pending Payments
**Symptom:** Lab orders exist in database but don't appear in receptionist page
**Check:**
1. Run pending payments SQL query manually
2. Check if payment already exists for the visit
3. Check workflow status of patient visit

## Detailed Console Output Examples

### Expected Console Output When Adding Test:
```
Adding test: Object { id: 1, name: "Blood Test", category_id: 1, price: "5000", ... }
Test added. Total selected tests: 1
Updated selectedTests hidden field: [1]
```

### Expected Console Output on Form Submit:
```
=== FORM VALIDATION STARTED ===
Next step value: lab_tests
Selected tests: Array [ {id: 1, name: "Blood Test", ...} ]
Selected medicines: Array []
Validation passed! Form will submit.
=== FORM VALIDATION ENDED ===
```

### Expected PHP Error Log Output:
```
[11-Oct-2025 09:00:00] Consultation submission - Patient ID: 2, Visit ID: 1, Consultation ID: 3
[11-Oct-2025 09:00:00] Selected tests: [1,2]
[11-Oct-2025 09:00:00] Selected medicines: 
```

## What to Report Back

Please run through these steps and report:

1. **Debug Script Results** - What did `debug_consultation.php` show?
2. **Browser Console Output** - Copy the console messages when:
   - Selecting radio button
   - Adding a test
   - Submitting form
3. **PHP Error Log** - Any consultation submission messages?
4. **Database Check** - Are lab_test_orders being created?
5. **Any Error Messages** - Screenshot or copy any error messages you see

## Files with Debug Logging

The following files now have console.log statements for debugging:
- `views/doctor/attend_patient.php` - Form validation and test selection
- `controllers/DoctorController.php` - Consultation submission processing

You can remove these console.log statements later once the issue is resolved.

## Date
October 11, 2025
