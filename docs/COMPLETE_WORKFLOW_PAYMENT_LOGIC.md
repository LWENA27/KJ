# Complete Workflow - Patient Journey & Payment Logic

**Date:** January 27, 2026  
**Status:** ✅ FIXED - Deprecated constant removed

## Patient Journey Flow

### Phase 1: RECEPTION (Initial Visit)

```
Patient Arrives at Hospital
    ↓
Reception Staff
    ├─ Register new patient OR verify existing patient
    ├─ Create/update patient record
    └─ Direct to Accountant for payment
```

**What happens:**
- Patient is registered in the system
- Patient gets a registration number
- Consultation fee is established
- Patient proceeds to payment

---

### Phase 2: ACCOUNTANT (Payment)

```
Accountant Counter
    ├─ Verify patient identity
    ├─ Calculate consultation fee
    ├─ Process payment
    └─ Issue receipt
        ↓
    Patient Ready for Doctor
```

**Payment Logic:**
- ✅ **Payment REQUIRED here** - First entry point
- Patient must pay consultation fee
- **Only happens ONCE** per visit
- After payment, patient proceeds to doctor

**Payment Records:**
- Payment amount recorded
- Date/time recorded
- Doctor assignment recorded
- Payment status: PAID

---

### Phase 3: DOCTOR - Initial Consultation

```
Doctor's Consultation Room
    ↓
Doctor Attends Patient
    ├─ Take main complaint (M/C)
    ├─ Perform physical examination (O/E)
    ├─ Make preliminary diagnosis
    ├─ Decide NEXT STEPS among:
    │  ├─ Lab Tests
    │  ├─ Radiology Tests
    │  ├─ IPD Admission (Ward)
    │  ├─ Medicine Prescription
    │  ├─ Allocate Services (Procedures)
    │  └─ Combination of above
    └─ Complete Consultation
        ↓
    Patient goes to selected service
```

**Payment Check:**
- ✅ **Payment already done** at Accountant
- Doctor can proceed without additional payment
- Payment blocking SKIPPED for this flow

**Example Scenario 1:**
Doctor decides: "Patient needs Radiology (Chest X-Ray)"
- Patient goes to Radiology department
- Radiology completes test
- Results recorded in system

---

### Phase 4: SERVICE PROVIDER (e.g., Radiologist)

```
Radiology Department
    ├─ Radiologist reviews X-Ray
    ├─ Performs interpretation
    ├─ Records findings/impression/recommendations
    └─ Saves results to patient record
        ↓
    Patient returns to Doctor
```

**Payment Logic:**
- Service fee may be charged here (for radiography)
- **NOT doctor's concern** - doctor already paid
- Patient/ward responsible for service fees
- No payment blocking for radiologist

---

### Phase 5: DOCTOR - Review Results & Decide Next Steps

```
Doctor Reviews Radiology Results
    ├─ Views findings from radiologist
    ├─ Sees impression and recommendations
    ├─ Makes clinical decision on next step
    └─ Options:
       ├─ Send to Lab (more tests)
       ├─ Send to Ward (admit for care)
       ├─ Send to Services (procedures)
       ├─ Prescribe Medicine (pharmacy)
       └─ Discharge (if done)
            ↓
       Patient proceeds to new service
```

**Payment Logic - CRITICAL:**
- ✅ **NO PAYMENT REQUIRED** - Patient already paid
- Doctor is just making clinical decisions
- Doctor is routing patient to services
- Each service may have its own fees (handled separately)
- **Payment blocking BYPASSED** when using ?action= parameter

**Why?**
- Patient paid once at reception/accountant
- Doctor is not starting a new consultation
- Doctor is just allocating follow-up services
- Payment already covers doctor's consultation time
- Additional service fees are separate line items

---

### Phase 6: WARD/SERVICE (if IPD Admission)

