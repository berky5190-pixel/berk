<?php
$pdo = new PDO('sqlite:database/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$check = $pdo->prepare("SELECT id FROM users WHERE username = 'ahmet'");
$check->execute();
$existing = $check->fetch();

if (!$existing) {
    $stmt = $pdo->prepare("
        INSERT INTO users (role_id, employee_id, username, email, password_hash, full_name, status)
        VALUES (3, 4, 'ahmet', 'ahmet.y@sirket.com', ?, 'Ahmet Yılmaz', 'active')
    ");
    $stmt->execute([password_hash('User123!', PASSWORD_DEFAULT)]);
    echo "Örnek personel kullanıcısı (ahmet / User123!) oluşturuldu.\n";
} else {
    $stmt = $pdo->prepare("UPDATE users SET employee_id = 4, role_id = 3 WHERE username = 'ahmet'");
    $stmt->execute();
    echo "Mevcut kullanıcı ahmet güncellendi.\n";
}
