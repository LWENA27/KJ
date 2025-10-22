Import report - zahanati database

Date: 2025-10-21 18:xx:xx

Actions taken:
- Read DB credentials from `config/database.php` (DB_HOST=localhost, DB_NAME=zahanati, DB_USER=root)
- Backed up existing database to `database/backups/zahanati_backup_2025-10-21.sql`
- Dropped and recreated database `zahanati` with charset utf8mb4
- Fixed SQL syntax issue in `database/zahanati.sql`: removed `DEFAULT curdate()` from `patient_visits.visit_date` which caused import error on this MySQL/MariaDB server
- Imported `database/zahanati.sql` into the recreated database

Verification:
- Tables present (sample): patients, patient_visits, payments, consultations, lab_tests, medicines, users, vital_signs, etc.
- Row counts: patients=6, patient_visits=8, payments=9

Notes and recommendations:
- The SQL dump included view definitions that use functions like `curdate()` and ordering/grouping that may trigger errors on more strict SQL modes (ONLY_FULL_GROUP_BY). I removed only the immediate problematic default expression so the import would complete.
- If you plan to re-import on other servers, consider running the dump with options compatible with target server versions or manually review view definitions for ONLY_FULL_GROUP_BY compatibility.
- If you want the original `visit_date` default behavior, re-add it later via ALTER TABLE using a server that supports function defaults, or set defaults at application-level when inserting.

Files changed:
- /var/www/html/KJ/database/zahanati.sql - patched to remove `DEFAULT curdate()` from `patient_visits.visit_date`
- /var/www/html/KJ/database/backups/zahanati_backup_2025-10-21.sql - backup of previous DB (if created)
- /var/www/html/KJ/database/IMPORT_REPORT.md - this report

If you want, I can:
- Recreate a corrected view (if any view definitions still fail under ONLY_FULL_GROUP_BY) or adjust SQL_MODE in the app to be more permissive.
- Run application-level smoke tests (e.g., load the web app pages) to ensure no runtime SQL errors remain.

