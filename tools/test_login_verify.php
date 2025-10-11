<?php
require_once __DIR__ . '/../config/database.php';
$username = $argv[1] ?? 'receptionist1';
$stmt = $pdo->prepare("SELECT id,username,password_hash,is_active FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "User not found: $username\n";
    exit(1);
}
echo "id: {$user['id']}\nusername: {$user['username']}\nis_active: {$user['is_active']}\npassword_hash: {$user['password_hash']}\n";
$ok = password_verify('password', $user['password_hash']);
echo "password_verify('password', hash) => " . ($ok ? 'true' : 'false') . "\n";
?>