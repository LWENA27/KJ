<?php
require_once __DIR__ . '/config/database.php';

// Reset admin password to 'admin123'
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update admin user password
$query = "UPDATE users SET password = ? WHERE username = 'admin'";
$stmt = $pdo->prepare($query);

if ($stmt->execute([$hashed_password])) {
    echo "Admin password has been reset successfully!\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "\nYou can now log in to the admin panel.\n";
} else {
    echo "Failed to reset admin password.\n";
}
?>
