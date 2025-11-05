# Quick Reference: Allocation System

## Why NOT to Combine allocate_patient() and allocate_resources()?

### **TL;DR: Different Purpose, Different Users, Different Data Model**

```
allocate_patient()          allocate_resources()
───────────────────────────────────────────────────────────
Doctor → Doctor             Doctor → Any Staff
Handoff entire patient      Delegate specific services
1 consultation record       Multiple service_order records
POST form (redirect)        POST JSON (AJAX)
Dashboard modal             Patient detail page
```

---

## 4 Major Conflicts

| # | Issue | Impact | Solution |
|---|-------|--------|----------|
| 1 | Different recipient types | Can't use same validation | Keep separate logic |
| 2 | Different data models | Consultations ≠ Service_orders | Use different tables |
| 3 | Different response types | Redirect ≠ JSON | Keep separate routes |
| 4 | Different business logic | 1 record ≠ N records | Keep separate functions |

---

## Areas Affected by These Functions

### Frontend Views
```
doctor/dashboard.php    → Allocate Patient Modal → allocate_patient()
doctor/view_patient.php → Allocate Button → allocate_resources()
```

### Database Tables
```
consultations    ← allocate_patient() writes here
service_orders   ← allocate_resources() writes here (READY ✅)
```

### Routes
```
POST /doctor/allocate_patient       → allocate_patient()
GET  /doctor/allocate_resources     → allocate_resources()
POST /doctor/save_allocation        → save_allocation()
POST /doctor/cancel_service_order   → cancel_service_order()
```

---

## Database Status: ✅ PRODUCTION READY

### service_orders Table
```
✅ Exists
✅ 12 fields (all needed)
✅ 0 records (empty, ready)
✅ Nullable performed_by (correct)
✅ Status enum: pending|in_progress|completed|cancelled
✅ Foreign keys configured
✅ Timestamps auto-managed
```

### Supporting Tables
```
✅ services (5 active services)
✅ users (6 active staff)
✅ patient_visits (visits linked to services)
✅ patients (patient records)
```

### Your Database SQL
- ✅ Delete the separate schema migration file (you already did)
- ✅ Keep your database as-is (it's perfect)
- ✅ Your attached SQL dump is reference-only (shows correct schema)

---

## Implementation Checklist

### ✅ Completed
- [x] allocate_patient() exists (doctor handoff)
- [x] allocate_resources() added (service delegation)
- [x] save_allocation() added (create orders)
- [x] cancel_service_order() added (cancel orders)
- [x] Database schema verified
- [x] All fields present
- [x] No migrations needed

### ⏳ Still Needed
- [ ] Create views/doctor/allocate_resources.php (form UI)
- [ ] Add JavaScript AJAX handlers
- [ ] Test save_allocation endpoint
- [ ] Create staff task queues
- [ ] Test cancel_service_order endpoint

---

## Quick Architecture Diagram

```
DOCTOR DASHBOARD                    PATIENT DETAIL PAGE
        │                                   │
        │                                   │
        ▼                                   ▼
┌──────────────────────┐        ┌──────────────────────┐
│ Allocate Patient     │        │ Quick Actions        │
│ Modal                │        │                      │
│ [Select Doctor ▼]    │        │ [Print Record]       │
│ [Notes]              │        │ [Attend Patient]     │
│ [Submit]             │        │ [Allocate] ←────────┐│
└──────────────────────┘        └──────────────────────┘│
        │                                                  │
        │                                                  │
        ▼                                POST              ▼
   allocate_patient()         /doctor/allocate_resources
        │                             │
        │                             ▼
        │                   ┌──────────────────────┐
        │                   │ Service Allocation   │
        │                   │ ☐ BP Check  → [Dr. Smith]
        │                   │ ☐ Dressing  → [Nurse]
        │                   │ ☐ ECG       → [Dr. Jane]
        │                   │ [Submit] → save_allocation()
        │                   └──────────────────────┘
        │                             │
        ▼                             ▼
   consultations              service_orders
   (1 record)                 (N records, one per service)
   Status: scheduled          Status: pending
```

---

## Key Takeaways

1. ✅ **Database is ready** - No changes needed
2. ❌ **Don't combine functions** - Different workflows
3. ✅ **Keep separate routes** - Cleaner architecture
4. ✅ **Use existing tables** - service_orders ready
5. ⏳ **Next: Build the form** - allocate_resources.php

---

## Files Created for Reference

1. `docs/ALLOCATION_SYSTEM_ANALYSIS.md` - Detailed explanation
2. `docs/ALLOCATION_COMPARISON_VISUAL.md` - Visual diagrams
3. `docs/ALLOCATION_DATABASE_STATUS.md` - Database verification
4. `docs/ALLOCATION_QUICK_REFERENCE.md` - This file

