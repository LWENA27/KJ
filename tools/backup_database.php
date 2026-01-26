<?php
// CLI backup script for KJ application
// Usage: php tools/backup_database.php [--keep-days=14]

chdir(dirname(__DIR__)); // project root

$keepDays = 14;
foreach ($argv as $arg) {
    if (strpos($arg, '--keep-days=') === 0) {
        $keepDays = (int)substr($arg, strlen('--keep-days='));
    }
}

// Load DB config - avoid creating a PDO (in case remote connection blocked) so we'll parse config file if possible
$configFile = __DIR__ . '/../config/database.php';

if (!file_exists($configFile)) {
    fwrite(STDERR, "Config file not found: $configFile\n");
    exit(1);
}

// Attempt to include config file. It may define DB constants.
// To avoid side effects of creating PDO, we'll parse constants if they are defined after include.
@include_once $configFile;

// Determine DB connection values from defined constants or environment
$dbHost = defined('DB_HOST') ? DB_HOST : getenv('DB_HOST');
$dbUser = defined('DB_USER') ? DB_USER : getenv('DB_USER');
$dbPass = defined('DB_PASS') ? DB_PASS : getenv('DB_PASS');
$dbName = defined('DB_NAME') ? DB_NAME : getenv('DB_NAME');
$dbPort = defined('DB_PORT') ? DB_PORT : (getenv('DB_PORT') ?: 3306);

if (empty($dbHost) || empty($dbUser) || empty($dbName)) {
    fwrite(STDERR, "Missing DB configuration. Please set DB_HOST, DB_USER, DB_NAME in config/database.php or environment.\n");
    exit(1);
}

$backupDir = __DIR__ . '/../storage/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$timestamp = date('Ymd-His');
$baseName = preg_replace('/[^a-z0-9_-]/i', '-', $dbName);
$filename = "$backupDir/{$baseName}-$timestamp.sql.gz";

// Build mysqldump command
$mysqldump = 'mysqldump';
$cmd = escapeshellcmd($mysqldump) .
    ' --single-transaction --quick --skip-lock-tables' .
    ' -h ' . escapeshellarg($dbHost) .
    ' -P ' . escapeshellarg($dbPort) .
    ' -u ' . escapeshellarg($dbUser);

// Use a temporary option file to avoid exposing password in process list (create .my.cnf in backup dir)
$optFile = sys_get_temp_dir() . '/.kj_backup_mycnf_' . uniqid();
$optContents = "[client]\nuser={$dbUser}\npassword={$dbPass}\nhost={$dbHost}\nport={$dbPort}\n";
file_put_contents($optFile, $optContents);
chmod($optFile, 0600);
$cmd .= ' --defaults-extra-file=' . escapeshellarg($optFile) . ' ' . escapeshellarg($dbName);

// Pipe to gzip
$cmd .= ' | gzip > ' . escapeshellarg($filename);

// Run
$start = microtime(true);
exec($cmd, $output, $returnVar);
$duration = round(microtime(true) - $start, 2);

// Clean up temp file
@unlink($optFile);

$logFile = __DIR__ . '/../logs/backup.log';
$log = date('Y-m-d H:i:s') . " | backup: $filename | duration: {$duration}s | return: {$returnVar}\n";
file_put_contents($logFile, $log, FILE_APPEND);

if ($returnVar !== 0) {
    fwrite(STDERR, "Backup command failed. See $logFile for details.\n");
    exit(2);
}

// Remove old backups
$files = glob($backupDir . '/' . $baseName . '-*.sql.gz');
$now = time();
foreach ($files as $f) {
    if (is_file($f)) {
        $fileMTime = filemtime($f);
        if ($fileMTime !== false && ($now - $fileMTime) > ($keepDays * 86400)) {
            @unlink($f);
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | removed old backup: $f\n", FILE_APPEND);
        }
    }
}

fwrite(STDOUT, "Backup completed: $filename\n");
exit(0);
