<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class AuditLogController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $recent_actions = [];
        
        if ($db->isConnected()) {
            // Assets
            $stmt = $pdo->query("SELECT 'asset' as type, name as detail, created_at FROM assets");
            while($row = $stmt->fetch()) {
                $recent_actions[] = ['action' => 'Yeni Demirbaş Eklendi', 'module' => 'Demirbaşlar', 'details' => $row['detail'], 'created_at' => $row['created_at'], 'user_name' => 'Sistem'];
            }
            
            // Employees
            $stmt = $pdo->query("SELECT 'employee' as type, first_name || ' ' || last_name as detail, created_at FROM employees");
            while($row = $stmt->fetch()) {
                $recent_actions[] = ['action' => 'Yeni Personel Eklendi', 'module' => 'Personeller', 'details' => $row['detail'], 'created_at' => $row['created_at'], 'user_name' => 'Sistem'];
            }
            
            // Assignments
            $stmt = $pdo->query("
                SELECT 'assignment' as type, 
                       a.assignment_code || ' (' || e.first_name || ' ' || e.last_name || ')' as detail, 
                       a.created_at 
                FROM assignments a 
                JOIN employees e ON a.employee_id = e.id
            ");
            while($row = $stmt->fetch()) {
                $recent_actions[] = ['action' => 'Yeni Zimmet Verildi', 'module' => 'Zimmetler', 'details' => $row['detail'], 'created_at' => $row['created_at'], 'user_name' => 'Sistem'];
            }
            
            // Stock Items
            $stmt = $pdo->query("SELECT 'stock' as type, name as detail, created_at FROM stock_items");
            while($row = $stmt->fetch()) {
                $recent_actions[] = ['action' => 'Yeni Stok Kartı', 'module' => 'Stok', 'details' => $row['detail'], 'created_at' => $row['created_at'], 'user_name' => 'Sistem'];
            }

            // Sort combined array by created_at DESC
            usort($recent_actions, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        $this->render('audit/index', [
            'pageTitle' => 'İşlem Geçmişi',
            'logs' => $recent_actions
        ]);
    }
}
