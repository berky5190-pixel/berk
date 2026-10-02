<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class TrashController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        
        // Silinenleri audit_logs tablosundan çekiyoruz (action = 'deleted')
        $stmt = $db->prepare("
            SELECT al.*, u.full_name as user_name 
            FROM audit_logs al 
            LEFT JOIN users u ON al.user_id = u.id 
            WHERE al.action = 'deleted' 
            ORDER BY al.created_at DESC 
            LIMIT 100
        ");
        $stmt->execute();
        $logs = $stmt->fetchAll();

        $deletedAssets = [];
        $deletedEmployees = [];
        $deletedAssignments = [];

        foreach ($logs as $log) {
            $data = json_decode($log['old_values'], true);
            $details = $data['details'] ?? 'Bilinmeyen Kayıt';
            $item = [
                'id' => $log['id'],
                'date' => $log['created_at'],
                'user' => $log['user_name'] ?? 'Sistem',
                'details' => $details,
                'can_restore' => isset($data['restore_data']) && !empty($data['restore_data'])
            ];

            if ($log['module'] === 'assets') {
                $deletedAssets[] = $item;
            } elseif ($log['module'] === 'employees') {
                $deletedEmployees[] = $item;
            } elseif ($log['module'] === 'assignments') {
                $deletedAssignments[] = $item;
            }
        }

        $this->render('trash/index', [
            'pageTitle' => 'Son Silinenler (Çöp Kutusu)',
            'deletedAssets' => $deletedAssets,
            'deletedEmployees' => $deletedEmployees,
            'deletedAssignments' => $deletedAssignments
        ]);
    }

    public function restore($id): void
    {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("SELECT * FROM audit_logs WHERE id = ? AND action = 'deleted'");
            $stmt->execute([$id]);
            $log = $stmt->fetch();

            if (!$log) {
                $this->flash('error', 'Geri yüklenecek kayıt bulunamadı.');
                $this->redirect('/trash');
                return;
            }

            $data = json_decode($log['old_values'], true);
            if (empty($data['restore_data'])) {
                $this->flash('error', 'Bu kaydın eski detayları bulunmadığından geri yüklenemez.');
                $this->redirect('/trash');
                return;
            }

            $restoreData = $data['restore_data'];
            $table = $log['module'];
            
            // Build dynamic insert
            $columns = array_keys($restoreData);
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $colNames = implode(', ', array_map(fn($c) => "`$c`", $columns));
            $values = array_values($restoreData);

            $insertSql = "INSERT INTO `$table` ($colNames) VALUES ($placeholders)";
            $db->prepare($insertSql)->execute($values);

            // Clean up the log so it doesn't show in trash anymore
            $db->prepare("DELETE FROM audit_logs WHERE id = ?")->execute([$id]);

            $this->flash('success', 'Kayıt başarıyla geri yüklendi.');
        } catch (\Exception $e) {
            $this->flash('error', 'Geri yüklenirken bir hata oluştu: ' . $e->getMessage());
        }

        $this->redirect('/trash');
    }
}
