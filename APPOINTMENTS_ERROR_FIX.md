# Appointments Page Error Fix

## Date: October 11, 2025

---

## Error Details

**Error Message:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'c.appointment_date' in 'field list'
```

**Location:** `controllers/ReceptionistController.php` line 293

**Page:** `http://localhost/KJ/receptionist/appointments`

---

## Root Cause

The appointments query was trying to access `c.appointment_date` from the `consultations` table, but this column **does not exist** in the schema.

### Consultations Table Schema

The `consultations` table has:
- ✅ `follow_up_date` - Date for follow-up appointments
- ✅ `created_at` - When consultation was created
- ✅ `visit_id` - Links to `patient_visits.visit_date`
- ❌ `appointment_date` - **Does not exist**

---

## The Fix

### BEFORE (Incorrect)
```sql
SELECT c.*, p.first_name, p.last_name, u.first_name as doctor_first, 
       u.last_name as doctor_last,
       COALESCE(c.appointment_date, pv.visit_date, c.created_at) as appointment_date
       --        ^^^^^^^^^^^^^^^^^^^ Column doesn't exist!
FROM consultations c
JOIN patients p ON c.patient_id = p.id
JOIN users u ON c.doctor_id = u.id
LEFT JOIN patient_visits pv ON c.visit_id = pv.id
ORDER BY COALESCE(c.appointment_date, pv.visit_date, c.created_at) DESC
```

### AFTER (Fixed)
```sql
SELECT c.*, 
       p.first_name, 
       p.last_name, 
       u.first_name as doctor_first, 
       u.last_name as doctor_last,
       pv.visit_date,
       COALESCE(c.follow_up_date, pv.visit_date, DATE(c.created_at)) as appointment_date
       --        ^^^^^^^^^^^^^^^^ Correct column!
FROM consultations c
JOIN patients p ON c.patient_id = p.id
JOIN users u ON c.doctor_id = u.id
LEFT JOIN patient_visits pv ON c.visit_id = pv.id
ORDER BY COALESCE(c.follow_up_date, pv.visit_date, c.created_at) DESC
```

---

## Changes Made

### File: `controllers/ReceptionistController.php` (Lines 291-306)

**Changed:**
1. ❌ `c.appointment_date` → ✅ `c.follow_up_date`
2. Added `pv.visit_date` to SELECT list (for display)
3. Wrapped `c.created_at` in `DATE()` for consistency

**Logic:**
- If follow-up date exists → Use it as appointment date
- Else if visit date exists → Use visit date
- Else → Use consultation creation date

---

## What the Appointments Page Shows Now

### Appointment Date Priority:
1. **Follow-up date** (if doctor scheduled follow-up)
2. **Visit date** (original visit date)
3. **Created date** (when consultation record was created)

### Example Appointments Display:

```
┌─────────────────────────────────────────────────────────┐
│ APPOINTMENT 1                                           │
│ Patient: John Doe                                       │
│ Doctor: Dr. Smith                                       │
│ Date: 2025-10-11 (from visit_date)                    │
│ Status: Pending                                         │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ APPOINTMENT 2                                           │
│ Patient: Jane Smith                                     │
│ Doctor: Dr. Johnson                                     │
│ Date: 2025-10-15 (from follow_up_date)                │
│ Status: Completed                                       │
└─────────────────────────────────────────────────────────┘
```

---

## Testing

### Test the Appointments Page

1. **Navigate to:** `http://localhost/KJ/receptionist/appointments`

2. **Expected Results:**
   - ✅ Page loads without SQL errors
   - ✅ Shows list of consultations/appointments
   - ✅ Displays patient name, doctor name, appointment date
   - ✅ Orders by date (most recent first)

3. **Data Shown:**
   - All consultations from the database
   - Patient and doctor information
   - Appointment date (follow-up date > visit date > created date)
   - Consultation status (pending/in_progress/completed/cancelled)

---

## Related Tables

### Consultations Table Columns Used:
- `id` - Consultation ID
- `visit_id` - Link to patient visit
- `patient_id` - Link to patient
- `doctor_id` - Link to doctor
- `consultation_type` - new/follow_up/emergency/referral
- `status` - pending/in_progress/completed/cancelled
- `follow_up_date` - **Used as appointment date** if set
- `created_at` - Fallback date

### Patient Visits Table:
- `visit_date` - **Used as appointment date** if no follow-up date

---

## Error Prevention

### Why This Error Occurred:
The query was written assuming the `consultations` table had an `appointment_date` column, but the actual schema uses:
- `follow_up_date` for scheduled follow-ups
- `visit_date` (from `patient_visits`) for original appointments

### How to Avoid Similar Errors:
1. ✅ Always check actual database schema before writing queries
2. ✅ Refer to `database/zahanati.sql` for canonical schema
3. ✅ Test queries against actual database structure
4. ✅ Use database compatibility mode (avoid columns that might not exist)

---

## Summary

| Item | Status |
|------|--------|
| Error Fixed | ✅ Yes |
| PHP Syntax Valid | ✅ Yes |
| Query Corrected | ✅ Yes |
| Uses Correct Columns | ✅ Yes |
| Ready to Test | ✅ Yes |

---

## Additional Notes

### Future Enhancement Opportunity:
If you want a dedicated appointments system with separate appointment scheduling (not tied to consultations), you could create an `appointments` table:

```sql
CREATE TABLE `appointments` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT(11) NOT NULL,
  `doctor_id` INT(11) NOT NULL,
  `appointment_date` DATE NOT NULL,
  `appointment_time` TIME NOT NULL,
  `appointment_type` VARCHAR(50),
  `status` ENUM('scheduled','confirmed','cancelled','completed'),
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`)
);
```

But for now, the system uses:
- **Consultations** as appointments (pending/in_progress consultations)
- **Follow-up dates** for scheduled follow-ups
- **Visit dates** for original appointments

---

**Status:** ✅ **FIXED - Ready to test**

**Test URL:** `http://localhost/KJ/receptionist/appointments`
