# Service Payment Duplication Fix

## Issue Description
Payment data was appearing in both the **Pending Service Payments** section and the **Payment History (Paid)** section simultaneously, causing confusion about which services needed payment.

## Root Cause Analysis

### The Problem
1. When a doctor allocated a service to a patient, the system was **automatically creating a pending payment record** in the `payments` table
2. When the receptionist recorded the actual payment, a **new paid payment record** was created
3. This caused:
   - Pending payments to appear in the payment history
   - Services to show as both paid and pending
   - Confusion about payment status

### Code Issues Found

#### 1. Duplicate Payment Insertion (Fixed in ReceptionistController.php)
The `record_payment()` method was inserting payment records **twice** for service payments:
- Once in the general payment insertion (lines 785-801)
- Again in the service-specific section (lines 844-870)

#### 2. Automatic Pending Payment Creation (Fixed in DoctorController.php)
When doctors allocated services, the system was creating pending payment records:
- Line 707-731 in the consultation completion method
- Line 1599-1625 in the service allocation method

**This was fundamentally wrong** because:
- Payment records should only exist when payment is actually received
- The `service_orders` table should be the source of truth for services that need payment
- Pending payments in the payments table created data integrity issues

## Changes Made

### 1. DoctorController.php
**File**: `/var/www/html/KJ/controllers/DoctorController.php`

#### Change 1: Removed pending payment creation in consultation completion (Line ~707)
```php
// REMOVED: Code that created pending payments when services were allocated
// Now: Service orders are created without corresponding payment records
```

#### Change 2: Removed pending payment creation in service allocation (Line ~1599)
```php
// REMOVED: Code that created pending payments during service allocation
// Now: Service orders are the source of truth for unpaid services
```

### 2. ReceptionistController.php
**File**: `/var/www/html/KJ/controllers/ReceptionistController.php`

#### Change: Removed duplicate payment insertion for services (Line ~808)
```php
// REMOVED: Duplicate INSERT INTO payments for service payments
// Now: Payment is inserted only once with proper item_id and item_type
```

### 3. Database Cleanup
**Script**: `/var/www/html/KJ/tmp/cleanup_pending_service_payments.php`

Removed 4 existing pending service payment records that were incorrectly created:
- El Becerril (KJ20250027): Tsh 5,000
- John Magufuri (KJ20250026): Tsh 5,000
- Lwena Samson (KJ20250001): Tsh 5,000
- Ester Lupolo (KJ20250023): Tsh 1,000

## How It Works Now

### Service Allocation Flow
1. **Doctor allocates service** → `service_orders` table entry created (status: 'pending')
2. **No payment record created yet**
3. Service appears in **Pending Service Payments** section (based on `service_orders` with `paid_count = 0`)

### Payment Recording Flow
1. **Receptionist records payment** → Single payment record created with:
   - `payment_type = 'service'`
   - `item_type = 'service_order'`
   - `item_id = <service_order_id>`
   - `payment_status = 'paid'`
2. Payment appears in **Payment History** section
3. Service disappears from **Pending Service Payments** (because `paid_count > 0`)

## Data Integrity

### Source of Truth
- **`service_orders` table**: Which services have been ordered
- **`payments` table**: Which services have been paid for
- **Query logic**: JOIN service_orders with payments to determine pending services

### Query Logic (Correct)
```sql
SELECT ...
FROM service_orders so
LEFT JOIN payments ON so.id = payments.item_id 
    AND payments.item_type = 'service_order'
    AND payments.payment_status = 'paid'
HAVING paid_count = 0  -- Shows only unpaid services
```

## Testing Performed

### 1. Verified No Duplicates
- Ran `fix_duplicate_service_payments.php` - confirmed no duplicate payment records
- Checked database directly - no service has multiple payment records

### 2. Verified Pending Payments Removed
- Ran `cleanup_pending_service_payments.php`
- Removed 4 pending service payment records
- Verified service orders remain intact

### 3. Data Verification
- Checked all 4 affected patients
- Confirmed only paid payments exist in payments table
- Confirmed service orders exist for unpaid services
- Confirmed `paid_count` correctly reflects payment status

## Expected Behavior After Fix

### Pending Service Payments Page
- **Should show**: Services from `service_orders` table that have NOT been paid (`paid_count = 0`)
- **Should NOT show**: Services that have payment records with `payment_status = 'paid'`

### Payment History Page
- **Should show**: ONLY payment records with `payment_status = 'paid'`
- **Should NOT show**: Pending payments (because they no longer exist)

### Service Payment Flow
1. Doctor allocates service → Appears in Pending Service Payments
2. Receptionist records payment → Appears in Payment History
3. Service automatically disappears from Pending Service Payments

## Files Modified
1. `/var/www/html/KJ/controllers/DoctorController.php` - Removed pending payment creation
2. `/var/www/html/KJ/controllers/ReceptionistController.php` - Removed duplicate payment insertion

## Utility Scripts Created
1. `/var/www/html/KJ/tmp/fix_duplicate_service_payments.php` - Check for duplicate payments
2. `/var/www/html/KJ/tmp/cleanup_pending_service_payments.php` - Remove pending payments
3. `/var/www/html/KJ/tmp/check_payment_data.php` - Verify payment data

## Impact
- ✅ Fixed data duplication issue
- ✅ Improved data integrity
- ✅ Clearer separation of concerns (orders vs payments)
- ✅ No more pending payment records in database
- ✅ Accurate payment tracking

## Future Considerations
- Service orders should be marked as 'paid' or have a payment_status field for additional clarity
- Consider adding database constraints to prevent duplicate payment records
- Add automated tests to prevent regression of this issue

---
**Fixed by**: GitHub Copilot
**Date**: November 10, 2025
**Issue**: Service payments appearing in both pending and paid sections
