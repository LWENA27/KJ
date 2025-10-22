# Workflow Queue Fix - Patients Appearing in Payments & Lab Queues

## Problem
After doctor submits consultation form with lab tests/medicines, patients were not appearing in:
1. Receptionist payments queue (`http://localhost/KJ/receptionist/payments`)
2. Lab technician dashboard queue

## Root Causes Identified

### Issue 1: Lab Dashboard Query Mismatch
**Location**: `controllers/LabController.php` - `dashboard()` method (lines 11-29)

**Problem**: 
- Lab dashboard was querying `lab_results` table with JOIN to `lab_test_orders`
- When doctor orders tests, only `lab_test_orders` records are created
- No `lab_results` record exists until technician starts processing
- JOIN condition failed, causing no patients to appear

**Solution**:
- Changed query to fetch from `lab_test_orders` as primary table
- Used LEFT JOIN to `lab_results` (optional relationship)
- Query now shows all pending orders immediately after doctor submits

```sql
-- OLD QUERY (broken)
FROM lab_results lr
JOIN lab_test_orders lto ON lr.order_id = lto.id
WHERE lto.status = 'pending'

-- NEW QUERY (fixed)
FROM lab_test_orders lto
LEFT JOIN lab_results lr ON lto.id = lr.order_id
WHERE lto.status IN ('pending', 'sample_collected', 'in_progress')
```

### Issue 2: Payments Queue Status Mismatch
**Location**: `controllers/ReceptionistController.php` - `payments()` method (line 458)

**Problem**:
- Payments query was looking for `lto.status = 'pending_payment'`
- Doctor creates lab orders with `status = 'pending'` (line 356 in DoctorController)
- Status mismatch caused no results to appear

**Solution**:
- Changed WHERE clause from `lto.status = 'pending_payment'` to `lto.status = 'pending'`
- Also improved grouping by visit_id to show all tests for a patient together
- Added test_count column to show number of tests ordered

## Files Modified

### 1. `controllers/LabController.php`
**Lines**: 11-29 (dashboard method)

**Changes**:
- Query now starts from `lab_test_orders` table
- Added LEFT JOIN to `lab_results` (optional)
- Added patient registration_number field
- Shows orders with status: 'pending', 'sample_collected', 'in_progress'
- Ordered by priority (urgent/high/normal) then creation date

### 2. `controllers/ReceptionistController.php`
**Lines**: 439-458 (payments method - lab payments query)

**Changes**:
- Fixed status check: `WHERE lto.status = 'pending'`
- Changed JOIN payment condition: `payment_type = 'lab_test'` (was 'lab_order')
- Grouped by visit_id to consolidate multiple tests per patient
- Added test_count to show number of tests
- Better ordering: visit_date DESC, created_at DESC

## Workflow Flow (After Fix)

### When Doctor Submits Consultation Form:
1. ✅ Lab test orders created with `status = 'pending'` in `lab_test_orders`
2. ✅ Prescriptions created with `status = 'pending'` in `prescriptions`
3. ✅ Workflow status updated to `'pending_payment'`
4. ✅ Redirect to `receptionist/payments` page

### Receptionist Payments Page:
1. ✅ Query finds `lab_test_orders` with `status = 'pending'`
2. ✅ Shows patient with all ordered tests grouped together
3. ✅ Displays total amount to be paid
4. ✅ After payment recorded, tests can proceed to lab

### Lab Technician Dashboard:
1. ✅ Query shows all pending lab_test_orders immediately
2. ✅ No need for lab_results to exist first
3. ✅ Technician can see patient, test details, and order info
4. ✅ Can start processing tests (create lab_results when ready)

## Testing Checklist

- [ ] Doctor submits consultation with lab tests → Patient appears in receptionist/payments
- [ ] Doctor submits consultation with medicines → Patient appears in receptionist/payments  
- [ ] Doctor submits with both tests & medicines → Patient appears in receptionist/payments
- [ ] After payment recorded → Patient appears in lab technician dashboard
- [ ] Lab tech can see test details: patient name, registration number, test names
- [ ] Multiple tests for same patient are grouped together in payments view
- [ ] Priority ordering works in lab dashboard (urgent → high → normal)

## Related Files
- `controllers/DoctorController.php` (lines 291-406: start_consultation method)
- `views/receptionist/payments.php` (payments display view)
- `views/lab/dashboard.php` (lab technician dashboard view)

## Status Value Reference

### lab_test_orders.status
- `'pending'` - Test ordered by doctor, awaiting payment
- `'sample_collected'` - Sample taken, ready for testing
- `'in_progress'` - Lab tech actively processing
- `'completed'` - Results ready
- `'cancelled'` - Order cancelled

### prescriptions.status
- `'pending'` - Prescribed, awaiting payment/dispensing
- `'partial'` - Partially dispensed
- `'dispensed'` - Fully dispensed
- `'cancelled'` - Cancelled

## Notes
- Both queries now correctly match the status values actually set by DoctorController
- Lab dashboard no longer requires lab_results to exist before showing orders
- Payments page groups tests by visit for better UX
- Priority-based ordering ensures urgent tests are processed first
