<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;
    private bool $isConnected = false;
    private ?string $lastError = null;
    private string $activeDriver = 'mysql';

    private function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $driver = $config['driver'] ?? 'mysql';

        if ($driver === 'sqlite') {
            $this->connectSqlite($config['sqlite_path'] ?? dirname(__DIR__, 2) . '/database/database.sqlite');
        } else {
            // Attempt MySQL connection
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                $this->isConnected = true;
                $this->activeDriver = 'mysql';
            } catch (PDOException $e) {
                // If MySQL is not running or unreachable, ALWAYS fall back to SQLite and auto-initialize!
                $sqlitePath = $config['sqlite_path'] ?? dirname(__DIR__, 2) . '/database/database.sqlite';
                $this->connectSqlite($sqlitePath);
            }
        }
    }

    private function connectSqlite(string $path): void
    {
        try {
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $this->pdo = new PDO("sqlite:{$path}", null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $this->pdo->exec('PRAGMA foreign_keys = ON;');
            $this->isConnected = true;
            $this->activeDriver = 'sqlite';

            // Auto-heal: Check if tables are created, if not initialize schema and admin user
            $hasUsers = (int)$this->pdo->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='users'")->fetchColumn();
            if ($hasUsers === 0) {
                $this->initializeSqlite($this->pdo);
            }

            // Always ensure auxiliary tables (asset_requests & asset_maintenances) exist
            $this->ensureAuxiliaryTables($this->pdo);
        } catch (PDOException $e) {
            $this->isConnected = false;
            $this->lastError = $e->getMessage();
        }
    }

    private function initializeSqlite(PDO $pdo): void
    {
        $schemaQueries = [
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
                details TEXT,
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
            );",
            "CREATE TABLE IF NOT EXISTS asset_requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                request_code VARCHAR(50) NOT NULL UNIQUE,
                user_id INTEGER NOT NULL,
                employee_id INTEGER NULL,
                request_type VARCHAR(30) NOT NULL,
                asset_id INTEGER NULL,
                title VARCHAR(150) NOT NULL,
                description TEXT NOT NULL,
                urgency VARCHAR(20) DEFAULT 'normal',
                status VARCHAR(30) DEFAULT 'pending',
                admin_notes TEXT NULL,
                resolved_by_user_id INTEGER NULL,
                resolved_at DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (employee_id) REFERENCES employees(id),
                FOREIGN KEY (asset_id) REFERENCES assets(id)
            );",
            "CREATE TABLE IF NOT EXISTS asset_maintenances (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                asset_id INTEGER NOT NULL,
                company_name TEXT NOT NULL,
                cost NUMERIC DEFAULT 0.00,
                start_date DATE,
                end_date DATE,
                notes TEXT,
                status TEXT DEFAULT 'in_progress',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (asset_id) REFERENCES assets(id)
            );"
        ];

        foreach ($schemaQueries as $query) {
            $pdo->exec($query);
        }

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

        // Admin users: admin & berk (Password: Admin123!)
        $adminHash = '$2y$10$F005jmLibEgjEhP8/MxfzuZqW6mliVpxqYAsYoVSsypVDo5ccYJXO';
        $pdo->exec("INSERT OR IGNORE INTO users (id, role_id, username, email, password_hash, full_name, status) VALUES
            (1, 1, 'admin', 'admin@kurum.com', '{$adminHash}', 'Sistem Yöneticisi', 'active'),
            (2, 1, 'berk', 'berky5190@gmail.com', '{$adminHash}', 'Berk (Yönetici)', 'active');");

        // Sample Settings
        $pdo->exec("INSERT OR IGNORE INTO settings (key, value, [group], description) VALUES
            ('company_name', 'T.C. Kurumsal Demirbaş ve Envanter Yönetimi', 'general', 'Resmi kurum adı'),
            ('company_email', 'bilgi@kurum.gov.tr', 'general', 'Kurumsal e-posta'),
            ('company_phone', '0 (212) 555 00 00', 'general', 'Kurum telefonu'),
            ('assignment_code_prefix', 'ZMT-', 'assignment', 'Zimmet kodu ön eki'),
            ('critical_stock_threshold', '5', 'stock', 'Kritik stok eşiği');");
    }

    private function ensureAuxiliaryTables(PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS asset_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            request_code VARCHAR(50) NOT NULL UNIQUE,
            user_id INTEGER NOT NULL,
            employee_id INTEGER NULL,
            request_type VARCHAR(30) NOT NULL,
            asset_id INTEGER NULL,
            title VARCHAR(150) NOT NULL,
            description TEXT NOT NULL,
            urgency VARCHAR(20) DEFAULT 'normal',
            status VARCHAR(30) DEFAULT 'pending',
            admin_notes TEXT NULL,
            resolved_by_user_id INTEGER NULL,
            resolved_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (employee_id) REFERENCES employees(id),
            FOREIGN KEY (asset_id) REFERENCES assets(id)
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS asset_maintenances (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            asset_id INTEGER NOT NULL,
            company_name TEXT NOT NULL,
            cost NUMERIC DEFAULT 0.00,
            start_date DATE,
            end_date DATE,
            notes TEXT,
            status TEXT DEFAULT 'in_progress',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (asset_id) REFERENCES assets(id)
        );");
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): ?PDO
    {
        return $this->pdo;
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function getActiveDriver(): string
    {
        return $this->activeDriver;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function beginTransaction(): bool
    {
        if ($this->pdo && !$this->pdo->inTransaction()) {
            return $this->pdo->beginTransaction();
        }
        return false;
    }

    public function commit(): bool
    {
        if ($this->pdo && $this->pdo->inTransaction()) {
            return $this->pdo->commit();
        }
        return false;
    }

    public function rollBack(): bool
    {
        if ($this->pdo && $this->pdo->inTransaction()) {
            return $this->pdo->rollBack();
        }
        return false;
    }

    private function __clone() {}
    public function __wakeup() {}
}