```
Ward/IPD Department
    ├─ Patient admitted for care
    ├─ Nursing staff provides care
    ├─ All treatments tracked
    ├─ Daily ward fees charged
    └─ All services documented
        ↓
    Patient may return to Doctor for re-evaluation
```

**Payment:**
- Ward has its own fees (per day)
- Service charges tracked separately
- Doctor consultation fee already paid

---

### Phase 7: FOLLOW-UP (if patient returns)

```
If Patient Returns for Follow-up
    ├─ May need NEW consultation
    ├─ May need to pay AGAIN (new visit)
    └─ New consultation workflow starts
```

---

## Complete Patient Journey Example

**Example: Patient with Suspected Pneumonia**

```
1. RECEPTION
   Patient registers → Gets Reg No.: KJ20260008

2. ACCOUNTANT  
   ✅ Consultation fee: 50,000 TSH
   ✅ PAYMENT DONE
   Receipt issued

3. DOCTOR - Initial Consultation
   ✅ NO PAYMENT CHECK (already paid)
   Chief Complaint: Cough for 3 days
   Examination: Fever, chest pain
   Preliminary Diagnosis: Suspected Pneumonia
   Decision: "Patient needs Chest X-Ray (Radiology)"
   → Saves consultation
   → Patient directed to Radiology

4. RADIOLOGIST
   Performs Chest X-Ray
   Findings: "Consolidation in right lower lobe"
   Impression: "Right lower lobe pneumonia"
   Recommendations: "Requires admission for antibiotics"
   → Saves results

5. DOCTOR - Reviews Radiology Results
   ✅ NO PAYMENT CHECK (already paid at step 2)
   ✅ Doctor clicks "Send to Ward" from radiology results
   ✅ NO PAYMENT BLOCKING (?action=ipd parameter used)
   ✅ Attend Patient form loads with IPD section pre-selected
   Selects: General Ward A
   Reason: "Pneumonia requiring IV antibiotics and monitoring"
   → Saves consultation with IPD admission
   → Patient directed to Ward

6. WARD STAFF
   Admits patient
   Provides care per doctor's orders
   ✅ Ward fees charged separately (per day)
   ✅ Medication costs tracked
   ✅ Service charges documented

7. DISCHARGE
   Doctor reviews patient progress
   Patient recovers
   Discharged with follow-up instructions
```

---

## Payment Blocking Rules

### When Payment IS Checked ✅

1. **Direct access to attend_patient without action parameter**
   - URL: `/doctor/attend_patient/44`
   - Scenario: Doctor clicks patient link from dashboard
   - Result: Payment check required
   - Reason: Could be starting a new consultation

2. **Radiologist starting radiology procedure**
   - Radiologist must verify payment BEFORE starting test
   - Reason: Radiologist is the service provider
   - They shouldn't start without confirmation

3. **Lab technician starting lab test**
   - Lab tech must verify payment before drawing blood
   - Reason: Lab is a paid service
   - Prevents unpaid tests

### When Payment is BYPASSED ❌

1. **Doctor routing from radiology results with ?action=**
   - URL: `/doctor/attend_patient/44?action=lab`
   - Reason: Patient already paid at reception
   - Doctor is just allocating services
   - No new consultation, just decision-making

2. **Doctor routing from lab results with ?action=**
   - URL: `/doctor/attend_patient/44?action=ward`
   - Reason: Same as above
   - Follow-up decision, not new consultation

3. **Any action parameter present**
   - URL contains `?action=ipd`, `?action=medicine`, etc.
   - Reason: These are follow-up actions, not new consultations
   - Patient already authorized payment at start

---

## Database Tracking

All patient movements are tracked:

### consultations table
- `patient_id` - Which patient
- `doctor_id` - Which doctor
- `visit_id` - Which visit
- `status` - in_progress, completed, etc.
- `created_at` - When consultation started
- `updated_at` - Last update

### radiology_test_orders
- `patient_id` - Which patient
- `test_type` - What test ordered
- `status` - ordered, completed, etc.
- `ordered_by` - Which doctor ordered
- `created_at` - When ordered

