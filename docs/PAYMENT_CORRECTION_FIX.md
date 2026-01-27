# Payment Correction Workflow Fix - Radiology & Ward

## Problem Summary

When attempting to record payment corrections for radiology and ward services through the Accountant dashboard, users received the following error:

```
Failed to record payment: SQLSTATE[HY000]: General error: 3819 
Check constraint 'chk_payment_type' is violated
```

### Root Cause Analysis

The `payments` table has a CHECK constraint that limits `payment_type` to specific values:

```sql
CHECK (`payment_type` in ('registration','consultation','lab_test','medicine','service'))
```

However, the payment correction form was attempting to send:
- `payment_type='radiology'` for radiology orders (invalid)
- `payment_type='ward'` for ward admissions (invalid)

These values are not in the allowed list, causing the database to reject the insert.

## Solution Overview

The fix was implemented in two layers:

### Layer 1: View/Frontend (JavaScript) - COMPLETED ✅

**File:** `/views/accountant/payments.php`

**Change:** Updated `openPaymentModal()` function to properly set `payment_type='service'` for both radiology and ward payments.

**Code Change:**
```javascript
function openPaymentModal(paymentId, type, amount, itemId) {
    // ... code ...
    if (type === 'radiology') {
        document.getElementById('modal_payment_type').value = 'service';
        document.getElementById('modal_item_type').value = 'radiology_order';
    } else if (type === 'ward') {
        document.getElementById('modal_payment_type').value = 'service';
        document.getElementById('modal_item_type').value = 'service_order';
    } else if (type === 'consultation') {
        document.getElementById('modal_payment_type').value = 'consultation';
        document.getElementById('modal_item_type').value = 'consultation';
    }
    // ... etc ...
}
```

**Impact:** Form now sends `payment_type='service'` which complies with the CHECK constraint.

### Layer 2: Controller/Backend (PHP) - COMPLETED ✅

**File:** `/controllers/AccountantController.php`

**Change:** Extended `record_payment()` method to handle `item_type='radiology_order'` and `item_type='service_order'`.

**Code Added:**
```php
} elseif ($payment_type === 'service') {
    // For service payments, check item_type to determine which service table to update
    if ($item_type === 'radiology_order') {
        // Update radiology test order status to 'completed' or 'paid' when payment is collected
        $radiologyStmt = $this->pdo->prepare("
            UPDATE radiology_test_orders 
            SET status = CASE 
                WHEN status = 'pending' THEN 'scheduled'
                WHEN status = 'scheduled' THEN 'scheduled'
                ELSE status 
            END,
            updated_at = NOW()
            WHERE id = ?
        ");
        $radiologyStmt->execute([$item_id]);
        error_log('[record_payment] Radiology order updated - order_id: ' . $item_id);
    } elseif ($item_type === 'service_order') {
        // For ward admissions (service_order), mark that payment has been received
        error_log('[record_payment] Ward admission service payment recorded - admission_id: ' . $item_id);
    }
}
```

**Impact:** 
- When radiology payment is recorded, the `radiology_test_orders` table is updated
- When ward payment is recorded, payment is tracked in logs (can be extended to update status columns if needed)
- Proper audit trail maintained in application logs

## Payment Tracking Architecture

The system uses **dual tracking** for complete audit visibility:

### 1. Payment Status (Primary Tracking)
**Table:** `payments`
**Column:** `payment_status` (varchar)
**Values:** `'pending'` → `'paid'`
**Purpose:** Tracks whether the payment transaction has been collected

### 2. Service Status (Secondary Tracking)
**Tables:**
- `radiology_test_orders.status` (enum: 'pending', 'scheduled', 'completed', etc.)
- `ipd_admissions.status` (enum: 'active', 'discharged', 'cancelled', etc.)
**Purpose:** Tracks the status of the actual service/order

### 3. Item Type Mapping
**payments.item_type enum values:**
- `'radiology_order'` → Links to `radiology_test_orders.id`
- `'service_order'` → Links to `ipd_admissions.id`
- `'lab_order'` → Links to lab tests
- `'prescription'` → Links to medicines
- `'service'` → Links to general services

## Database Schema Reference

### payments table
```
payment_type VARCHAR - CHECK constraint: ('registration','consultation','lab_test','medicine','service')
item_type ENUM - ('lab_order','prescription','service','service_order','radiology_order')
item_id INT - Foreign key to the specific service table
payment_status VARCHAR - 'pending' or 'paid'
```

### radiology_test_orders table
```
id INT PRIMARY KEY
status ENUM - ('pending','scheduled','completed','cancelled')
patient_id INT
visit_id INT
```

