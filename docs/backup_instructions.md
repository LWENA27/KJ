# Backup Instructions

This document explains how to run database backups and schedule automatic daily backups at 00:00 (midnight).

## What was added
- `tools/backup_database.php` — CLI script that creates a gzipped mysqldump into `storage/backups/` and removes files older than a configurable retention (default 14 days).
- Admin UI at `Admin -> Database Backups` (Controller: `AdminController::backup_database`) to trigger backups manually and list/download existing backups.

## Manual run
From the project root on the server (where PHP CLI is available):

```bash
php tools/backup_database.php --keep-days=14
```

The script writes logs to `logs/backup.log` and outputs the created file path on success.

## Schedule daily automatic backup (cron)
1. SSH to your server.
2. Edit the crontab for the user that can run `php` and has write access to `storage/backups`:

```bash
crontab -e
```

3. Add the following line to run the backup daily at midnight and append logs:

```cron
0 0 * * * /usr/bin/php /path/to/your/project/tools/backup_database.php --keep-days=14 >> /path/to/your/project/logs/backup.log 2>&1
```

Replace `/usr/bin/php` and `/path/to/your/project` with the correct paths on your server. To find PHP CLI path, run `which php`.

## Security & cleanup
- The script creates a temporary `.my.cnf` in the system temp folder for the duration of the dump to avoid exposing passwords in the process list. This file is removed immediately after the dump.
- Backups are stored in `storage/backups/`. Ensure this folder is not publicly accessible or is protected by server configuration. If your `.htaccess` denies `.sql` files, gzipped backups are still downloadable; lock down the directory as needed.
- Delete backups from the UI using the Delete button.

## Troubleshooting
- If `mysqldump` is not installed on your server, install it (package `mysql-client` or `mariadb-client` depending on OS).
- If the CLI user cannot write to `storage/backups`, change permissions or run cron as a user with the right permissions.
- If your hosting provider does not allow running cron jobs, consider scheduling via control panel or use an external service (cron-job.org) that calls a secure endpoint. If using a web endpoint, protect it with a secret token.

## Next steps (optional)
- Add S3/remote upload after creating the backup for offsite backups.
- Integrate retention policy configurable from Admin UI.
