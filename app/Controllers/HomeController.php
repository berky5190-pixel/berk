<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        if (!\App\Services\AuthService::check()) {
            $this->render('system/landing', [], 'blank');
            return;
        }

        // Standart personel ise doğrudan Personel Portalı'nı göster
        if (!\App\Services\AuthService::isStaff()) {
            $this->employeePortal();
            return;
        }

        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stats = [
            'total_assets' => 0,
            'assigned_assets' => 0,
            'in_stock_assets' => 0,
            'defective_assets' => 0,
            'service_assets' => 0,
            'total_employees' => 0,
            'active_assignments' => 0,
            'pending_return' => 0,
            'critical_stock' => 0
        ];
        
        $recent_actions = [];
        $notifications = [];

        if ($db->isConnected()) {
            // Assets stats
            $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM assets GROUP BY status");
            $assets = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
            
            $stats['total_assets'] = array_sum($assets);
            $stats['assigned_assets'] = $assets['assigned'] ?? 0;
            $stats['in_stock_assets'] = $assets['in_stock'] ?? 0;
            $stats['defective_assets'] = $assets['defective'] ?? 0;
            $stats['service_assets'] = $assets['in_service'] ?? 0;
            
            // Employees
            $stats['total_employees'] = $pdo->query("SELECT COUNT(*) FROM employees WHERE status='active'")->fetchColumn();
            
            // Assignments
            $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM assignments GROUP BY status");
            $assignments = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
            $stats['active_assignments'] = $assignments['active'] ?? 0;
            $stats['pending_return'] = $assignments['pending_return'] ?? 0;
            
            // Stock
            $stats['critical_stock'] = $pdo->query("SELECT COUNT(*) FROM stock_items WHERE current_stock <= min_stock")->fetchColumn();
            
            // Recent Actions (Combined from real tables)
            $recent_actions = [];
            
            // Latest Assets
            $stmt = $pdo->query("SELECT 'asset' as type, name as detail, datetime(created_at, 'localtime') as created_at FROM assets ORDER BY created_at DESC LIMIT 5");
            while($row = $stmt->fetch()) {
                $recent_actions[] = ['action' => 'Yeni Demirbaş Eklendi', 'module' => 'Demirbaşlar', 'details' => $row['detail'], 'created_at' => $row['created_at'], 'user_name' => 'Sistem'];
            }
            
            // Latest Employees
            $stmt = $pdo->query("SELECT 'employee' as type, first_name || ' ' || last_name as detail, datetime(created_at, 'localtime') as created_at FROM employees ORDER BY created_at DESC LIMIT 5");
            while($row = $stmt->fetch()) {
                $recent_actions[] = ['action' => 'Yeni Personel Eklendi', 'module' => 'Personeller', 'details' => $row['detail'], 'created_at' => $row['created_at'], 'user_name' => 'Sistem'];
            }
            
            // Latest Assignments
            $stmt = $pdo->query("
                SELECT 'assignment' as type, 
                       a.assignment_code || ' (' || e.first_name || ' ' || e.last_name || ')' as detail, 
                       datetime(a.created_at, 'localtime') as created_at 
                FROM assignments a 
                JOIN employees e ON a.employee_id = e.id 
                ORDER BY a.created_at DESC LIMIT 5
            ");
            while($row = $stmt->fetch()) {
                $recent_actions[] = ['action' => 'Yeni Zimmet Verildi', 'module' => 'Zimmetler', 'details' => $row['detail'], 'created_at' => $row['created_at'], 'user_name' => 'Sistem'];
            }
            
            // Sort combined array by created_at DESC
            usort($recent_actions, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            $recent_actions = array_slice($recent_actions, 0, 8); // Top 8 recent actions
            
            // Notifications (dummy or real if user logged in)
            $notifications = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5")->fetchAll();

            // Automatic Warranty Reminders
            $thirtyDaysFromNow = date('Y-m-d', strtotime('+30 days'));
            $today = date('Y-m-d');
            
            // Check for assets whose warranty is expiring in the next 30 days
            $expiringAssets = $pdo->query("SELECT id, name, asset_code, warranty_end FROM assets WHERE warranty_end IS NOT NULL AND warranty_end >= '{$today}' AND warranty_end <= '{$thirtyDaysFromNow}'")->fetchAll();
            
            $userId = \App\Services\AuthService::id() ?: 1;
            
            foreach ($expiringAssets as $ea) {
                // Check if we already notified about this recently to avoid spamming (e.g. within 7 days)
                $msg = "Uyarı: '{$ea['name']} ({$ea['asset_code']})' demirbaşının garanti süresi yakında doluyor ({$ea['warranty_end']}).";
                $checkNotif = $pdo->prepare("SELECT id FROM notifications WHERE user_id = ? AND message = ? AND created_at >= date('now', '-7 days')");
                $checkNotif->execute([$userId, $msg]);
                
                if (!$checkNotif->fetch()) {
                    $insertNotif = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message) VALUES (?, 'warning', 'Garanti Uyarısı', ?)");
                    $insertNotif->execute([$userId, $msg]);
                }
            }
        }

        $this->render('home', [
            'pageTitle' => 'Dashboard - Demirbaş ve Zimmet Yönetimi',
            'stats' => $stats,
            'recent_actions' => $recent_actions,
            'notifications' => $notifications
        ]);
    }

    public function health(): void
    {
        $db = Database::getInstance();
        $storageDir = dirname(__DIR__, 2) . '/storage';

        $dbInfo = [
            'connected' => $db->isConnected(),
            'driver' => $db->getActiveDriver(),
        ];

        if ($db->isConnected()) {
            try {
                $pdo = $db->getConnection();
                $tables = [];
                if ($db->getActiveDriver() === 'sqlite') {
                    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
                    while ($row = $stmt->fetch()) {
                        $tables[] = $row['name'];
                    }
                } else {
                    $stmt = $pdo->query("SHOW TABLES");
                    while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                        $tables[] = $row[0];
                    }
                }
                $dbInfo['tables'] = $tables;
                $dbInfo['table_count'] = count($tables);
            } catch (\Throwable $e) {
                $dbInfo['error'] = $e->getMessage();
            }
        } else {
            $dbInfo['error'] = $db->getLastError();
        }

        $this->render('system/health', [
            'pageTitle' => 'Sistem Durumu',
            'status' => 'ok',
            'app' => 'Demirbaş & Envanter Yönetim',
            'php_version' => PHP_VERSION,
            'database' => $dbInfo,
            'pdo_loaded' => extension_loaded('pdo'),
            'pdo_sqlite_loaded' => extension_loaded('pdo_sqlite'),
            'storage_writable' => is_writable($storageDir),
            'timestamp' => date('d.m.Y H:i:s'),
            'os' => php_uname('s') . ' ' . php_uname('r')
        ], 'blank');
    }

    private function employeePortal(): void
    {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $empId = \App\Services\AuthService::employeeId();
        $userId = \App\Services\AuthService::id();
        $employee = \App\Services\AuthService::employee();

        $myAssignments = [];
        if ($empId) {
            $stmt = $pdo->prepare("
                SELECT a.*, ast.asset_code, ast.name as asset_name, ast.brand, ast.model, 
                       ast.serial_number, c.name as category_name
                FROM assignments a
                JOIN assets ast ON a.asset_id = ast.id
                LEFT JOIN asset_categories c ON ast.category_id = c.id
                WHERE a.employee_id = ? AND a.status = 'active'
                ORDER BY a.assignment_date DESC
            ");
            $stmt->execute([$empId]);
            $myAssignments = $stmt->fetchAll();
        }

        $myRequests = [];
        $reqStats = ['pending_requests' => 0, 'completed_requests' => 0];
        if ($userId) {
            $stmtReq = $pdo->prepare("
                SELECT r.*, ast.name as asset_name, ast.asset_code
                FROM asset_requests r
                LEFT JOIN assets ast ON r.asset_id = ast.id
                WHERE r.user_id = ? OR (r.employee_id IS NOT NULL AND r.employee_id = ?)
                ORDER BY r.created_at DESC
            ");
            $stmtReq->execute([$userId, $empId ?: 0]);
            $myRequests = $stmtReq->fetchAll();

            foreach ($myRequests as $mr) {
                if (in_array($mr['status'], ['pending', 'in_review'])) {
                    $reqStats['pending_requests']++;
                } elseif (in_array($mr['status'], ['approved', 'completed'])) {
                    $reqStats['completed_requests']++;
                }
            }
        }

        $this->render('portal/index', [
            'pageTitle' => 'Personel Self-Service Portalı',
            'employee' => $employee,
            'myAssignments' => $myAssignments,
            'myRequests' => $myRequests,
            'stats' => $reqStats
        ]);
    }
}
