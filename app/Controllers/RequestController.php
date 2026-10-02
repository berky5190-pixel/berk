<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\AssetRequest;
use App\Services\AuthService;

class RequestController extends Controller
{
    /**
     * Talepleri Listele (Personel kendi taleplerini, Yönetici tüm talepleri görür)
     */
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        $isStaff = AuthService::isStaff();
        $userId = AuthService::id();
        $empId = AuthService::employeeId();

        $statusFilter = $_GET['status'] ?? '';
        $typeFilter = $_GET['type'] ?? '';

        $query = "
            SELECT r.*, 
                   u.username as user_username, u.full_name as user_fullname,
                   e.first_name, e.last_name, e.registration_no, d.name as department_name,
                   ast.asset_code, ast.name as asset_name, ast.brand as asset_brand, ast.model as asset_model
            FROM asset_requests r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN employees e ON r.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN assets ast ON r.asset_id = ast.id
            WHERE 1=1
        ";

        $params = [];

        // Standart personel ise sadece kendi talepleri
        if (!$isStaff) {
            if ($empId) {
                $query .= " AND (r.user_id = ? OR r.employee_id = ?)";
                $params[] = $userId;
                $params[] = $empId;
            } else {
                $query .= " AND r.user_id = ?";
                $params[] = $userId;
            }
        }

        if (!empty($statusFilter)) {
            $query .= " AND r.status = ?";
            $params[] = $statusFilter;
        }

        if (!empty($typeFilter)) {
            $query .= " AND r.request_type = ?";
            $params[] = $typeFilter;
        }

        $query .= " ORDER BY r.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $requests = $stmt->fetchAll();

        // Personelin üzerine zimmetli aktif cihazlar (yeni talep modalında seçebilmesi için)
        $myAssets = [];
        if ($empId) {
            $stmtAssets = $db->prepare("
                SELECT ast.id, ast.asset_code, ast.name, ast.brand, ast.model, c.name as category_name
                FROM assignments a
                JOIN assets ast ON a.asset_id = ast.id
                LEFT JOIN asset_categories c ON ast.category_id = c.id
                WHERE a.employee_id = ? AND a.status = 'active'
                ORDER BY ast.name ASC
            ");
            $stmtAssets->execute([$empId]);
            $myAssets = $stmtAssets->fetchAll();
        }

        // İstatistikler
        $stats = [
            'total' => count($requests),
            'pending' => 0,
            'approved' => 0,
            'completed' => 0,
            'rejected' => 0
        ];
        foreach ($requests as $r) {
            if (isset($stats[$r['status']])) {
                $stats[$r['status']]++;
            }
        }

        $this->render('requests/index', [
            'pageTitle' => $isStaff ? 'Personel Talep Yönetimi' : 'Taleplerim & Arıza Bildirimi',
            'requests' => $requests,
            'myAssets' => $myAssets,
            'isStaff' => $isStaff,
            'statusFilter' => $statusFilter,
            'typeFilter' => $typeFilter,
            'stats' => $stats
        ]);
    }

