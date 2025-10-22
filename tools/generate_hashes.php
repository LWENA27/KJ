<?php
$users = ['admin','receptionist1','doctor1','lab1'];
foreach ($users as $u) {
    $hash = password_hash('password', PASSWORD_DEFAULT);
    // Escape single quotes for SQL
    $escaped = str_replace("'", "\\'", $hash);
    echo "UPDATE `users` SET `password_hash` = '" . $escaped . "' WHERE `username` = '" . $u . "';\n";
}

echo "\n-- NOTE: Password for all users is 'password' (plaintext).\n";
?>