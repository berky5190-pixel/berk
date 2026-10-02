<?php

namespace App\Services;

use App\Core\Database;

class AuditLogger
{
    public static function log(string $module, string $action, ?int $recordId, string $details, ?array $dataToRestore = null): void
    {
        $db = Database::getInstance();
        if (!$db->isConnected()) return;

        $userId = AuthService::id() ?: 1;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        // Storing the details in old_values as a JSON string for simplicity
        $oldValues = json_encode([
            'details' => $details,
            'restore_data' => $dataToRestore
        ]);

        $sql = "INSERT INTO audit_logs (user_id, module, action, record_id, ip_address, user_agent, old_values) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$userId, $module, $action, $recordId, $ip, $ua, $oldValues]);
    }
}