    /**
     * Yeni Talep Oluştur (Personel)
     */
    public function store(): void
    {
        $post = $this->request->post();
        $db = Database::getInstance()->getConnection();
        $userId = AuthService::id();
        $empId = AuthService::employeeId();

        $type = trim($post['request_type'] ?? '');
        $title = trim($post['title'] ?? '');
        $description = trim($post['description'] ?? '');
        $urgency = trim($post['urgency'] ?? 'normal');
        $assetId = !empty($post['asset_id']) ? (int)$post['asset_id'] : null;

        if (empty($type) || empty($title) || empty($description)) {
            $this->flash('error', 'Lütfen talep türü, başlık ve açıklama alanlarını eksiksiz doldurun.');
            $this->redirect('/requests');
            return;
        }

        // Arıza veya Değişim ise cihaz seçilmişse teyit edelim
        if (in_array($type, ['malfunction', 'exchange']) && $assetId && $empId) {
            $check = $db->prepare("SELECT id FROM assignments WHERE asset_id = ? AND employee_id = ? AND status = 'active'");
            $check->execute([$assetId, $empId]);
            if (!$check->fetch() && !AuthService::isStaff()) {
                $this->flash('error', 'Seçilen demirbaş sizin üzerinize zimmetli görünmüyor.');
                $this->redirect('/requests');
                return;
            }
        }

        $requestCode = AssetRequest::generateCode();

        try {
            $stmt = $db->prepare("
                INSERT INTO asset_requests 
                (request_code, user_id, employee_id, request_type, asset_id, title, description, urgency, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ");

            $stmt->execute([
                $requestCode,
                $userId,
                $empId,
                $type,
                $assetId,
                $title,
                $description,
                $urgency
            ]);

            // Yöneticilere bildirim oluştur
            $typeNames = [
                'malfunction' => 'Arıza / Onarım',
                'exchange' => 'Cihaz Değişim',
                'equipment' => 'Yeni Ekipman / Sarf Malzeme'
            ];
            $typeName = $typeNames[$type] ?? 'Yeni Talep';
            $user = AuthService::user();
            $authorName = $user['full_name'] ?? $user['username'];

            $adminStmt = $db->query("SELECT id FROM users WHERE role_id IN (1, 2)");
            while ($admin = $adminStmt->fetch()) {
                $notifStmt = $db->prepare("
                    INSERT INTO notifications (user_id, type, title, message, link, is_read, created_at)
                    VALUES (?, 'system_alert', ?, ?, '/requests', 0, CURRENT_TIMESTAMP)
                ");
                $notifStmt->execute([
                    $admin['id'],
                    "Yeni Talep: {$typeName}",
                    "{$authorName} tarafından yeni bir talep iletildi: '{$title}' ({$requestCode})"
                ]);
            }

            $this->flash('success', "Talebiniz (#{$requestCode}) başarıyla oluşturuldu ve Bilgi İşlem birimine iletildi.");
        } catch (\Exception $e) {
            $this->flash('error', 'Talep oluşturulurken bir hata meydana geldi: ' . $e->getMessage());
        }

        $this->redirect('/requests');
    }

    /**
     * Talep Durumu Güncelle (Yönetici / IT Birimi)
     */
    public function updateStatus($id): void
    {
        if (!AuthService::isStaff()) {
            $this->flash('error', 'Bu işlemi yapmaya yetkiniz bulunmamaktadır.');
            $this->redirect('/requests');
            return;
        }

        $post = $this->request->post();
        $db = Database::getInstance()->getConnection();

        $status = trim($post['status'] ?? '');
        $adminNotes = trim($post['admin_notes'] ?? '');
        $validStatuses = ['pending', 'in_review', 'approved', 'rejected', 'completed'];

        if (!in_array($status, $validStatuses)) {
            $this->flash('error', 'Geçersiz talep durumu seçildi.');
            $this->redirect('/requests');
            return;
        }

        $stmt = $db->prepare("SELECT * FROM asset_requests WHERE id = ?");
        $stmt->execute([$id]);
        $req = $stmt->fetch();

        if (!$req) {
            $this->flash('error', 'Talep bulunamadı.');
            $this->redirect('/requests');
            return;
        }

        $resolvedAt = in_array($status, ['approved', 'rejected', 'completed']) ? date('Y-m-d H:i:s') : null;

        $updateStmt = $db->prepare("
            UPDATE asset_requests 
            SET status = ?, admin_notes = ?, resolved_by_user_id = ?, resolved_at = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $updateStmt->execute([
            $status,
            $adminNotes,
            AuthService::id(),
            $resolvedAt,
            $id
        ]);

        // Talep sahibine otomatik bildirim gönder
        $statusLabels = [
            'pending' => 'Beklemeye Alındı',
            'in_review' => 'İncelemeye Alındı',
            'approved' => 'Onaylandı',
            'rejected' => 'Reddedildi',
            'completed' => 'Tamamlandı'
        ];
        $statusLabel = $statusLabels[$status] ?? $status;

        $message = "'{$req['title']}' başlıklı talebinizin durumu güncellendi: {$statusLabel}.";
        if (!empty($adminNotes)) {
            $message .= " (Not: {$adminNotes})";
        }

        $notifStmt = $db->prepare("
            INSERT INTO notifications (user_id, type, title, message, link, is_read, created_at)
            VALUES (?, 'system_alert', 'Talep Durumu Güncellendi', ?, '/requests', 0, CURRENT_TIMESTAMP)
        ");
        $notifStmt->execute([$req['user_id'], $message]);

        $this->flash('success', "Talep (#{$req['request_code']}) durumu '{$statusLabel}' olarak güncellendi ve personele bildirim gönderildi.");
        $this->redirect('/requests');
    }

    /**
     * Talep İptali (Personel henüz beklemedeyken iptal edebilir)
     */
    public function cancel($id): void
    {
        $db = Database::getInstance()->getConnection();
        $userId = AuthService::id();
        $isStaff = AuthService::isStaff();

        $stmt = $db->prepare("SELECT * FROM asset_requests WHERE id = ?");
        $stmt->execute([$id]);
        $req = $stmt->fetch();

        if (!$req) {
            $this->flash('error', 'Talep bulunamadı.');
            $this->redirect('/requests');
            return;
        }

        if (!$isStaff && $req['user_id'] != $userId) {
            $this->flash('error', 'Bu talebi iptal etme yetkiniz yok.');
            $this->redirect('/requests');
            return;
        }

        if (!$isStaff && $req['status'] !== 'pending') {
            $this->flash('error', 'İncelenmeye başlanmış veya sonuçlandırılmış talepler iptal edilemez.');
            $this->redirect('/requests');
            return;
        }

        $delStmt = $db->prepare("DELETE FROM asset_requests WHERE id = ?");
        $delStmt->execute([$id]);

        $this->flash('success', 'Talep başarıyla iptal edildi / kaldırıldı.');
        $this->redirect('/requests');
    }
}
