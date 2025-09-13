# KJ Dispensary — Implementation TODO

This TODO contains prioritized tasks derived from the user story and the current application state. Work items are grouped, include acceptance criteria, and indicate likely files to edit.

Priority: High

1) Enforce consultation payment before doctor access
   - Why: Receptionist must collect TZS 3,000 for consultations and record it before doctor attends.
   - Files: `controllers/ReceptionistController.php`, `controllers/DoctorController.php`, `database/updates.sql`
   - DB: add `visits` table or extend visits with `consultation_paid` boolean and `status` field.
   - Acceptance: Doctor's queue only shows consults with `consultation_paid = 1` (or non-consult visits). Attempts to open unpaid consultation must be blocked.

2) Payments and receipts (atomic)
   - Why: All payments (consultation, lab, medicines) must be recorded and linked to a visit.
   - Files: `controllers/ReceptionistController.php`, `views/receptionist/*`, `database/updates.sql`
   - Acceptance: Payments recorded in `payments` table, linked to `visit_id`, receipts printable.

3) Doctor workflow: create lab orders and prescriptions
   - Why: Doctor must be able to send patient to lab (select tests) and prescribe medicines.
   - Files: `controllers/DoctorController.php`, `views/doctor/*`, `database/updates.sql`
   - DB: `lab_orders`, `prescriptions` tables.

4) Reception: collect lab payments and dispatch to lab
   - Why: Lab tests require payment before processing.
   - Files: `views/receptionist/*`, `controllers/ReceptionistController.php`
   - Acceptance: Paid lab orders move to lab queue.

5) Lab technician: process tests & return results
   - Why: Lab tech completes orders and posts results to doctor.
   - Files: `controllers/LabController.php`, `views/lab/*`

6) Pharmacy/dispense: support full and partial dispensing
   - Why: Patients may not afford full prescription; need balance tracking.
   - Files: `views/receptionist/dispense_medicines.php`, `controllers/ReceptionistController.php`
   - DB: `dispenses` table to track items and amounts collected.

7) Minor services & direct lab-only flows
   - Why: Support cases that don't require doctor or have direct lab-only visits.
   - Files: `views/receptionist/register_patient.php`, `controllers/ReceptionistController.php`

8) Printable visit summary
   - Why: Provide printable form mirroring physical form from registration to final stage.
   - Files: `views/print/visit_summary.php` (new)

9) Access control & audit logs
   - Why: Secure operations and auditability.
   - Files: `includes/BaseController.php`, add `audit_logs` table

10) Tests & CI
    - Write integration/smoke tests covering registration → payment → doctor → lab → dispense.

Next steps recommended
- Implement server-side enforcement for consultation (urgent).
- Add DB migrations for visits/payments/lab_orders/prescriptions/dispenses.
- Provide small end-to-end smoke test script.

Notes
- A lot of UI is changed already in `views/receptionist/register_patient.php` (client-side toggling & badge). Server-side enforcement is required to make the workflow robust.
