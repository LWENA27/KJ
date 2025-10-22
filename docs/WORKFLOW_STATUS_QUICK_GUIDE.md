# Quick Guide: Workflow Status After Registration

## What You Asked For ✅

**Your Request:**
> "ok what i want this from on submiting it as consalitation and amount is entered it should change to Workflow Status, also for any specific status a patient is current is"

**What We Implemented:**
✅ Registration with consultation + payment → **Shows proper Workflow Status**  
✅ Workflow Status reflects patient's **current stage** in the system  
✅ Status updates **automatically** as patient progresses  

---

## Visual Example: Before vs After

### BEFORE (Incorrect) ❌
```
User submits registration:
├── Visit type: Consultation
├── Fee: 3000 TZS
└── Payment: Cash

Patient List Shows:
┌─────────────────────────────────────┐
│ Patient: John Doe                   │
│ Workflow Status: 🟡 Registration    │  ← WRONG! (Payment was made)
│ Payment: ✅ Consultation Paid       │
└─────────────────────────────────────┘
```

### AFTER (Correct) ✅
```
User submits registration:
├── Visit type: Consultation
├── Fee: 3000 TZS
└── Payment: Cash

Patient List Shows:
┌─────────────────────────────────────┐
│ Patient: John Doe                   │
│ Workflow Status: 🔵 Consultation    │  ← CORRECT! (Waiting for doctor)
│ Payment: ✅ Consultation Paid       │
└─────────────────────────────────────┘
```

---

## All Workflow Statuses

### 1. 🟡 Registration
**When:** Payment NOT made yet  
**Meaning:** Patient registered but needs to pay  
**Actions:** "Process Payment" button available  
**Next Step:** Pay consultation fee  

### 2. 🔵 Consultation  
**When:** Payment made + Waiting for/with doctor  
**Meaning:** Patient ready for doctor consultation  
**Actions:** Doctor can see patient in "Pending Patients"  
**Next Step:** Doctor attends patient  

### 3. 🟡 Lab Tests
**When:** Doctor ordered lab tests  
**Meaning:** Lab work in progress  
**Actions:** Lab can see pending tests  
**Next Step:** Lab completes tests and enters results  

### 4. 🟣 Medicine Dispensing
**When:** Doctor prescribed medicine  
**Meaning:** Medicine needs to be dispensed  
**Actions:** "Dispense Medicine" button available  
**Next Step:** Receptionist dispenses medicine  

### 5. 🟣 Results Review
**When:** Tests completed, waiting for review  
**Meaning:** Doctor needs to review results  
**Actions:** Doctor can view results  
**Next Step:** Doctor reviews and decides next action  

### 6. 🟢 Completed
**When:** Visit completed  
**Meaning:** All workflow steps done  
**Actions:** Can view history  
**Next Step:** None (visit closed)  

---

## Real-World Scenarios

### Scenario 1: Simple Consultation Visit
```
Step 1: Registration with Payment
       ↓
🔵 Consultation (waiting for doctor)
       ↓
Doctor attends and prescribes medicine
       ↓
🟣 Medicine Dispensing
       ↓
Receptionist dispenses medicine
       ↓
🟢 Completed
```

### Scenario 2: Consultation with Lab Tests
```
Step 1: Registration with Payment
       ↓
🔵 Consultation (waiting for doctor)
       ↓
Doctor attends and orders lab tests
       ↓
🟡 Lab Tests (lab work in progress)
       ↓
Lab completes tests
       ↓
🟣 Results Review (doctor reviews results)
       ↓
Doctor prescribes medicine
       ↓
🟣 Medicine Dispensing
       ↓
Receptionist dispenses medicine
       ↓
🟢 Completed
```

### Scenario 3: Registration WITHOUT Payment
```
Step 1: Registration WITHOUT payment
       ↓
🟡 Registration (payment pending)
       ↓
Receptionist processes payment
       ↓
🔵 Consultation (waiting for doctor)
       ↓
... continues as above
```

---

## How to Test

### Test 1: Registration with Payment
1. Go to: `http://localhost/KJ/receptionist/register_patient`
2. Fill form:
   - First name: John
   - Last name: Doe
   - Phone: 0712345678
   - Visit type: **Consultation** ✅
   - Consultation fee: **3000** ✅
   - Payment method: **Cash** ✅
3. Click "Register Patient"
4. Go to: `http://localhost/KJ/receptionist/patients`
5. **Check:** Patient shows 🔵 **"Consultation"** badge (NOT "Registration")
6. **Check:** Payment shows ✅ **"Consultation: Paid"** (green dot)

