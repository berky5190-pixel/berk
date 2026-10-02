<?php

define('ROOT_PATH', dirname(__DIR__));

// Autoloader setup
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = ROOT_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Database;

putenv('DB_DRIVER=sqlite');
putenv('DB_DATABASE=' . ROOT_PATH . '/database/database.sqlite');

$db = Database::getInstance();
$pdo = $db->getConnection();

if (!$db->isConnected()) {
    die("Veritabanı bağlantısı kurulamadı.\n");
}

echo "5 Personel ekleniyor...\n";
$stmt = $pdo->prepare("INSERT INTO employees (department_id, registration_no, first_name, last_name, email, title, status) VALUES (1, ?, ?, ?, ?, ?, 'active')");
$employees = [
    ['SC-1001', 'Ahmet', 'Yılmaz', 'ahmet.y@sirket.com', 'Yazılım Geliştirici'],
    ['SC-1002', 'Ayşe', 'Kaya', 'ayse.k@sirket.com', 'İnsan Kaynakları Uzmanı'],
    ['SC-1003', 'Mehmet', 'Demir', 'mehmet.d@sirket.com', 'Sistem Yöneticisi'],
    ['SC-1004', 'Fatma', 'Çelik', 'fatma.c@sirket.com', 'Muhasebe Müdürü'],
    ['SC-1005', 'Ali', 'Can', 'ali.c@sirket.com', 'Pazarlama Uzmanı']
];

foreach ($employees as $emp) {
    try {
        $stmt->execute($emp);
    } catch (Exception $e) {
        // ignore duplicate
    }
}

echo "10 Demirbaş ekleniyor...\n";
$stmt = $pdo->prepare("INSERT INTO assets (category_id, location_id, asset_code, name, brand, model, serial_number, status) VALUES (1, 1, ?, ?, ?, ?, ?, 'in_stock')");
$assets = [
    ['D-2001', 'MacBook Pro 14"', 'Apple', 'M2 Pro', 'SN-APPLE-001'],
    ['D-2002', 'MacBook Air 13"', 'Apple', 'M1', 'SN-APPLE-002'],
    ['D-2003', 'ThinkPad T14', 'Lenovo', 'Gen 3', 'SN-LEN-001'],
    ['D-2004', 'ThinkPad X1 Carbon', 'Lenovo', 'Gen 10', 'SN-LEN-002'],
    ['D-2005', 'Dell XPS 15', 'Dell', '9520', 'SN-DELL-001'],
    ['D-2006', 'Dell Latitude 7420', 'Dell', '7420', 'SN-DELL-002'],
    ['D-2007', 'HP EliteBook 840', 'HP', 'G9', 'SN-HP-001'],
    ['D-2008', 'HP ProBook 450', 'HP', 'G8', 'SN-HP-002'],
    ['D-2009', 'Mac Studio', 'Apple', 'M2 Max', 'SN-APPLE-003'],
    ['D-2010', 'LG 27" 4K Monitör', 'LG', '27UN880', 'SN-LG-001']
];

foreach ($assets as $asset) {
    try {
        $stmt->execute($asset);
    } catch (Exception $e) {
        // ignore duplicate
    }
}

echo "Test verileri başarıyla eklendi!\n";
