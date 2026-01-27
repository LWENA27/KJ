# Complete Ward Payment Fix Summary

## Issues Fixed

### Issue 1: SQL Error - Wrong Table Alias ✅ FIXED
**File:** `/controllers/AccountantController.php`
**Error:** `Unknown column 'w.daily_rate'`
**Fix:** Changed `w.daily_rate` → `b.daily_rate` (daily_rate is in ipd_beds table, not ipd_wards)

### Issue 2: Missing visit_id Field ✅ FIXED
**File:** `/controllers/AccountantController.php`
**Error:** `Incorrect integer value: 'undefined' for column 'visit_id'`
**Fix:** Added `ia.visit_id` to the SELECT clause in ward payment query

## Complete Ward Payment Query

```sql
SELECT 
    ia.id as admission_id,
    ia.admission_number,
    ia.visit_id,                                  -- ✅ ADDED (required by form)
    ia.patient_id,
    p.first_name,
    p.last_name,
    p.registration_number,
    ia.admission_datetime as admission_date,
    w.ward_name,                                  -- ✅ CORRECT (from ipd_wards)
    b.daily_rate * DATEDIFF(...) as amount,      -- ✅ FIXED (was w.daily_rate)
    COALESCE(pay.amount, 0) as paid_amount,
    (b.daily_rate * DATEDIFF(...) - ...) as remaining_amount_to_pay
FROM ipd_admissions ia
JOIN patients p ON ia.patient_id = p.id
LEFT JOIN ipd_beds b ON ia.bed_id = b.id        -- ✅ Contains daily_rate
LEFT JOIN ipd_wards w ON b.ward_id = w.id       -- ✅ Contains ward_name
LEFT JOIN payments pay ON pay.item_type = 'service_order' 
    AND pay.item_id = ia.id 
    AND pay.payment_status = 'paid'
WHERE ia.status = 'active'
AND (pay.id IS NULL OR pay.payment_status = 'pending')
ORDER BY ia.admission_datetime DESC
```

## JavaScript Form Population

When user clicks "Collect" for a ward admission, the modal receives:
```javascript
openPaymentModal('ward', {
    admission_id: 1,                    // ✅ Used as modal_item_id
    visit_id: 78,                       // ✅ Used as modal_visit_id
    patient_id: 24,                     // ✅ Used as modal_patient_id
    first_name: "kuku",
    last_name: "maji",
    ward_name: "General Ward A",        // ✅ Displayed in modal
    remaining_amount_to_pay: 15000,     // ✅ Used as modal_amount
    ...
})
```

Form fields populated:
```javascript
document.getElementById('modal_patient_id').value = 24;
document.getElementById('modal_visit_id').value = 78;
document.getElementById('modal_payment_type').value = 'service';
document.getElementById('modal_item_id').value = 1;
document.getElementById('modal_item_type').value = 'service_order';
document.getElementById('modal_amount').value = 15000;
```

Form submitted with:
```php
POST /accountant/record_payment
  patient_id: 24
  visit_id: 78
  payment_type: 'service'
  item_id: 1 (admission ID)
  item_type: 'service_order'
  amount: 15000
  payment_method: 'cash'
  reference_number: 'PAY-xxx'
```

## Database Changes

Payment recorded in `payments` table:
```sql
INSERT INTO payments 
(visit_id, patient_id, payment_type, item_id, item_type, amount, 
 payment_method, payment_status, reference_number, collected_by, payment_date)
VALUES
(78, 24, 'service', 1, 'service_order', 15000,
 'cash', 'paid', 'PAY-xxx', 13, NOW())
```

## Testing Results

✅ Ward query executes without errors
✅ Ward admissions display with all required fields
✅ Form submission succeeds with valid visit_id (integer, not 'undefined')
✅ Payment recorded in database with correct values
✅ Pending ward admissions list updates on next page load

## Files Modified

| File | Change | Lines |
|------|--------|-------|
| `/controllers/AccountantController.php` | Added `ia.visit_id` to SELECT | 270 |
| `/controllers/AccountantController.php` | Changed `w.daily_rate` to `b.daily_rate` | 277-278 |
| `/tools/test_payment_queries.php` | Added `ia.visit_id` to test query | 57 |
| `/tools/test_payment_queries.php` | Added visit_id display in test output | 88 |

## Verification Commands

```bash
# Test the ward payment query
php tools/test_payment_queries.php

# Check if payment is recorded correctly
php -r "require 'config/database.php'; \$s=\$pdo->query(\"SELECT * FROM payments WHERE item_type='service_order' LIMIT 1\"); print_r(\$s->fetch());"
```

---

**Status:** ✅ Complete
**Date:** January 27, 2026
**Severity:** High (payment recording was broken)
**Impact:** Ward payment collection now fully functional
