# Quick Testing Guide - Service Allocation Enhanced Features

**Date:** November 5, 2025  
**Time Estimate:** 10-15 minutes for complete testing

---

## Pre-Testing Setup

### 1. Ensure Test Data Exists
```sql
-- Check if patient exists
SELECT * FROM patients LIMIT 1;

-- Check if active visits exist
SELECT * FROM patient_visits WHERE status = 'active' LIMIT 1;

-- Check if services exist
SELECT * FROM services WHERE is_active = 1;

-- Check if staff exists
SELECT * FROM users WHERE role IN ('doctor', 'lab_technician', 'nurse') AND is_active = 1;
```

### 2. Know Your Test IDs
- Test Patient ID: _____
- Test Doctor ID: _____
- Test Staff ID (Lab Tech): _____
- Test Service ID (Free): _____ (BP Check, Consultation, etc.)
- Test Service ID (Paid): _____ (ECG, Advanced tests, etc.)

---

## Test 1: Navigate to Allocation Form (2 min)

### Steps
1. Login as Doctor
2. Go to My Patients (`/doctor/patients`)
3. Click on a patient
4. In Quick Actions, click **"Allocate"** button
5. Should redirect to `/doctor/allocate_resources?patient_id=X`

### Expected Result
✅ Form loads with:
- Patient info header (name, reg #, phone, age)
- Service search + category filter
- **"Add More Services"** button (green)
- Service checkboxes
- "Allocate Services" submit button

### Screenshot Markers
- Look for: **"Add More Services"** button in top-right
- Look for: Search field with placeholder "Search by name, code, or description..."
- Look for: Category dropdown with options (All, Clinical, Lab, Imaging, Procedure)

---

## Test 2: Service Search (3 min)

### Test 2a: Real-Time Search
1. Type "ECG" in search field
2. Verify: Only ECG-related services appear
3. Type "BP" → Only BP Check appears
4. Clear search → All services reappear

### Test 2b: Category Filter
1. Select "Laboratory Tests" from dropdown
2. Verify: Only lab tests appear (ECG, Blood Tests, etc.)
3. Select "Clinical Services"
4. Verify: Only clinical appear (BP Check, Consultation, etc.)

### Test 2c: Combined Search + Filter
1. Type "test" in search
2. Select "Laboratory Tests" in category
3. Verify: Only shows lab tests with "test" in name

### Test 2d: Mobile Responsiveness
1. Resize browser to mobile size (375px width)
2. Verify: Search and filter still visible
3. Verify: Services list stacks properly

### Expected Results
✅ All searches filter in <1 second  
✅ No page reload  
✅ Responsive on mobile  
✅ Clearing inputs restores all services

---

## Test 3: Dynamic Service Addition (4 min)

### Test 3a: Add Service Modal
1. Click **"Add More Services"** button
2. Modal appears with title "Add Additional Service"
3. Modal shows:
   - Service dropdown
   - Staff dropdown
   - Notes textarea
   - Add Service button
   - List area for added services

### Test 3b: Add Single Service
1. In modal, select a service from dropdown (e.g., "ECG")
2. Select staff member from dropdown
3. (Optional) Add notes
4. Click **"Add Service"** button
5. Verify: Service appears in list below modal
6. Verify: Green card shows service name + staff name
7. Verify: "Remove" button (trash icon) visible

### Test 3c: Add Multiple Services
1. Add 3 different services (repeat Test 3b)
2. Verify: All 3 appear in list
3. Remove middle one
4. Verify: Now shows 2 services

### Test 3d: Close Modal
1. After adding services, click "Cancel" button
2. Modal closes
3. Verify: Added services still visible on page
4. Verify: Can close by clicking backdrop (X area)

### Test 3e: Submit Multiple Services
1. With 2-3 added services in modal
2. Also select 1-2 services from main form
3. Click **"Allocate Services"** submit button
4. Verify: Loading state (spinner on button)
5. Check database: All services created
   ```sql
   SELECT COUNT(*) FROM service_orders WHERE patient_id = X AND status = 'pending';
   ```

### Expected Results
✅ Modal opens/closes smoothly  
✅ Services add to list without reload  
✅ Remove works correctly  
✅ Multiple services allocate in single submission  
✅ Database records created for all services

---

## Test 4: Payment Validation (3 min)

### Setup
```sql
-- Check a patient's payment status
SELECT * FROM payments 
WHERE visit_id = (
    SELECT id FROM patient_visits 
    WHERE patient_id = X 
    ORDER BY created_at DESC LIMIT 1
);
```

### Test 4a: Free Service (No Payment Required)
1. Select a free service (e.g., BP Check, price = 0)
2. Select staff
3. Submit
4. Verify: Success message shows service allocated
5. Check database: service_order created

### Test 4b: Paid Service WITH Payment
1. Ensure payment received for visit
2. Select a paid service (price > 0)
3. Select staff
4. Submit
5. Verify: Success message shows service allocated
6. Check database: service_order created

### Test 4c: Paid Service WITHOUT Payment (The Critical Test)
1. Choose a paid service where NO payment received
2. Select staff
3. Submit
4. Verify: Response shows warning message
5. Verify: Message says "{X} service(s) require payment before allocation"
6. Verify: Service NOT created in database
   ```sql
   SELECT * FROM service_orders 
   WHERE patient_id = X AND service_id = Y;
   -- Should be EMPTY
   ```
7. Verify: Other free services allocated (if selected)

### Test 4d: Mixed Services (Some Paid, Some Free)
1. Select 3 services:
   - Service A: Free ✅
   - Service B: Paid but NOT paid 🚫
   - Service C: Free ✅
2. Submit
3. Verify: Success with warning
4. Verify: Message says "Service B requires payment"
5. Verify: Service A & C created, Service B NOT created

### Expected Results
✅ Free services allocate always  
✅ Paid services allocate only if payment received  
✅ Unpaid services show clear warning  
✅ Warning includes service name + price  
✅ Database only contains paid/free services

### JSON Response Example
```json
{
    "success": true,
    "message": "2 service(s) allocated successfully",
    "orders_created": 2,
    "warning": "1 service(s) require payment before allocation",
    "unpaid_services": [
        {
            "service_name": "Advanced ECG",
            "price": 50000,
            "service_id": 5
        }
    ]
}
```

---

## Test 5: Notification System (2 min) - Optional

### If notifications table EXISTS
1. Allocate service to staff member
2. Check notifications table:
   ```sql
   SELECT * FROM notifications 
   WHERE user_id = [STAFF_ID] 
   ORDER BY created_at DESC LIMIT 1;
   ```
3. Verify: Record created with:
   - `type` = 'service_allocation'
   - `title` = 'New Service Allocated'
   - `message` includes service name + patient name
   - `is_read` = 0
   - `related_id` = service_order ID

### If notifications table DOES NOT EXIST
1. Allocate service
2. Verify: No error shown
3. Verify: Service still allocated successfully
4. Check error log: Should be clean (no errors logged)

### Expected Results
✅ Notifications created (if table exists)  
✅ No errors if table missing  
✅ Graceful degradation working

---

## Browser Developer Tools Checks

### Console (F12 → Console tab)
- [ ] No JavaScript errors
- [ ] No 404 errors on resources
- [ ] No SQL errors

### Network (F12 → Network tab)
- [ ] Search/filter requests: <100ms
- [ ] Form submit: <500ms
- [ ] All requests: 200 OK status

### Performance (F12 → Performance tab, record)
- [ ] Search filtering: <50ms
- [ ] Modal open: <10ms
- [ ] Form submission: <1s

---

## Edge Cases to Test

### Edge Case 1: No Services Available
1. If services are disabled
2. Verify: Empty message shown
3. Verify: No error thrown

### Edge Case 2: No Staff Available
1. If all staff inactive
2. Verify: Cannot select staff
3. Verify: Clear error message

### Edge Case 3: No Active Visit
1. Patient with no active visit
2. Verify: Warning shown "No Active Visit"
3. Verify: Form disabled or hidden

### Edge Case 4: Invalid CSRF Token
1. Change CSRF token in form
2. Submit
3. Verify: 403 error
4. Verify: Error message shown

### Edge Case 5: Very Long Service Names
1. Select service with long name (50+ characters)
2. Verify: Text doesn't break layout
3. Verify: Tooltip shows full name on hover

---

## Final Verification Checklist

- [ ] **Feature 1 Complete**: Dynamic service addition works
- [ ] **Feature 2 Complete**: Search and filter functional
- [ ] **Feature 3 Complete**: Payment validation working
- [ ] **Feature 4 Complete**: Notifications sent (or gracefully skipped)
- [ ] **Syntax OK**: No PHP errors (`php -l` passed)
- [ ] **Database**: All records created correctly
- [ ] **UI/UX**: Responsive, clean, intuitive
- [ ] **Performance**: All operations <1 second
- [ ] **Error Handling**: Clear error messages
- [ ] **Mobile**: Works on small screens

---

## Success Indicators

✅ **All 5 tests pass**  
✅ **Database records created correctly**  
✅ **No console errors**  
✅ **Payment validation prevents unpaid allocations**  
✅ **Multiple services allocate in one submission**  
✅ **Staff notified (if table exists)**  
✅ **Performance acceptable (<1s per operation)**  

---

## If Tests Fail

### Issue: Search not filtering
**Debug:**
```javascript
// In console
document.querySelectorAll('.service-item').forEach(item => {
    console.log(item.dataset);
});
```
Check `data-search-text` and `data-category` attributes.

### Issue: Modal not opening
**Debug:**
```javascript
// In console
document.getElementById('addServiceModal').classList.remove('hidden');
```
If this works, JavaScript event listener issue. Check console for errors.

### Issue: Allocation fails silently
**Debug:**
```javascript
// Check CSRF token
console.log(document.querySelector('input[name="csrf_token"]').value);
```
Check Network tab for response status and error message.

### Issue: Payment check not working
**Debug:**
```sql
SELECT * FROM payments 
WHERE visit_id = 1 
AND payment_status = 'completed' 
AND amount >= 0;
```
Ensure payment records exist with correct status.

---

## Quick Command Reference

### Check Data
```sql
-- List all patients
SELECT id, first_name, last_name FROM patients LIMIT 5;

-- List active services
SELECT id, service_name, price FROM services WHERE is_active = 1;

-- List active staff
SELECT id, CONCAT(first_name, ' ', last_name) as name, role FROM users WHERE is_active = 1;

-- List allocated services
SELECT * FROM service_orders WHERE patient_id = X ORDER BY created_at DESC;

-- Check notifications
SELECT * FROM notifications WHERE user_id = X ORDER BY created_at DESC;
```

### Clear Test Data
```sql
-- Delete test allocations (backup first!)
DELETE FROM service_orders WHERE patient_id = X AND status = 'pending';

-- Delete test notifications
DELETE FROM notifications WHERE user_id = X;
```

---

## Notes for Next Team

- [ ] Document any issues found
- [ ] Note browser/OS combinations tested
- [ ] Test with real patient data (not just demo)
- [ ] Test with 50+ services (performance check)
- [ ] Monitor error logs after deployment
- [ ] Gather user feedback from doctors/staff

---

**Ready to Test? Start with Test 1 above!** 🚀

