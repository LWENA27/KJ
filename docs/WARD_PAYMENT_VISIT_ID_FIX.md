# Fixed: Missing visit_id in Ward Payment Query

## Problem

When attempting to record a payment for a ward admission, the form submission failed with:

```
SQLSTATE[HY000]: General error: 1366
Incorrect integer value: 'undefined' for column 'visit_id' at row 1
```

## Root Cause

The ward (IPD admission) payment query in `AccountantController.php` was missing the `ia.visit_id` field in the SELECT clause. 

When the JavaScript `openPaymentModal()` function received the data object from the server, it couldn't find `visit_id` in the data array. The form then submitted with `visit_id='undefined'` (as a string), which MySQL rejected as an invalid integer value.

## Solution

**File:** `/controllers/AccountantController.php` - Lines 267-290

**Added:** `ia.visit_id` to the SELECT clause

```sql
-- BEFORE:
SELECT 
    ia.id as admission_id,
    ia.admission_number,
    ia.patient_id,
    ...

-- AFTER:
SELECT 
    ia.id as admission_id,
    ia.admission_number,
    ia.visit_id,        ← ADDED
    ia.patient_id,
    ...
```

## Related Updates

Also updated the test script `/tools/test_payment_queries.php` to include `ia.visit_id` in its ward query for consistency.

## Verification

✅ Ward query now returns all required fields:
```
Patient: kuku maji (KJ20260024)
  Admission ID: 1 (ADM-20260126-9aed2df9)
  Visit ID: 78                          ← Now available!
  Ward: General Ward A
  Amount: 15000.00, Paid: 0.00, Remaining: 15000.00
```

✅ Form can now properly submit ward payments with valid visit_id

---

**Status:** ✅ Fixed
**Date:** January 27, 2026
**Impact:** Ward payment recording is now functional