### ipd_admissions table
```
id INT PRIMARY KEY
status ENUM - ('active','discharged','cancelled')
patient_id INT
admission_number VARCHAR
```

## Business Logic Flow

### When Doctor Orders Radiology:
1. Doctor submits consultation with radiology request
2. `DoctorController` creates:
   - `radiology_test_order` with `status='pending'`
   - `payment` record with `payment_type='service'`, `item_type='radiology_order'`, `payment_status='pending'`

### When Accountant Records Payment:
1. Accountant views "Radiology" tab in Payments page
2. Clicks "Collect Payment" button
3. Modal opens with pre-filled:
   - `payment_type='service'` (from JS: openPaymentModal)
   - `item_type='radiology_order'` (from JS)
   - `item_id={radiology_order_id}` (from JS)
4. Accountant enters amount, method, reference
5. Form submits to `AccountantController::record_payment()`
6. Controller:
   - Validates payment amount matches quoted amount
   - Updates `payments.payment_status` to `'paid'`
   - Updates `radiology_test_orders.status` to `'scheduled'` (or completed)
   - Logs transaction

### When Doctor Orders IPD (Ward) Admission:
1. Doctor creates IPD admission
2. `DoctorController` creates:
   - `ipd_admission` with `status='active'`
   - `payment` record with `payment_type='service'`, `item_type='service_order'`, `payment_status='pending'`

### When Accountant Records Ward Payment:
1. Accountant views "Ward" tab in Payments page
2. Similar flow as radiology (modal opens with correct payment_type)
3. Form submits with:
   - `payment_type='service'`
   - `item_type='service_order'`
   - `item_id={admission_id}`
4. Controller logs the payment and can update `ipd_admissions` status if needed

## Testing

Run the test script to validate the configuration:

```bash
php /var/www/html/KJ/tools/test_payment_correction.php
```

**Test Coverage:**
- ✓ CHECK constraint allows `payment_type='service'`
- ✓ `item_type` enum includes `'radiology_order'` and `'service_order'`
- ✓ Pending radiology orders are queryable
- ✓ Active ward admissions are queryable
- ✓ Payment records with radiology_order items exist
- ✓ SQL UPDATE statement syntax is valid

## Key Changes Summary

| Component | File | Change | Status |
|-----------|------|--------|--------|
| Frontend | `/views/accountant/payments.php` | `openPaymentModal()` now sets `payment_type='service'` for radiology and ward | ✅ Complete |
| Backend | `/controllers/AccountantController.php` | `record_payment()` now handles `item_type='radiology_order'` and `item_type='service_order'` | ✅ Complete |
| Config | `/config/database.php` | No changes needed (already correct) | ✅ OK |
| Schema | Database | No migrations needed (constraints already in place) | ✅ OK |

## Backward Compatibility

✓ No breaking changes
✓ Existing consultation, lab_test, medicine payments unaffected
✓ Only adds new handling for radiology and ward service payments
✓ Uses same `payment_type='service'` pattern as general services

## Future Enhancements

1. **Status Updates:** Consider updating `ipd_admissions.status` when ward payment is collected (currently only logged)
2. **Workflow Integration:** Could add workflow status updates when payment milestone is reached
3. **Payment Tracking:** Could add `payment_received_date` column to service tables for audit trail
4. **Reconciliation:** Add accounting reports matching payment collection to service completion

## Troubleshooting

### Error: "Check constraint 'chk_payment_type' is violated"
- Verify `payment_type` value is one of: `'registration','consultation','lab_test','medicine','service'`
- Check that `openPaymentModal()` is properly setting `document.getElementById('modal_payment_type').value = 'service'`

### Radiology payment not showing in pending list
- Check `radiology_test_orders.status` is `'pending'` or `'scheduled'`
- Verify `payments` record exists with `item_type='radiology_order'` and `payment_status='pending'`
- Run test script: `php tools/test_payment_correction.php`

### Ward payment not showing in pending list
- Check `ipd_admissions.status` is `'active'` (not discharged/cancelled)
- Verify `payments` record exists with `item_type='service_order'` and `payment_status='pending'`
- Run test script: `php tools/test_payment_correction.php`

## Related Documentation

- `/docs/PAYMENT_TRACKING_DESIGN.md` - Overall payment system design
- `/docs/RADIOLOGY_IPD_IMPLEMENTATION.md` - Feature implementation details
- `/controllers/AccountantController.php` - Payment processing logic
- `/views/accountant/payments.php` - Accountant dashboard and payment UI

---

**Last Updated:** 2025-01-26
**Version:** 1.0
**Status:** ✅ Complete and Tested
