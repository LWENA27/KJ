yiel# Testing Guide: IPD & Radiology Modules

## Overview
This guide will help you test the newly implemented IPD (In-Patient Department) and Radiology modules.

## Prerequisites

### 1. Database Setup
All database tables should be created. Verify:
```bash
mysql -u root -p zahanati -e "SHOW TABLES LIKE 'ipd_%'; SHOW TABLES LIKE 'radiology_%';"
```

Expected output: 9 new tables (4 radiology + 5 IPD)

### 2. Test Data Loaded
Verify seed data:
```bash
mysql -u root -p zahanati -e "
SELECT COUNT(*) as radiology_tests FROM radiology_tests;
SELECT COUNT(*) as wards FROM ipd_wards;
SELECT COUNT(*) as beds FROM ipd_beds;
"
```

Expected: 17 radiology tests, 6 wards, 33 beds

## Creating Test Users

### Option 1: SQL Method (Quick)

**Create Radiologist User:**
```sql
-- Insert new user
INSERT INTO users (username, password_hash, first_name, last_name, email, role, is_active, created_at)
VALUES ('radiologist1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
        'Sarah', 'Johnson', 'radiologist@hospital.com', 'radiologist', 1, NOW());

-- Note: Default password is 'password' (hashed)
```

**Add Nurse Role to Existing Receptionist:**
```sql
-- Find receptionist user ID
SELECT id, username, first_name, last_name FROM users WHERE role = 'receptionist' LIMIT 1;

-- Add nurse role (replace USER_ID with actual ID)
INSERT INTO user_roles (user_id, role, is_primary, granted_by, is_active, granted_at)
VALUES (USER_ID, 'nurse', 0, 1, 1, NOW());
```

### Option 2: Admin Panel Method (Recommended for Future)
Once multi-role UI is built, admins can assign roles through the interface.

## Test Workflows

### Radiology Module Testing

#### 1. Create a Test Order (Via Doctor Interface)
1. Login as doctor
2. Select a patient with an active visit
3. Navigate to patient details
4. Click "Order Test" → Select "Radiology"
5. Choose a radiology test (e.g., "Chest X-Ray")
6. Set priority (Routine/Urgent/STAT)
7. Submit order

**Verify in Database:**
```sql
SELECT * FROM radiology_test_orders WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1;
```

#### 2. Login as Radiologist
1. Logout current user
2. Login with radiologist credentials
3. Default redirect: `/radiologist/dashboard`

**Dashboard Checks:**
- [ ] Pending orders count displayed
- [ ] Today's tests count displayed
- [ ] Completed this week count displayed
- [ ] Pending orders table shows orders
- [ ] Today's schedule section populated

#### 3. Perform Test Workflow
1. Click "View All Orders" or navigate to `/radiologist/orders`
2. Find a pending order
3. Click "Perform Test"
4. Review patient info and preparation instructions
5. Click "Confirm - Start Test"

**Verify Status Change:**
```sql
SELECT order_number, status FROM radiology_test_orders WHERE id = ?;
-- Expected: status = 'in_progress'
```

#### 4. Record Result
1. From orders list, click "Record Result" on in-progress order
2. Fill in:
   - Findings (detailed observations)
   - Impression/Conclusion (diagnosis)
   - Upload images (optional)
3. Submit result

**Verify Result Saved:**
```sql
SELECT * FROM radiology_results WHERE order_id = ?;
-- Should have new record with findings and impression
```

**Verify Order Completed:**
```sql
SELECT status FROM radiology_test_orders WHERE id = ?;
-- Expected: status = 'completed'
```

#### 5. View Result
1. From orders list, click "View Result" on completed order
2. Verify display shows:
   - Patient information
   - Test details
   - Findings
   - Impression
   - Performed by (radiologist name)
   - Timestamps
3. Test print functionality

### IPD Module Testing

#### 1. Login with IPD Access
Login as receptionist, nurse, doctor, or admin (all have access to IPD)

**Multi-Role Test:**
If user has both receptionist AND nurse roles, verify they can access IPD.

#### 2. View IPD Dashboard
Navigate to `/ipd/dashboard`

**Dashboard Checks:**
- [ ] Total beds count correct
- [ ] Occupied beds count
- [ ] Available beds count
- [ ] Active admissions count
- [ ] Ward occupancy grid shows all wards
- [ ] Recent admissions table populated

#### 3. View Bed Management
Navigate to `/ipd/beds`

**Bed Management Checks:**
- [ ] Ward filter tabs work (All, General A, General B, Private, ICU, etc.)
- [ ] Bed grid shows all beds
- [ ] Bed status color-coded correctly:
  - Green = Available
  - Blue = Occupied
  - Yellow = Maintenance
  - Purple = Reserved
- [ ] Current patient name shown for occupied beds

#### 4. Admit Patient Workflow
1. Navigate to `/ipd/admit`
2. Select a patient (must have active visit)
3. Select ward (e.g., "General A")
4. Select available bed
5. Enter diagnosis
6. Enter initial vital signs (optional):
   ```json
   {"temperature": "37.2", "blood_pressure": "120/80", "pulse_rate": "72"}
   ```
7. Select attending doctor
8. Add admission notes
9. Submit

**Verify Admission Created:**
```sql
SELECT * FROM ipd_admissions WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1;
-- Should have new admission with status = 'active'
```

**Verify Bed Status Updated:**
```sql
SELECT status, current_patient_id FROM ipd_beds WHERE id = ?;
-- Expected: status = 'occupied', current_patient_id = patient_id
```

