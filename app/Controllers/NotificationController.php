<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Services\AuthService;

class NotificationController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        $userId = AuthService::id() ?: 1; // Fallback to 1 for testing if not logged in

        $notifications = $db->query("SELECT * FROM notifications WHERE user_id = {$userId} ORDER BY created_at DESC LIMIT 50")->fetchAll();

        // Mark as read
        $db->query("UPDATE notifications SET is_read = 1, read_at = CURRENT_TIMESTAMP WHERE user_id = {$userId} AND is_read = 0");

        $this->render('notifications/index', [
            'pageTitle' => 'Bildirimler',
            'notifications' => $notifications
        ]);
    }

    public function markAllAsRead(): void
    {
        $db = Database::getInstance()->getConnection();
        $userId = AuthService::id() ?: 1;
        
        $db->query("UPDATE notifications SET is_read = 1, read_at = CURRENT_TIMESTAMP WHERE user_id = {$userId} AND is_read = 0");
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Tüm bildirimler okundu olarak işaretlendi.']);
        exit;
    }
}