### radiology_results
- `order_id` - Links to test order
- `patient_id` - Which patient
- `findings` - What was found
- `impression` - Doctor's interpretation
- `recommendations` - What to do next
- `completed_at` - When completed

### patient_visits
- `patient_id` - Which patient
- `visit_date` - Date of visit
- `status` - active, completed, etc.
- `payment_status` - paid, pending, etc.

---

## Key Principles

### 1. Single Payment Point
- Patient pays ONCE when entering system
- All initial consultations covered by this payment
- Follow-up services may have additional fees

### 2. Service Provider Responsibility
- Lab Tech checks payment before starting lab test
- Radiologist checks payment before starting radiology
- Ward staff checks payment before admitting
- **Doctor does NOT check** - they're ordering services

### 3. Doctor Role
- Doctor makes clinical decisions
- Doctor allocates services
- Doctor routes patient to appropriate department
- Doctor is NOT responsible for payment collection

### 4. Service Fee Tracking
- Each service tracks its own fees
- Ward charges per day
- Lab charges per test
- Radiology charges per procedure
- All tracked separately from consultation fee

---

## URL Parameter Usage

### Without ?action= (Initial Consultation)
```
/doctor/attend_patient/44
├─ Payment CHECK required
├─ Full form shown
├─ Doctor reviewing fresh patient
└─ May redirect to payment_required page
```

### With ?action= (Follow-up Decision)
```
/doctor/attend_patient/44?action=lab
├─ Payment check BYPASSED
├─ Specific section auto-selected
├─ Doctor allocating service
├─ Smooth navigation to selected section
```

---

## Technical Fix Applied

**Problem:**
- Line 1684 used deprecated `FILTER_SANITIZE_STRING`
- Caused page to fail and show deprecation warning
- Page went blank when accessing with ?action= parameter

**Solution:**
```php
// OLD (deprecated):
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

// NEW (correct):
$action = isset($_GET['action']) ? trim(htmlspecialchars($_GET['action'], ENT_QUOTES, 'UTF-8')) : null;
```

**Benefits:**
- ✅ No deprecation warnings
- ✅ Page loads properly
- ✅ Action parameter processed correctly
- ✅ XSS protection maintained
- ✅ Compatible with PHP 8.1+

---

## Testing the Complete Flow

### Test 1: Initial Doctor Consultation (with payment check)
```
1. Doctor clicks patient from dashboard
2. URL: /doctor/attend_patient/44
3. ✅ Payment check occurs
4. If unpaid → redirected to payment_required page
5. If paid → Can proceed with consultation
```

### Test 2: From Radiology Results (payment bypassed)
```
1. Doctor views radiology results
2. Clicks "Send to Ward" button
3. URL: /doctor/attend_patient/44?action=ipd
4. ✅ NO payment check (action parameter present)
5. IPD section auto-selected
6. Doctor fills ward and reason
7. Consultation completes successfully
```

### Test 3: From Lab Results (payment bypassed)
```
1. Doctor views lab results
2. Clicks "Send to Lab" button
3. URL: /doctor/attend_patient/44?action=lab
4. ✅ NO payment check
5. Lab section auto-selected
6. Doctor selects new lab tests
7. Consultation completes successfully
```

---

## Summary

✅ **Payment is checked ONCE** when patient enters (at Accountant)  
✅ **Doctor follows-up decisions** do NOT require payment re-check  
✅ **Service providers** verify payment before providing service  
✅ **Each service** tracks its own fees separately  
✅ **Patient journey** is seamless and tracked end-to-end  

**Result:** 
- Patients don't get blocked by payment multiple times
- Doctors can make clinical decisions freely
- All services are properly tracked
- System is audit-ready and compliant

---

**Status:** Implementation Complete ✅  
**Date:** January 27, 2026  
**Fix Applied:** Deprecated constant removed, page now loads correctly
