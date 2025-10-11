# Vital Signs Display Fix

## Problem
Vital signs were not showing in the doctor's medical record view (`view_patient` page) for newly registered patients.

## Root Cause
The `DoctorController::view_patient()` method was only fetching patient data from the `patients` table, but vital signs are stored in a separate `vital_signs` table linked to each visit.

The view template was trying to access fields like:
- `$patient['temperature']`
- `$patient['blood_pressure']`
- `$patient['pulse_rate']`
- `$patient['body_weight']`
- `$patient['height']`

But these fields don't exist in the patients table.

## Database Schema
Vital signs are stored in the `vital_signs` table with the following structure:
```sql
CREATE TABLE `vital_signs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `visit_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `temperature` DECIMAL(4,1) DEFAULT NULL,
  `blood_pressure_systolic` INT(11) DEFAULT NULL,
  `blood_pressure_diastolic` INT(11) DEFAULT NULL,
  `pulse_rate` INT(11) DEFAULT NULL,
  `respiratory_rate` INT(11) DEFAULT NULL,
  `weight` DECIMAL(5,1) DEFAULT NULL,
  `height` DECIMAL(5,1) DEFAULT NULL,
  `bmi` DECIMAL(4,1) AS (CASE WHEN height > 0 THEN (weight / ((height/100) * (height/100))) ELSE NULL END) STORED,
  `recorded_by` INT(11) NOT NULL,
  `recorded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
```

## Solution

### 1. Updated DoctorController::view_patient() Method
Added a query to fetch the latest vital signs for the patient:

```php
// Get latest vital signs for this patient
$stmt = $this->pdo->prepare("
    SELECT vs.*, pv.visit_date
    FROM vital_signs vs
    LEFT JOIN patient_visits pv ON vs.visit_id = pv.id
    WHERE vs.patient_id = ?
    ORDER BY vs.recorded_at DESC
    LIMIT 1
");
$stmt->execute([$patient_id]);
$vital_signs = $stmt->fetch();

$this->render('doctor/view_patient', [
    'patient' => $patient,
    'consultations' => $consultations,
    'vital_signs' => $vital_signs,  // Added vital signs data
    'csrf_token' => $this->generateCSRF()
]);
```

### 2. Updated views/doctor/view_patient.php Template
Changed the vital signs display to use the `$vital_signs` array with correct field names:

**Temperature:**
```php
<?php 
if (!empty($vital_signs['temperature'])) {
    echo htmlspecialchars($vital_signs['temperature']) . '°C';
}
?>
```

**Blood Pressure:**
```php
<?php 
if (!empty($vital_signs['blood_pressure_systolic']) && !empty($vital_signs['blood_pressure_diastolic'])) {
    echo htmlspecialchars($vital_signs['blood_pressure_systolic']) . '/' . htmlspecialchars($vital_signs['blood_pressure_diastolic']);
}
?>
```

**Pulse Rate:**
```php
<?php 
if (!empty($vital_signs['pulse_rate'])) {
    echo htmlspecialchars($vital_signs['pulse_rate']) . ' bpm';
}
?>
```

**Body Weight:**
```php
<?php 
if (!empty($vital_signs['weight'])) {
    echo htmlspecialchars($vital_signs['weight']) . ' kg';
}
?>
```

**Height:**
```php
<?php 
if (!empty($vital_signs['height'])) {
    echo htmlspecialchars($vital_signs['height']) . ' cm';
}
?>
```

## How Vital Signs Are Recorded

Vital signs are recorded during patient registration in the receptionist's `register_patient` form:

1. Receptionist selects "Doctor Consultation" as the visit type
2. The vital signs section becomes visible (controlled by JavaScript)
3. Receptionist can enter:
   - Temperature (°C)
   - Blood Pressure (systolic/diastolic)
   - Pulse Rate (bpm)
   - Body Weight (kg)
   - Height (cm)
4. When the form is submitted, vital signs are saved to the `vital_signs` table linked to the patient's visit

## Testing

To verify the fix:

1. **Register a new patient:**
   - Go to Receptionist → Register Patient
   - Fill in patient details
   - Select "Doctor Consultation" as visit type
   - Fill in vital signs (Temperature: 37.5, Blood Pressure: 120/80, Pulse: 72, Weight: 70, Height: 175)
   - Complete registration

2. **View patient record:**
   - Login as Doctor
   - Go to Doctor → Patients
   - Click on the newly registered patient
   - Navigate to patient view page
   - Verify that all vital signs are displayed correctly in the medical record form

## Files Modified

1. `controllers/DoctorController.php` - Added vital signs query in `view_patient()` method
2. `views/doctor/view_patient.php` - Updated vital signs display to use correct data source and field names

## Date Fixed
October 11, 2025
