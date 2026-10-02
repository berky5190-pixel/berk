<?php

define('ROOT_PATH', dirname(__DIR__));
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = ROOT_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});
require __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    // Add Employees
    $stmt = $db->prepare("INSERT OR IGNORE INTO employees (id, department_id, registration_no, first_name, last_name, title, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 1, 'EMP001', 'Ahmet', 'Yılmaz', 'Yazılım Uzmanı', 'active']);
    $stmt->execute([2, 1, 'EMP002', 'Mehmet', 'Kaya', 'Sistem Yöneticisi', 'active']);
    $stmt->execute([3, 2, 'EMP003', 'Ayşe', 'Demir', 'İnsan Kaynakları Uzmanı', 'active']);

    // Add Assets
    $stmt = $db->prepare("INSERT OR IGNORE INTO assets (id, category_id, location_id, asset_code, name, brand, model, status, purchase_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 1, 1, 'AS-001', 'Dizüstü Bilgisayar', 'Lenovo', 'ThinkPad T14', 'assigned', 35000]);
    $stmt->execute([2, 2, 1, 'AS-002', '24" Monitör', 'Dell', 'P2419H', 'in_stock', 4500]);
    $stmt->execute([3, 3, 2, 'AS-003', 'Ergonomik Ofis Koltuğu', 'IKEA', 'Markus', 'assigned', 3200]);

    // Add Assignments
    $stmt = $db->prepare("INSERT OR IGNORE INTO assignments (id, assignment_code, employee_id, asset_id, assigned_by_user_id, assignment_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 'ZMT-20231015-001', 1, 1, 1, '2023-10-15', 'active']);
    $stmt->execute([2, 'ZMT-20231101-002', 3, 3, 1, '2023-11-01', 'active']);

    // Add Stock Items
    $stmt = $db->prepare("INSERT OR IGNORE INTO stock_items (id, category_id, stock_code, name, current_stock, min_stock, unit) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 5, 'STK-001', 'A4 Fotokopi Kağıdı', 4, 10, 'box']); // Kritik Stok
    $stmt->execute([2, 4, 'STK-002', 'Kablosuz Fare', 15, 5, 'piece']);
    $stmt->execute([3, 4, 'STK-003', 'Lazer Yazıcı Toneri', 2, 3, 'piece']); // Kritik Stok

    $db->commit();
    echo "Örnek veriler başarıyla eklendi!\n";
} catch (\Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "Hata: " . $e->getMessage() . "\n";
}
