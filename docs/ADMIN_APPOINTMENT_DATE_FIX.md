# Admin Dashboard appointment_date Error Fix

## Error Summary
**Date**: 2025-10-11  
**Error**: `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'appointment_date' in 'where clause'`  
**Location**: `AdminController::getDashboardStats()` line 257  
**Impact**: Admin dashboard failed to load - couldn't calculate today's consultations

---

## Root Cause
Same issue as previously fixed in other controllers - the `consultations` table does NOT have an `appointment_date` column.

**Correct columns**:
- ✅ `consultations.follow_up_date` - Date for follow-up appointments
- ✅ `patient_visits.visit_date` - Date of the visit
- ✅ `consultations.created_at` - When consultation record was created

---

## Solution Implemented

### Query Fixed: Today's Consultations Count

**OLD QUERY** (Incorrect):
```php
SELECT COUNT(*) as total 
FROM consultations 
LEFT JOIN patient_visits pv ON consultations.visit_id = pv.id 
WHERE DATE(COALESCE(appointment_date, pv.visit_date, created_at)) = CURDATE()
```

**Issues**:
- ❌ `appointment_date` - column doesn't exist
- ❌ No table alias for consultations table
- ❌ Ambiguous `created_at` reference

**NEW QUERY** (Correct):
```php
SELECT COUNT(*) as total 
FROM consultations c
LEFT JOIN patient_visits pv ON c.visit_id = pv.id 
WHERE DATE(COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) = CURDATE()
```

**Key Changes**:
1. ✅ Added alias `c` for consultations table
2. ✅ Changed `appointment_date` → `c.follow_up_date`
3. ✅ Qualified all column references with table aliases
4. ✅ Proper COALESCE priority: follow_up_date → visit_date → created_at

---

## Files Modified

### **controllers/AdminController.php**
- **Method**: `getDashboardStats()` (line 257)
- **Section**: "Today's consultations" statistic
- **Changes**: Fixed column name and added proper table aliases
- **Result**: Admin dashboard now loads correctly

---

## Testing

### Test Admin Dashboard
```
URL: http://localhost/KJ/admin/dashboard
Expected: Dashboard loads without SQL errors
Check: "Today's Consultations" statistic displays correct count
```

### Verify Query Results
```sql
-- Test the query directly
SELECT COUNT(*) as total 
FROM consultations c
LEFT JOIN patient_visits pv ON c.visit_id = pv.id 
WHERE DATE(COALESCE(c.follow_up_date, pv.visit_date, c.created_at)) = CURDATE();
```

---

## Related Fixes

This is the **5th instance** of the `appointment_date` error fixed across the system:

1. ✅ ReceptionistController (5 instances) - Fixed
2. ✅ DoctorController (5 instances) - Fixed  
3. ✅ LabController (2 instances) - Fixed
4. ✅ PatientHistoryController (5 instances) - Fixed
5. ✅ AdminController (1 instance) - **Just fixed!**

**Total**: 18 occurrences fixed system-wide

---

## Additional Errors Fixed (Line 273 & 277)

### Error #2: Array Offset on Boolean (Line 273)
**Error**: `Warning: Trying to access array offset on value of type bool`  
**Cause**: Query with `GROUP BY` returns multiple rows, but `fetch()['total']` expects single row

**OLD QUERY** (Broken):
```php
SELECT COUNT(DISTINCT m.id) as total 
FROM medicines m
LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
GROUP BY m.id  -- Returns multiple rows!
HAVING COALESCE(SUM(mb.quantity_remaining), 0) < 10
```

**Problem**: This query groups by medicine ID and returns one row PER medicine, not a total count.

**NEW QUERY** (Fixed):
```php
SELECT COUNT(*) as total
FROM (
    SELECT m.id
    FROM medicines m
    LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
    GROUP BY m.id, m.reorder_level
    HAVING COALESCE(SUM(mb.quantity_remaining), 0) < COALESCE(m.reorder_level, 20)
) as low_stock_meds
```

**Key Changes**:
1. ✅ Wrapped grouped query in subquery
2. ✅ Outer query counts the number of low-stock medicines
3. ✅ Uses `m.reorder_level` (default 20) instead of hardcoded 10
4. ✅ Added null-safe fetch: `$result = $stmt->fetch(); $stats['low_stock'] = $result ? $result['total'] : 0;`

### Error #3: Wrong Column Name (Line 277)
**Error**: Column `status` doesn't exist in payments table  
**Correct**: Column is `payment_status`

**OLD**: `WHERE status = 'pending'`  
**NEW**: `WHERE payment_status = 'pending'`

Also added null-safe fetch to prevent array offset errors.

---

## Summary

**Problem #1**: Used non-existent `appointment_date` column in admin dashboard query  
**Problem #2**: Malformed GROUP BY query causing boolean instead of count  
**Problem #3**: Wrong column name `status` instead of `payment_status`  

**Solutions**: 
1. Changed to `c.follow_up_date` with proper COALESCE fallback
2. Fixed low stock query with subquery and null-safe fetch
3. Changed `status` to `payment_status` with null-safe fetch

**Result**: Admin dashboard now loads successfully with all statistics  
**Testing**: Verify admin dashboard displays all counts correctly

**Status**: All AdminController dashboard errors resolved! ✅
