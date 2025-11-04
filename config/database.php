<?php
// Database configuration - Railway compatible (uses environment variables)
// Support single connection URL from Railway: set MYSQL_URL = ${{ MySQL.MYSQL_URL }}
$mysql_url = getenv('MYSQL_URL') ?: getenv('MYSQLDATABASE_URL') ?: getenv('DATABASE_URL');

if ($mysql_url) {
    // parse e.g. mysql://root:pass@host:3306/dbname
    $parts = parse_url($mysql_url);
    define('DB_HOST', $parts['host'] ?? 'localhost');
    define('DB_USER', $parts['user'] ?? 'root');
    define('DB_PASS', isset($parts['pass']) ? $parts['pass'] : '');
    // path starts with '/', remove it
    $db_name = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
    define('DB_NAME', $db_name ?: 'zahanati');
    if (isset($parts['port'])) {
        define('DB_PORT', $parts['port']);
    }
} else {
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_NAME', getenv('DB_NAME') ?: 'zahanati');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '');
}
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
