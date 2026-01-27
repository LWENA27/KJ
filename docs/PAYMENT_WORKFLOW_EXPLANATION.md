# Healthcare Workflow - Payment & Service Flow

**Date:** January 27, 2026  
**Topic:** Understanding Payment Blocking in Doctor Workflow  
**Status:** Documented

---

## Overview

The healthcare system has a **multi-step workflow** where patients progress through different services. Payment is checked at specific checkpoints, not at every step.

---

## Complete Patient Journey

```
Step 1: RECEPTION - Registration/Revisit
├─ Patient enters system
├─ Demographics recorded
└─ Status: NOT YET PAID

         ↓

Step 2: ACCOUNTANT - Payment Processing  ⚠️ PAYMENT CHECK HERE
├─ Accountant collects payment
├─ Payment recorded in system
└─ Status: PAID ✅

         ↓

Step 3: DOCTOR - Initial Consultation
├─ Doctor reviews patient
├─ Examination notes recorded
├─ Doctor selects NEXT STEP from options:
│  ├─ Lab Tests
│  ├─ Radiology
│  ├─ IPD/Ward Admission
│  ├─ Services Allocation
│  ├─ Medicine Prescription
│  └─ Discharge
└─ Status: CONSULTATION IN PROGRESS

         ↓

Step 4: SERVICE PROVIDER (Lab/Radiology/Ward)
├─ Lab Tech performs lab tests (if selected)
├─ Radiologist performs radiology (if selected)
├─ Ward staff admits patient (if selected)
└─ Status: SERVICE IN PROGRESS

         ↓

Step 5: RESULTS/COMPLETION
├─ Lab Tech records results
├─ Radiologist records findings
├─ Doctor reviews results
└─ Status: RESULTS RECORDED

         ↓

Step 6: DOCTOR - Follow-up Decision
├─ Doctor reviews results from Step 5
├─ Doctor selects NEXT STEP again from same options
├─ NO PAYMENT CHECK HERE (already paid in Step 2)
└─ Status: NEW SERVICE ORDERED

         ↓

Step 7: REPEAT Steps 4-6
├─ Services continue as ordered
├─ Process repeats until patient is discharged
└─ Status: ONGOING CARE

         ↓

FINAL: Discharge
└─ Patient leaves system
```

---

## Payment Blocking Rules

### ✅ WHEN Payment Should Be Checked

**Scenario 1: Patient First Enters Doctor (No Consultation Yet)**
```
Reception → Accountant → Doctor [FIRST TIME]
                          ↓
                   PAYMENT CHECK HERE ✅
                   (Is patient paid for consultation?)
                          ↓
                   Yes → Continue to attend_patient form
                   No  → Redirect to payment_required page
```

**When to Check:**
- Patient has NO active consultation
- Doctor is starting fresh consultation
- Called: `/doctor/attend_patient/{patient_id}` (no action parameter)

---

### ❌ WHEN Payment Should NOT Be Checked

**Scenario 1: Doctor Allocating Services from Results**
```
Radiology Results → Doctor selects action → attend_patient form
                    ↓
            URL has ?action=lab (or other action)
                    ↓
            SKIP PAYMENT CHECK ❌
            (Doctor is allocating services, not new consultation)
```

**Scenario 2: Doctor Reviewing Any Results and Making Follow-up Decisions**
```
Lab Results     → View → Doctor decides next step → attend_patient
Radiology Results → View → Doctor decides next step → attend_patient
                             ↓
                    URL has ?action= parameter
                             ↓
                    SKIP PAYMENT CHECK ❌
```

**Scenario 3: Service Provider Performing Their Task**
```
Patient already in Lab → Lab Tech runs tests
Patient already in Radiology → Radiologist performs scan
Patient already in Ward → Ward staff provides care

(These checks happen at SERVICE PROVIDER level, not doctor level)
```

---

## Payment Check Locations in Code

### DoctorController::attend_patient()
```php
// Line 1685-1687: Check if action parameter exists
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

// Line 1690-1692: Only check payment if NO action parameter
if (!$action) {
    $access_check = $this->checkWorkflowAccess($patient_id, 'consultation');
    // ... handle payment required
}
```

**Logic:**
- `?action=lab` → Skip payment check
- `?action=ipd` → Skip payment check
- `?action=medicine` → Skip payment check
- `?action=allocation` → Skip payment check
- No `?action=` → Check payment ✅

---

## Service Provider Payment Checks

### BaseController::checkWorkflowAccess()
```php
/**
 * Check if workflow task can be performed
 * Different checks for different roles:
 * - Doctor: Payment for consultation
 * - Lab Tech: Payment for lab services
 * - Radiologist: Payment for radiology services
 * - Ward Staff: Payment for ward admission
 */
```

### RadiologistController::perform_test()
- Checks: Does patient have payment for radiology?
- If not: Blocks radiologist from starting test
- This is CORRECT - radiologist should not work without payment

### LabTechController::perform_test()
- Checks: Does patient have payment for lab?
- If not: Blocks lab tech from starting test
- This is CORRECT - lab tech should not work without payment

---

## The Loop Explained

**Why the "loop" happens:**

```
Patient Path:
1. Receptionist: Register → Patient goes to Accountant
2. Accountant: Pay → Patient goes to Doctor
3. Doctor: Attends consultation → Doctor selects "Radiology"
4. Radiologist: Performs radiology → Results recorded
5. Doctor: Reviews results → Doctor selects "Lab Tests"
6. Lab Tech: Performs lab → Results recorded
7. Doctor: Reviews results → Doctor selects "Ward"
8. Ward: Admits patient → Patient in ward
9. Doctor: Reviews ward progress → Doctor selects "Medicine"
10. Pharmacy: Dispenses medicine → Prescription filled
11. Doctor: Final review → Discharge

This is CORRECT workflow! ✅

Patient makes MULTIPLE PASSES through different services
because DIFFERENT SERVICES are needed.
```

