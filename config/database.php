<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'zahanati');
define('DB_USER', 'app_user'); // Change this to your MySQL username
define('DB_PASS', 'StrongPassword123'); // Change this to your MySQL password
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
