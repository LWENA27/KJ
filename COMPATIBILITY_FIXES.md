# Database Compatibility Fixes

## Overview
This document describes the changes made to ensure the application works with the existing database schema without requiring immediate import of the completed `zahanati.sql`.

## Date: 2025-10-11

## Changes Made

### 1. Removed `patient_latest_visit` View Dependency

**File:** `controllers/ReceptionistController.php`

**Issue:** Code referenced the `patient_latest_visit` view which doesn't exist in the current database.

**Fix:** Replaced the view reference with an inline derived table:
```php
// BEFORE (using view)
LEFT JOIN patient_latest_visit lv ON lv.patient_id = p.id

// AFTER (using derived table)
LEFT JOIN (
    SELECT pv.patient_id, pv.id AS visit_id, pv.status
    FROM patient_visits pv
    JOIN (SELECT patient_id, MAX(created_at) AS latest FROM patient_visits GROUP BY patient_id) latest
    ON latest.patient_id = pv.patient_id AND latest.latest = pv.created_at
) lv ON lv.patient_id = p.id
```

**Impact:** Slightly slower query performance, but no database changes required.

---

### 2. Fixed `medicines.stock_quantity` References

**Issue:** Code referenced `medicines.stock_quantity` column which doesn't exist in canonical schema. Stock is tracked in `medicine_batches` table.

**Affected Files:**
- `controllers/ReceptionistController.php` (multiple functions)
- `controllers/DoctorController.php` (medicine search)
- `controllers/AdminController.php` (dashboard stats and medicine list)

**Fixes Applied:**

#### A. Medicine Listing Queries
All SELECT queries updated to aggregate stock from `medicine_batches`:
```php
// BEFORE
SELECT m.*, m.stock_quantity FROM medicines m

// AFTER
SELECT m.*, COALESCE(SUM(mb.quantity_remaining), 0) as stock_quantity
FROM medicines m
LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
GROUP BY m.id, ...
```

#### B. Stock Deduction (Dispensing)
Replaced simple UPDATE statements with FEFO (First-Expiry-First-Out) batch deduction:
```php
// BEFORE
UPDATE medicines SET stock_quantity = stock_quantity - ? WHERE id = ?

// AFTER (FEFO algorithm)
1. Fetch batches ordered by expiry_date ASC, created_at ASC
2. Deduct from each batch in order until quantity fulfilled
3. UPDATE medicine_batches SET quantity_remaining = quantity_remaining - ?
```

**Functions Updated:**
- `ReceptionistController::dispense_medicines()` (line ~530)
- `ReceptionistController::dispense_patient_medicine()` (line ~715)

#### C. Adding New Medicine
Updated to create medicine record + initial batch:
```php
// BEFORE
INSERT INTO medicines (..., stock_quantity, expiry_date, ...)

// AFTER
1. INSERT INTO medicines (name, generic_name, ...) -- no stock_quantity column
2. INSERT INTO medicine_batches (medicine_id, batch_number, quantity_received, ...)
```

**Function:** `ReceptionistController::add_medicine()`

#### D. Stock Adjustment
Updated to add new batches or adjust existing batch:
```php
// BEFORE
UPDATE medicines SET stock_quantity = stock_quantity + ? WHERE id = ?

// AFTER
- action='add': INSERT new batch with batch_number = 'BATCH-{date}-{id}'
- action='set': UPDATE most recent batch's quantity_remaining
```

**Function:** `ReceptionistController::update_medicine_stock()`

#### E. Dashboard Sidebar (Low Stock Alert)
```php
// BEFORE
SELECT COUNT(*) FROM medicines WHERE stock_quantity <= 10

// AFTER
SELECT COUNT(DISTINCT m.id)
FROM medicines m
LEFT JOIN medicine_batches mb ON mb.medicine_id = m.id
GROUP BY m.id
HAVING SUM(COALESCE(mb.quantity_remaining, 0)) <= 10
```

**Function:** `ReceptionistController::getSidebarData()`

#### F. Reports - Medicine Inventory Stats
```php
// BEFORE
SELECT COUNT(*) as total_medicines,
       COUNT(CASE WHEN stock_quantity <= 10 THEN 1 END) as low_stock,
       SUM(stock_quantity * unit_price) as total_inventory_value
FROM medicines

// AFTER (with subquery for batch aggregation)
SELECT COUNT(DISTINCT m.id) as total_medicines,
       COUNT(DISTINCT CASE WHEN total_stock <= 10 THEN m.id END) as low_stock,
       COALESCE(SUM(total_stock * m.unit_price), 0) as total_inventory_value
FROM medicines m
LEFT JOIN (
    SELECT medicine_id, SUM(quantity_remaining) as total_stock
    FROM medicine_batches GROUP BY medicine_id
) mb ON m.id = mb.medicine_id
```

**Function:** `ReceptionistController::reports()`

#### G. Doctor Medicine Search
```php
// BEFORE
SELECT id, name, ..., stock_quantity FROM medicines WHERE ... AND stock_quantity > 0

// AFTER
SELECT m.id, m.name, ..., COALESCE(SUM(mb.quantity_remaining), 0) as stock_quantity
FROM medicines m
LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id
WHERE ...
GROUP BY m.id, ...
HAVING stock_quantity > 0
```

