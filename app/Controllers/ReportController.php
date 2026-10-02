<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class ReportController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        
        // Özet Veriler
        $assetCount = $db->query("SELECT COUNT(*) FROM assets")->fetchColumn();
        $employeeCount = $db->query("SELECT COUNT(*) FROM employees WHERE status='active'")->fetchColumn();
        $activeAssignments = $db->query("SELECT COUNT(*) FROM assignments WHERE status='active'")->fetchColumn();
        $stockItemCount = $db->query("SELECT COUNT(*) FROM stock_items")->fetchColumn();

        // Departman Bazlı Demirbaş ve Personel Dağılımı
        $deptStats = $db->query("
            SELECT d.name, 
                   COUNT(DISTINCT e.id) as emp_count,
                   (SELECT COUNT(*) FROM assignments a JOIN employees e2 ON a.employee_id = e2.id WHERE e2.department_id = d.id AND a.status='active') as assigned_assets
            FROM departments d
            LEFT JOIN employees e ON d.id = e.department_id AND e.status='active'
            GROUP BY d.id
        ")->fetchAll();

        // Kategori Bazlı Envanter Dağılımı
        $catStats = $db->query("
            SELECT c.name, COUNT(a.id) as asset_count 
            FROM asset_categories c
            LEFT JOIN assets a ON c.id = a.category_id
            GROUP BY c.id
        ")->fetchAll();

        $this->render('reports/index', [
            'pageTitle' => 'Raporlar ve Analizler',
            'summary' => [
                'assets' => $assetCount,
                'employees' => $employeeCount,
                'assignments' => $activeAssignments,
                'stocks' => $stockItemCount
            ],
            'deptStats' => $deptStats,
            'catStats' => $catStats
        ]);
    }
    public function exportAssets(): void
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT a.asset_code, a.name, a.brand, a.model, a.serial_number, a.status, c.name as category FROM assets a LEFT JOIN asset_categories c ON a.category_id = c.id");
        $assets = $stmt->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=demirbaslar_' . date('Ymd_His') . '.csv');
        
        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['Demirbaş Kodu', 'Adı', 'Marka', 'Model', 'Seri No', 'Durum', 'Kategori']);
        
        foreach ($assets as $asset) {
            fputcsv($output, [
                $asset['asset_code'],
                $asset['name'],
                $asset['brand'],
                $asset['model'],
                $asset['serial_number'],
                $asset['status'],
                $asset['category']
            ]);
        }
        fclose($output);
        exit;
    }

    public function exportAssignments(): void
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT a.assignment_code, a.assignment_date, a.status,
                   e.first_name, e.last_name, e.registration_no,
                   asst.asset_code, asst.name as asset_name
            FROM assignments a
            JOIN employees e ON a.employee_id = e.id
            JOIN assets asst ON a.asset_id = asst.id
        ");
        $assignments = $stmt->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=zimmetler_' . date('Ymd_His') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['Zimmet Kodu', 'Tarih', 'Durum', 'Personel', 'Sicil No', 'Demirbaş Kodu', 'Demirbaş Adı']);
        
        foreach ($assignments as $a) {
            fputcsv($output, [
                $a['assignment_code'],
                $a['assignment_date'],
                $a['status'],
                $a['first_name'] . ' ' . $a['last_name'],
                $a['registration_no'],
                $a['asset_code'],
                $a['asset_name']
            ]);
        }
        fclose($output);
        exit;
    }

    public function printDepartments(): void
    {
        $db = Database::getInstance()->getConnection();
        
        $deptStats = $db->query("
            SELECT d.name, 
                   COUNT(DISTINCT e.id) as emp_count,
                   (SELECT COUNT(*) FROM assignments a JOIN employees e2 ON a.employee_id = e2.id WHERE e2.department_id = d.id AND a.status='active') as assigned_assets
            FROM departments d
            LEFT JOIN employees e ON d.id = e.department_id AND e.status='active'
            GROUP BY d.id
        ")->fetchAll();

        // Kategori Bazlı Envanter Dağılımı
        $catStats = $db->query("
            SELECT c.name, COUNT(a.id) as asset_count 
            FROM asset_categories c
            LEFT JOIN assets a ON c.id = a.category_id
            GROUP BY c.id
        ")->fetchAll();

        // Load the print view without the main layout
        require_once dirname(__DIR__) . '/Views/reports/print_departments.php';
    }
}
