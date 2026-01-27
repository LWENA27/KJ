# Fixed: SQL Error in Ward Payments Query

## Problem

When accessing the Accountant Payments page, a SQL error occurred:

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'w.daily_rate' in 'field list'
```

This prevented the page from loading entirely.

## Root Cause

In the ward (IPD admission) payment query, the code referenced `w.daily_rate`:
```sql
w.daily_rate * DATEDIFF(...) as amount,
...
(w.daily_rate * DATEDIFF(...) - ...) as remaining_amount_to_pay
```

But `daily_rate` is in the `ipd_beds` table (aliased as `b`), NOT in the `ipd_wards` table (aliased as `w`).

## Solution

**File:** `/controllers/AccountantController.php` - Lines 268-290

**Changed:**
```sql
-- BEFORE (WRONG):
w.daily_rate * DATEDIFF(...) as amount,
(w.daily_rate * DATEDIFF(...) - ...) as remaining_amount_to_pay

-- AFTER (CORRECT):
b.daily_rate * DATEDIFF(...) as amount,
(b.daily_rate * DATEDIFF(...) - ...) as remaining_amount_to_pay
```

## Verification

✅ Query now executes without errors  
✅ Ward admissions display correctly with calculated amounts  
✅ Page loads and displays all payment tabs  

**Test Results:**
```
Ward admissions found: 2

Patient: kuku maji (KJ20260024)
  Admission ID: 1 (ADM-20260126-9aed2df9)
  Ward: General Ward A
  Amount: 15000.00, Paid: 0.00, Remaining: 15000.00
  
Patient: home mbinga (KJ20260034)
  Admission ID: 2 (ADM-20260126-8325ff30)
  Ward: General Ward A
  Amount: 15000.00, Paid: 0.00, Remaining: 15000.00
```

## Complete Payment Query Chain

All payment queries now work correctly:

| Type | Status | Count |
|------|--------|-------|
| Consultation | Pending | 0 |
| Lab Tests | Pending | 3 |
| Medicines | Pending | 3 |
| Services | Pending | 1 |
| Radiology | Pending | 1 ✓ |
| Ward | Pending | 2 ✓ |

---

**Status:** ✅ Fixed
**Date:** January 27, 2026
**Impact:** High - Page was completely inaccessible
**Resolution:** Changed table alias reference from `w` to `b` for `daily_rate` column