#### 5. View Admission Details
1. Navigate to `/ipd/admissions`
2. Click "View" on active admission
3. Verify display shows:
   - Patient information
   - Admission details
   - Ward/bed assignment
   - Length of stay calculation
   - Empty progress notes (initially)
   - Empty medications (initially)

#### 6. Add Progress Note
1. From admission view, click "Add Note"
2. Enter vital signs:
   - Temperature: 37.5
   - Pulse: 75
   - BP Systolic: 120
   - BP Diastolic: 80
3. Enter progress note text
4. Submit

**Verify Note Saved:**
```sql
SELECT * FROM ipd_progress_notes WHERE admission_id = ? ORDER BY note_datetime DESC LIMIT 1;
```

#### 7. Discharge Patient Workflow
1. From admission view, click "Discharge Patient"
2. Set discharge date/time
3. Enter discharge diagnosis
4. Select condition at discharge (e.g., "Improved")
5. Enter discharge summary
6. Enter discharge instructions
7. Set follow-up if required
8. Submit

**Verify Discharge Processed:**
```sql
SELECT status, discharge_datetime FROM ipd_admissions WHERE id = ?;
-- Expected: status = 'discharged', discharge_datetime IS NOT NULL
```

**Verify Bed Released:**
```sql
SELECT status, current_patient_id FROM ipd_beds WHERE id = ?;
-- Expected: status = 'available', current_patient_id IS NULL
```

**Verify Days Calculated:**
```sql
SELECT total_days FROM ipd_admissions WHERE id = ?;
-- Should show number of days between admission and discharge
```

## Multi-Role Testing

### Test Multi-Role User Access
1. Create user with multiple roles (receptionist + nurse)
2. Login as this user
3. Verify access to:
   - [ ] Receptionist functions (patient registration, appointments)
   - [ ] IPD/Nurse functions (admissions, progress notes)

### Verify Role Restrictions
1. Login as radiologist
2. Attempt to access `/ipd/dashboard` (should be denied)
3. Login as receptionist (without nurse role)
4. Attempt to access `/radiologist/dashboard` (should be denied)

## Navigation Testing

### Sidebar Menu Verification
Login as each role and verify sidebar shows correct menu items:

**Radiologist:**
- [ ] Radiology menu visible
- [ ] Dashboard, Orders, Results links work

**Receptionist/Nurse/Doctor:**
- [ ] IPD menu visible
- [ ] Dashboard, Beds, Admit Patient, Admissions links work

**Other Roles:**
- [ ] Radiology/IPD menus NOT visible

## Performance Testing

### Load Testing
```sql
-- Create 10 test admissions
-- Create 20 test radiology orders
-- Verify dashboard loads in < 2 seconds
-- Verify orders page loads in < 2 seconds
```

## Common Issues & Troubleshooting

### Issue: "Access Denied" Message
**Solution:** Verify user has correct role in `users` table and/or `user_roles` table

### Issue: No Orders Showing in Radiology
**Solution:** Create test orders via doctor interface first, or insert test data:
```sql
INSERT INTO radiology_test_orders (order_number, patient_id, visit_id, test_id, ordered_by, priority, status)
VALUES ('RO20260125001', 1, 1, 1, 2, 'routine', 'pending');
```

### Issue: No Available Beds
**Solution:** Check bed status and release if needed:
```sql
UPDATE ipd_beds SET status = 'available', current_patient_id = NULL WHERE id = ?;
```

### Issue: Admission Form Missing Patients
**Solution:** Ensure patients have active visits:
```sql
SELECT p.id, p.first_name, p.last_name, pv.id as visit_id
FROM patients p
LEFT JOIN patient_visits pv ON p.id = pv.patient_id AND pv.status = 'active'
WHERE pv.id IS NOT NULL;
```

### Issue: Total Days Not Calculating
**Solution:** Verify trigger exists and works:
```sql
SHOW TRIGGERS LIKE 'ipd_admissions';
-- Should show: update_admission_days

-- Test trigger:
UPDATE ipd_admissions SET updated_at = NOW() WHERE id = ?;
SELECT total_days FROM ipd_admissions WHERE id = ?;
```

## Test Results Checklist

### Radiology Module
- [ ] Dashboard displays correctly
- [ ] Orders list works with filters
- [ ] Perform test updates status
- [ ] Record result saves data
- [ ] View result displays properly
- [ ] Order status workflow (pending → in_progress → completed) works

### IPD Module
- [ ] Dashboard shows accurate stats
- [ ] Bed management grid works
- [ ] Ward filtering works
- [ ] Admit patient creates admission and updates bed
- [ ] Admissions list displays correctly
- [ ] View admission shows details
- [ ] Progress notes can be added
- [ ] Discharge releases bed correctly
- [ ] Total days calculation works

### Multi-Role Functionality
- [ ] Users can have multiple roles
- [ ] Multi-role users can access all their role dashboards
- [ ] Role restrictions enforced (no unauthorized access)

## Next Steps After Testing

1. **Create Real Users:** Set up actual staff accounts with appropriate roles
2. **Train Staff:** Provide training on new modules
3. **Monitor Performance:** Check query performance and optimize if needed
4. **Backup Data:** Ensure backup includes new tables
5. **Documentation:** Update user manuals with new features

## Support

For issues or questions:
- Check application logs: `/var/www/html/KJ/logs/`
- Check MySQL error log
- Review controller error handling
- Verify database constraints not violated
