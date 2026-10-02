<?php
$pdo = new PDO('sqlite:database/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("SELECT id, username, email, password_hash, status FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "USERS FOUND: " . count($users) . "\n";
foreach ($users as $u) {
    echo "ID: {$u['id']} | Username: {$u['username']} | Email: {$u['email']} | Status: {$u['status']}\n";
    $verifyAdmin = password_verify('Admin123!', $u['password_hash']);
    echo "Verify 'Admin123!': " . ($verifyAdmin ? 'YES' : 'NO') . "\n";
}