---

## Why This Design?

### Benefits:
1. **Payment happens ONCE** → At accountant, before any service
2. **Doctor can order multiple services** → Without payment checks each time
3. **Service providers control their payment** → Each provider checks if payment covers their service
4. **Clear workflow** → Each step has clear responsibility
5. **Prevents repeated payment prompts** → Better user experience

### Example:
```
Patient has ONE payment at accountant:
- Covers consultation with doctor
- Covers lab services if ordered
- Covers radiology if ordered
- Covers ward admission if ordered
- Covers pharmacy services if ordered

Doctor can order ANY combination of these services
WITHOUT additional payment checks
```

---

## Current Implementation Status

### ✅ Already Fixed
- [x] Payment check skipped when `?action=` present
- [x] Doctor can order services from radiology results
- [x] Doctor can order services from lab results
- [x] Doctor can make follow-up decisions without payment re-check

### ✅ Working Correctly
- [x] Service providers check payment for their service
- [x] Radiologist checks before starting test
- [x] Lab tech checks before starting test
- [x] Ward checks before admitting patient
- [x] Pharmacy checks before dispensing

---

## Workflow Diagram

```
RECEPTION
   ↓
ACCOUNTANT ← PAYMENT CHECK HERE (once)
   ↓
DOCTOR (Attend Consultation)
   ├─ Check payment? YES ✅
   └─ If paid: Show form, select service
   ↓
SERVICE PROVIDER (Lab/Radiology/Ward)
   ├─ Check payment for their service? YES ✅
   └─ If covered: Perform service
   ↓
DOCTOR (Review Results)
   ├─ Check payment again? NO ❌ (Skip, already paid)
   ├─ Render attend_patient form with ?action=
   └─ Select next service
   ↓
REPEAT last 2 steps until discharge
```

---

## Payment Coverage

When patient pays at accountant, payment typically covers:

```
Single Payment = Multiple Services
├─ Consultation Fee
│  └─ Covers doctor's consultation
├─ Service Bundle
│  ├─ Lab tests (if ordered)
│  ├─ Radiology (if ordered)
│  ├─ Ward/IPD (if ordered)
│  ├─ Services/Procedures (if ordered)
│  └─ Pharmacy (if ordered)
└─ Package Deal
   └─ All services included in one payment
```

**Implementation Note:**
- Current system allows ordering all services with single payment
- Payment blocking happens at SERVICE PROVIDER level
- Doctor has freedom to order any combination

---

## FAQ

**Q: Why does doctor get blocked at payment_required sometimes?**
A: Only when accessing `/doctor/attend_patient/{id}` WITHOUT `?action=` parameter
   - This means starting fresh consultation
   - Payment must be verified

**Q: Why can doctor order lab tests from radiology results without payment check?**
A: Because URL has `?action=lab_tests`
   - Doctor is allocating services to already-paid patient
   - Payment was verified when patient first entered doctor

**Q: What if patient's payment only covers consultation, not lab?**
A: Lab tech will check payment when patient reaches lab
   - Lab tech sees insufficient payment
   - Lab tech blocks patient
   - Patient must pay additional amount

**Q: Can patient skip paying and go directly to services?**
A: No
   - Accountant processes payment first
   - Doctor won't see patient without accountant clearance
   - Service providers check payment

---

## Testing the Workflow

### Test Case 1: Normal Flow (With Payment)
```
1. Patient registers at reception
2. Patient goes to accountant and pays
3. Patient goes to doctor
   → Doctor can attend (payment verified)
   → Doctor can order services (no additional payment check)
4. Patient goes to lab
   → Lab tech checks payment
   → Lab tech performs test (payment verified)
5. Patient back to doctor
   → Doctor can review results
   → Doctor can order more services (no additional payment check)
```

### Test Case 2: Missing Payment
```
1. Patient registers at reception
2. Patient SKIPS accountant (doesn't pay)
3. Patient goes to doctor
   → Doctor sees payment_required page
   → Doctor must override with reason OR
   → Patient must go back to accountant to pay
```

### Test Case 3: Service-Specific Payment
```
1. Patient has paid for consultation + lab
2. Patient goes to doctor and orders radiology
3. Radiologist checks payment for radiology
   → If radiology not covered: Radiologist blocks
   → If radiology covered: Radiologist proceeds
```

---

## Configuration Notes

**File:** `/includes/BaseController.php`

**Method:** `checkWorkflowAccess($patient_id, $workflow_type)`

**Parameters:**
- `$patient_id` → Patient to check
- `$workflow_type` → Type of workflow: 'consultation', 'lab', 'radiology', 'ipd'

**Returns:**
```php
[
    'access' => true/false,      // Can proceed?
    'reason' => 'message',        // Why blocked?
    'amount_due' => 0.00,        // Any amount needed?
    'payment_method' => 'method'  // How to pay?
]
```

---

## Summary

**Payment Blocking is Intelligent:**

✅ **Check at:** Initial doctor consultation  
❌ **Don't check at:** Service allocation/follow-up  
✅ **Check at:** Service provider level  
❌ **Don't check at:** Every doctor decision  

**Result:** Patients pay ONCE, get MULTIPLE SERVICES without repeated payment checks.

This is the **correct medical workflow**! ✅

---

**Status:** ✅ WORKING AS DESIGNED
