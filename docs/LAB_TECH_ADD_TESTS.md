Lab Technician Guide — Adding Lab Tests to the System

Purpose
-------
This short guide shows lab technicians how to add new lab tests (test types) into the application so receptionists and clinicians can order them.

Who should read this
---------------------
- Lab technicians and admins responsible for maintaining the lab test catalog.

Before you begin
----------------
- You need an account with the role `lab` or an administrator account.
- Login to the application and navigate to the laboratory (Lab) section.

Where to manage lab tests
-------------------------
- In the application menu, go to Lab -> Test Catalog (or Lab Settings -> Lab Tests).
- If your UI differs, ask your administrator to point you to the page that lists lab tests and has "Add Test" or "New Test" button.

Step-by-step: Add a new lab test
--------------------------------
1. Click "Add Test" or "New Test".
2. Fill the form fields (required fields are marked with *):
   - Test Name* — Clear human-friendly name (e.g., "Malaria Test", "Full Blood Count").
   - Test Code* — Short unique code (e.g., "MAL-001", "FBC"). This helps searching and printing labels.
   - Price* — Numeric price in TZS for billing purposes. Example: 8000
   - Category — Optional (e.g., Microbiology, Hematology)
   - Is Active — Toggle whether the test can be ordered now. When off/disabled, receptionists can’t pick it for new orders.
   - Description — Optional details about the test (sample type, fasting required, turnaround time).
3. Validate the data:
   - Ensure the Test Code is unique (the system may prevent duplicates).
   - Price must be a positive number.
4. Click "Save" or "Create".
5. Confirm the test appears in the list and that you can search for it by name/code.

Tips and examples
------------------
- Use readable test codes and keep a consistent naming convention (e.g., three-letter department + incremental number).
- Add the estimated turnaround time in the description field (for example "TAT: 24 hours").

Common problems and fixes
-------------------------
- "Test code already exists": choose a unique code or ask the admin to check duplicates in the database.
- Price validation failed: ensure you entered a number (no commas) and it’s greater than zero.
- Test not appearing: make sure "Is Active" is enabled and refresh the page.

How tests are used after adding
------------------------------
- Receptionists can select active tests when registering a patient for lab tests.
- Lab technicians will see incoming orders in the lab queue and can update results.
- Billing will use the test price when recording payments for lab tests.

Support and escalation
----------------------
If you run into issues you cannot resolve:
- Contact the system administrator with a screenshot and the test details you tried to add.
- Provide the test code you attempted and any validation messages from the UI.

Maintenance notes (for admins)
------------------------------
- Test records are stored in the `lab_tests` table. Key fields: `id`, `test_name`, `test_code`, `price`, `is_active`, `created_at`, `updated_at`.
- To bulk-import tests, prepare a CSV with columns `test_name,test_code,price,category,is_active,description` and ask the dev team to import it.

Acknowledgements
----------------
- This guide was generated to help lab staff keep the test catalog up-to-date.