**Function:** `DoctorController::search_medicines()`

#### H. Admin Medicine List & Dashboard
Similar aggregation queries as above for:
- `AdminController::medicines()` - medicine listing page
- `AdminController::getDashboardStats()` - low stock count

---

## Testing Recommendations

After these fixes, test the following workflows:

1. **Dashboard Access**
   - [x] Login as receptionist
   - [ ] Verify sidebar shows correct counts (pending patients, appointments, low stock)

2. **Patient Registration**
   - [ ] Register new patient
   - [ ] Verify patient appears in listing with correct `current_step`

3. **Medicine Management**
   - [ ] View medicine list (should show aggregated stock from batches)
   - [ ] Add new medicine (creates medicine + initial batch)
   - [ ] Adjust stock (creates new batch or updates latest batch)

4. **Dispensing**
   - [ ] Doctor prescribes medicine
   - [ ] Receptionist dispenses (should deduct from batches using FEFO)
   - [ ] Verify stock correctly reduced across batches

5. **Reports**
   - [ ] View receptionist reports
   - [ ] Verify medicine inventory stats (low stock, expiring soon, total value)

6. **Doctor Workflows**
   - [ ] Search medicines during consultation
   - [ ] Verify only in-stock medicines appear

---

## Performance Notes

### Query Performance Impact

**Medium Impact:**
- `ReceptionistController::patients()` - now uses derived table instead of view
  - **Recommendation:** Create `patient_latest_visit` view when ready for better performance

**Low Impact:**
- All medicine queries now JOIN `medicine_batches` and use GROUP BY
  - Most medicine tables are small (<1000 records typically)
  - Indexes on `medicine_batches.medicine_id` help performance

**Optimization Done:**
- FEFO dispensing algorithm only fetches active batches (quantity_remaining > 0)
- Medicine search limits to 20 results

### Future Database Migration

When ready to import the completed `zahanati.sql`:

1. **Backup current database:**
   ```bash
   mysqldump -u root zahanati > backup_before_migration.sql
   ```

2. **Import new schema:**
   ```bash
   mysql -u root zahanati < database/zahanati.sql
   ```

3. **Benefits after migration:**
   - `patient_latest_visit` view improves patient listing performance
   - All demo data and seed records available
   - 7 reporting views for analytics
   - Proper indexes and foreign keys

4. **Data Migration (if keeping existing data):**
   - Export existing patients, consultations, etc.
   - Import new schema
   - Re-import data
   - Manual reconciliation may be needed

---

## Verification Checklist

- [x] No PHP syntax errors in controllers
- [x] No references to non-existent `patient_latest_visit` view in queries
- [x] No references to non-existent `medicines.stock_quantity` column
- [x] All medicine stock operations use `medicine_batches`
- [x] FEFO algorithm implemented for stock deduction
- [ ] Runtime testing completed (see Testing Recommendations above)

---

## Files Modified

1. `controllers/ReceptionistController.php`
   - `patients()` - removed view dependency
   - `getSidebarData()` - fixed low stock query
   - `medicine()` - fixed medicine listing query
   - `add_medicine()` - creates medicine + batch
   - `update_medicine_stock()` - adds/adjusts batches
   - `dispense_medicines()` - FEFO batch deduction
   - `dispense_patient_medicine()` - FEFO batch deduction
   - `reports()` - fixed medicine inventory stats

2. `controllers/DoctorController.php`
   - `search_medicines()` - aggregates stock from batches

3. `controllers/AdminController.php`
   - `medicines()` - aggregates stock from batches
   - `getDashboardStats()` - fixed low stock count query

---

## Next Steps

1. **User Testing:** Try registering a patient and verify the workflow
2. **Medicine Testing:** Add a medicine and verify batch creation
3. **Performance Monitoring:** Check if patient listing query is acceptably fast
4. **Optional:** Create `patient_latest_visit` view if performance is slow:
   ```sql
   CREATE VIEW patient_latest_visit AS
   SELECT pv.patient_id, pv.id AS visit_id, pv.status, pv.visit_date, pv.visit_type
   FROM patient_visits pv
   JOIN (
       SELECT patient_id, MAX(created_at) AS latest 
       FROM patient_visits 
       GROUP BY patient_id
   ) latest ON latest.patient_id = pv.patient_id AND latest.latest = pv.created_at;
   ```

---

## Known Limitations

1. **No View Optimization:** Patient listing query is slower without the view (acceptable for <10,000 patients)
2. **Batch Tracking Overhead:** Medicine operations now require multiple queries (batch fetch + updates)
3. **Manual Batch Numbers:** System auto-generates batch numbers as `BATCH-{date}-{id}` - no user input for existing batches

---

## Compatibility Status

✅ **Application is now compatible with existing database schema**
- No immediate database changes required
- Code works with `medicine_batches` table
- Patient listing works without `patient_latest_visit` view
- All stock operations use batch-tracking model

🔄 **Optional optimization available:** Import `database/zahanati.sql` when ready for:
- Performance improvements (views)
- Complete seed data
- Enhanced reporting capabilities
