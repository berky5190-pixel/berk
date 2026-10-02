<?php

/**
 * Demirbaş ve Zimmet Yönetim Sistemi
 * Veritabanı Kurulum ve Tohumlama Aracı (Migration & Seeder Runner)
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/app/Core/Database.php';

use App\Core\Database;

echo "\n=======================================================\n";
echo "   DEMİRBAŞ & ZİMMET SİSTEMİ - VERİTABANI KURULUMU     \n";
echo "=======================================================\n\n";

$dbConfig = require ROOT_PATH . '/config/database.php';
$schemaFile = ROOT_PATH . '/database/schema.sql';
$seederFile = ROOT_PATH . '/database/seeders.sql';

if (!file_exists($schemaFile) || !file_exists($seederFile)) {
    die("HATA: schema.sql veya seeders.sql dosyası bulunamadı!\n");
}

$driver = $argv[1] ?? $dbConfig['driver'] ?? 'mysql';
$pdo = null;

if ($driver === 'mysql') {
    echo "[1/4] MySQL bağlantısı deneniyor ({$dbConfig['host']}:{$dbConfig['port']})...\n";
    try {
        // Connect to server without database to create it if not exists
        $serverDsn = sprintf(
            'mysql:host=%s;port=%s;charset=%s',
            $dbConfig['host'],
            $dbConfig['port'],
            $dbConfig['charset']
        );
        $serverPdo = new PDO($serverDsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        echo "  -> '{$dbConfig['database']}' veritabanı hazırlandı.\n";

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $dbConfig['host'],
            $dbConfig['port'],
            $dbConfig['database'],
            $dbConfig['charset']
        );
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
        echo "  -> MySQL bağlantısı başarılı.\n";
    } catch (PDOException $e) {
        echo "  [UYARI] MySQL sunucusuna bağlanılamadı: " . $e->getMessage() . "\n";
        echo "  [BİLGİ] Sıfır kurulum gereksinimi için SQLite ortamına geçiş yapılıyor...\n";
        $driver = 'sqlite';
    }
}

if ($driver === 'sqlite') {
    $sqliteFile = $dbConfig['sqlite_path'] ?? (ROOT_PATH . '/database/database.sqlite');
    echo "[1/4] SQLite veritabanı hazırlanıyor ({$sqliteFile})...\n";
    $dir = dirname($sqliteFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    // If exists, remove for fresh migration if --fresh is provided
    if (in_array('--fresh', $argv) && file_exists($sqliteFile)) {
        unlink($sqliteFile);
    }

    $pdo = new PDO("sqlite:{$sqliteFile}", null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    echo "  -> SQLite veritabanı oluşturuldu ve bağlandı.\n";
}

// 2. Tablo Şeması Kurulumu
echo "[2/4] Tablo şemaları oluşturuluyor...\n";

if ($driver === 'mysql') {
    $schemaSql = file_get_contents($schemaFile);
    $pdo->exec($schemaSql);
} else {
    // SQLite compatible schema creation
    createSqliteSchema($pdo);
}
echo "  -> 16 ana tablo başarıyla oluşturuldu.\n";

// 3. Tohumlama (Seeders)
echo "[3/4] Başlangıç verileri ve yönetici hesabı ekleniyor...\n";
if ($driver === 'mysql') {
    $seederSql = file_get_contents($seederFile);
    $pdo->exec($seederSql);
} else {
    seedSqliteData($pdo);
}
echo "  -> Roller, yetkiler, departmanlar, kategoriler ve Admin hesabı eklendi.\n";

// 4. Doğrulama
echo "[4/4] Kurulum doğrulanıyor...\n";
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$roleCount = $pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
$deptCount = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();

echo "  -> Aktif Kullanıcı Sayısı: {$userCount}\n";
echo "  -> Tanımlı Rol Sayısı: {$roleCount}\n";
echo "  -> Departman Sayısı: {$deptCount}\n";
echo "\n=======================================================\n";
echo "   KURULUM BAŞARIYLA TAMAMLANDI!\n";
echo "   Yönetici Kullanıcı Adı : admin\n";
echo "   Yönetici E-posta       : admin@kurum.com\n";
echo "   Yönetici Parola        : Admin123!\n";
echo "=======================================================\n\n";

function createSqliteSchema(PDO $pdo): void {
    $queries = [
        "CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            module TEXT NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS role_permissions (
            role_id INTEGER NOT NULL,
            permission_id INTEGER NOT NULL,
            PRIMARY KEY (role_id, permission_id),
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        );",
        "CREATE TABLE IF NOT EXISTS departments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            code TEXT NOT NULL UNIQUE,
            description TEXT,
            status TEXT DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS locations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            code TEXT NOT NULL UNIQUE,
            building TEXT,
            room_number TEXT,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS employees (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            department_id INTEGER NOT NULL,
            registration_no TEXT NOT NULL UNIQUE,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            email TEXT,
            phone TEXT,
            title TEXT NOT NULL,
            hire_date DATE,
            status TEXT DEFAULT 'active',
            photo_path TEXT,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (department_id) REFERENCES departments(id)
        );",
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role_id INTEGER NOT NULL,
            employee_id INTEGER,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            full_name TEXT NOT NULL,
            status TEXT DEFAULT 'active',
            last_login_at DATETIME,
            remember_token TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (role_id) REFERENCES roles(id),
            FOREIGN KEY (employee_id) REFERENCES employees(id)
        );",
        "CREATE TABLE IF NOT EXISTS asset_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            parent_id INTEGER,
            name TEXT NOT NULL,
            code TEXT NOT NULL UNIQUE,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (parent_id) REFERENCES asset_categories(id)
        );",
        "CREATE TABLE IF NOT EXISTS assets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            location_id INTEGER,
            asset_code TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            brand TEXT NOT NULL,
            model TEXT NOT NULL,
            serial_number TEXT,
            barcode TEXT,
            purchase_date DATE,
            purchase_price NUMERIC DEFAULT 0.00,
            currency TEXT DEFAULT 'TRY',
            warranty_start DATE,
            warranty_end DATE,
            status TEXT DEFAULT 'in_stock',
            photo_path TEXT,
            description TEXT,
            created_by INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES asset_categories(id),
            FOREIGN KEY (location_id) REFERENCES locations(id),
            FOREIGN KEY (created_by) REFERENCES users(id)
        );",
        "CREATE TABLE IF NOT EXISTS assignments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            assignment_code TEXT NOT NULL UNIQUE,
            employee_id INTEGER NOT NULL,
            asset_id INTEGER NOT NULL,
            assigned_by_user_id INTEGER NOT NULL,
            received_by_name TEXT,
            assignment_date DATE NOT NULL,
            planned_return_date DATE,
            actual_return_date DATE,
            status TEXT DEFAULT 'active',
            return_condition TEXT,
            return_notes TEXT,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (employee_id) REFERENCES employees(id),
            FOREIGN KEY (asset_id) REFERENCES assets(id),
            FOREIGN KEY (assigned_by_user_id) REFERENCES users(id)
        );",
        "CREATE TABLE IF NOT EXISTS assignment_documents (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            assignment_id INTEGER NOT NULL,
            document_type TEXT DEFAULT 'initial_protocol',
            original_filename TEXT NOT NULL,
            stored_filename TEXT NOT NULL UNIQUE,
            file_path TEXT NOT NULL,
            mime_type TEXT NOT NULL,
            file_size INTEGER NOT NULL,
            sha256_hash TEXT,
            description TEXT,
            uploaded_by_user_id INTEGER NOT NULL,
            uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
            FOREIGN KEY (uploaded_by_user_id) REFERENCES users(id)
        );",
        "CREATE TABLE IF NOT EXISTS stock_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            location_id INTEGER,
            stock_code TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            brand TEXT,
            model TEXT,
            unit TEXT DEFAULT 'piece',
            current_stock INTEGER DEFAULT 0,
            min_stock INTEGER DEFAULT 5,
            max_stock INTEGER DEFAULT 1000,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES asset_categories(id),
            FOREIGN KEY (location_id) REFERENCES locations(id)
        );",
        "CREATE TABLE IF NOT EXISTS stock_movements (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            stock_item_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            movement_type TEXT NOT NULL,
            quantity INTEGER NOT NULL,
            previous_stock INTEGER NOT NULL,
            new_stock INTEGER NOT NULL,
            reference_type TEXT,
            reference_id INTEGER,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (stock_item_id) REFERENCES stock_items(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );",
        "CREATE TABLE IF NOT EXISTS notifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            type TEXT NOT NULL,
            title TEXT NOT NULL,
            message TEXT NOT NULL,
            link TEXT,
            is_read INTEGER DEFAULT 0,
            read_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );",
        "CREATE TABLE IF NOT EXISTS audit_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            action TEXT NOT NULL,
            module TEXT NOT NULL,
            record_id INTEGER,
            ip_address TEXT NOT NULL,
            user_agent TEXT,
            old_values TEXT,
            new_values TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );",
        "CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            key TEXT NOT NULL UNIQUE,
            value TEXT,
            [group] TEXT DEFAULT 'general',
            description TEXT,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );"
    ];

    foreach ($queries as $q) {
        $pdo->exec($q);
    }
}

function seedSqliteData(PDO $pdo): void {
    // Roles
    $pdo->exec("INSERT OR IGNORE INTO roles (id, name, slug, description) VALUES
        (1, 'Sistem Yöneticisi', 'admin', 'Tüm sistem yetkileri'),
        (2, 'Birim Sorumlusu', 'manager', 'Demirbaş ve personel operasyonları'),
        (3, 'Standart Kullanıcı', 'user', 'Kendi zimmetlerini görüntüleme');");

    // Permissions
    $perms = [
        [1, 'Demirbaşları Listele', 'assets.view', 'assets'],
        [2, 'Demirbaş Ekle', 'assets.create', 'assets'],
        [3, 'Demirbaş Düzenle', 'assets.edit', 'assets'],
        [4, 'Demirbaş Sil', 'assets.delete', 'assets'],
        [5, 'Zimmetleri Listele', 'assignments.view', 'assignments'],
        [6, 'Zimmet Oluştur', 'assignments.create', 'assignments'],
        [7, 'Zimmet İade Al', 'assignments.return', 'assignments'],
        [8, 'Zimmet Belgesi Yükle', 'assignments.upload_doc', 'assignments'],
        [9, 'Zimmet Belgesi İndir', 'assignments.download_doc', 'assignments'],
        [10, 'Personelleri Listele', 'employees.view', 'employees'],
        [11, 'Personel Ekle/Düzenle', 'employees.manage', 'employees'],
        [12, 'Stokları Yönet', 'stock.manage', 'stock'],
        [13, 'Raporları Görüntüle', 'reports.view', 'reports'],
        [14, 'İşlem Geçmişini İncele', 'audit.view', 'audit'],
        [15, 'Kullanıcıları Yönet', 'users.manage', 'users'],
        [16, 'Sistem Ayarları', 'settings.manage', 'settings'],
    ];

    $stmt = $pdo->prepare("INSERT OR IGNORE INTO permissions (id, name, slug, module) VALUES (?, ?, ?, ?)");
    foreach ($perms as $p) {
        $stmt->execute($p);
    }

    // Role permissions
    for ($i = 1; $i <= 16; $i++) {
        $pdo->exec("INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES (1, {$i})");
        if ($i <= 14) {
            $pdo->exec("INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES (2, {$i})");
        }
    }
    $pdo->exec("INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES (3, 1), (3, 5), (3, 9)");

    // Departments
    $pdo->exec("INSERT OR IGNORE INTO departments (id, name, code, description) VALUES
        (1, 'Bilgi İşlem Daire Başkanlığı', 'DEP-IT', 'Yazılım ve donanım yönetimi'),
        (2, 'İnsan Kaynakları', 'DEP-HR', 'Personel ve özlük işleri'),
        (3, 'Muhasebe ve Finans', 'DEP-ACC', 'Mali işler ve fatura'),
        (4, 'İdari ve Destek Hizmetleri', 'DEP-ADM', 'Bina ve lojistik');");

    // Locations
    $pdo->exec("INSERT OR IGNORE INTO locations (id, name, code, building, room_number) VALUES
        (1, 'Merkez Bina - Bilgi İşlem', 'LOC-IT', 'A Blok', 'Kat 2 - No: 204'),
        (2, 'Merkez Depo', 'LOC-DEPOT', 'B Blok', 'Zemin Kat - No: 01'),
        (3, 'Genel Ofisler', 'LOC-OFFICE', 'A Blok', 'Kat 1');");

    // Asset Categories
    $pdo->exec("INSERT OR IGNORE INTO asset_categories (id, parent_id, name, code, description) VALUES
        (1, NULL, 'Bilgisayar & Donanım', 'CAT-COMP', 'Dizüstü, masaüstü bilgisayarlar'),
        (2, 1, 'Dizüstü Bilgisayar (Laptop)', 'CAT-LAPTOP', 'Taşınabilir bilgisayarlar'),
        (3, 1, 'Masaüstü Bilgisayar (PC)', 'CAT-DESKTOP', 'İş istasyonları'),
        (4, NULL, 'Monitör & Ekran', 'CAT-MONITOR', 'Ekranlar'),
        (5, NULL, 'Yazıcı & Tarayıcı', 'CAT-PRINT', 'Yazıcı cihazları'),
        (6, NULL, 'Ağ Cihazları', 'CAT-NET', 'Router, switch ve modemler'),
        (7, NULL, 'Ofis Mobilyası', 'CAT-FURN', 'Masa ve koltuklar'),
        (8, NULL, 'Sarf Malzeme & Aksesuar', 'CAT-ACC', 'Klavye, mouse, kablolar');");

    // Admin user: admin / Admin123!
    $adminHash = '$2y$10$F005jmLibEgjEhP8/MxfzuZqW6mliVpxqYAsYoVSsypVDo5ccYJXO';
    $pdo->exec("INSERT OR IGNORE INTO users (id, role_id, username, email, password_hash, full_name, status) VALUES
        (1, 1, 'admin', 'admin@kurum.com', '{$adminHash}', 'Sistem Yöneticisi', 'active'),
        (2, 1, 'berk', 'berky5190@gmail.com', '{$adminHash}', 'Berk (Yönetici)', 'active')");

    // Sample Settings
    $pdo->exec("INSERT OR IGNORE INTO settings (key, value, [group], description) VALUES
        ('company_name', 'T.C. Kurumsal Demirbaş ve Envanter Yönetimi', 'general', 'Resmi kurum adı'),
        ('company_email', 'bilgi@kurum.gov.tr', 'general', 'Kurumsal e-posta'),
        ('company_phone', '0 (212) 555 00 00', 'general', 'Kurum telefonu'),
        ('assignment_code_prefix', 'ZMT-', 'assignment', 'Zimmet kodu ön eki'),
        ('critical_stock_threshold', '5', 'stock', 'Kritik stok eşiği');");
}