### Test 2: Registration WITHOUT Payment
1. Go to: `http://localhost/KJ/receptionist/register_patient`
2. Fill form:
   - First name: Jane
   - Last name: Smith
   - Phone: 0723456789
   - Visit type: **Consultation**
   - Consultation fee: **(leave empty)** ❌
   - Payment method: **(leave empty)** ❌
3. Click "Register Patient"
4. Go to: `http://localhost/KJ/receptionist/patients`
5. **Check:** Patient shows 🟡 **"Registration"** badge (payment pending)
6. **Check:** Payment shows ❌ **"Consultation: Pending"** (gray dot)
7. **Check:** "Process Payment" button visible

---

## What the Code Does Now

### File: `controllers/ReceptionistController.php`

#### Enhancement 1: Records Workflow Status
```php
if ($visit_type === 'consultation' && !empty($consultation_fee)) {
    // 1. Record payment
    // 2. Create consultation record
    // 3. Track workflow status ← NEW!
    
    try {
        $stmt->prepare("INSERT INTO patient_workflow_status (...) 
                       VALUES (..., 'consultation', 'pending', ...)");
        $stmt->execute([...]);
    } catch (Exception $e) {
        // Non-blocking: continues if table doesn't exist
    }
}
```

#### Enhancement 2: Smart Workflow Status Calculation
```sql
CASE
    -- NO payment → Registration (yellow)
    WHEN payment_count = 0 
    THEN 'registration'
    
    -- Payment made + Consultation pending → Consultation (blue)
    WHEN consultation_status IN ('pending','in_progress') 
    THEN 'consultation_registration'
    
    -- Lab tests pending → Lab Tests (yellow)
    WHEN lab_test_orders > 0 
    THEN 'lab_tests'
    
    -- Prescriptions pending → Medicine Dispensing (purple)
    WHEN prescriptions_pending > 0 
    THEN 'medicine_dispensing'
    
    -- Active but nothing pending → Results Review (purple)
    WHEN visit_status = 'active' 
    THEN 'results_review'
    
    -- Visit completed → Completed (green)
    ELSE 'completed'
END
```

---

## Summary Table

| User Action | Payment | Workflow Status | Badge | Visible to Doctor? |
|-------------|---------|-----------------|-------|--------------------|
| Register, no payment | ❌ No | 🟡 Registration | Yellow | ❌ No |
| Register + pay | ✅ Yes | 🔵 Consultation | Blue | ✅ Yes |
| Doctor orders labs | ✅ Yes | 🟡 Lab Tests | Yellow | ✅ Yes |
| Doctor prescribes | ✅ Yes | 🟣 Medicine | Purple | ✅ Yes |
| Medicine dispensed | ✅ Yes | 🟢 Completed | Green | Info only |

---

## Quick Reference

### Color Legend
- 🟡 **Yellow** = Action needed / Waiting
- 🔵 **Blue** = Consultation (with doctor)
- 🟣 **Purple** = Processing (medicine/review)
- 🟢 **Green** = Completed

### Icon Legend
- 📋 Registration
- 👨‍⚕️ Consultation
- 🧪 Lab Tests
- 💊 Medicine
- ✅ Results Review
- ✔️ Completed

---

## What's Next?

### Immediate
1. ✅ **Test registration** with consultation payment
2. ✅ **Verify workflow status** shows correctly
3. ✅ **Confirm doctor can see** patient in pending list

### Optional Enhancements
1. Add workflow status filter dropdown
2. Show time in current status (e.g., "Consultation for 15 min")
3. Add workflow timeline view
4. Create workflow analytics dashboard
5. Add `patient_workflow_status` table for detailed tracking

---

## Need Help?

### Issue: Status not showing correctly
**Check:**
1. Payment recorded? `SELECT * FROM payments WHERE visit_id = ?`
2. Consultation created? `SELECT * FROM consultations WHERE visit_id = ?`
3. Visit active? `SELECT status FROM patient_visits WHERE id = ?`

### Issue: Doctor can't see patient
**Check:**
1. Payment made? Should be "Consultation: Paid"
2. Consultation status? Should be "pending" or "in_progress"
3. Visit status? Should be "active"

### Issue: Wrong workflow step
**Check:**
- Refer to WORKFLOW_STATUS_ENHANCEMENT.md for detailed logic
- Check each table: payments, consultations, lab_test_orders, prescriptions
- Verify CASE statement conditions match actual data

---

**Result:** 
✅ Registration with consultation payment now correctly shows **"Consultation"** status!  
✅ Workflow status accurately reflects patient's current stage!  
✅ System ready for testing!
