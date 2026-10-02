<?php

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected string $table = 'audit_logs';
    protected array $fillable = [
        'user_id',
        'action',
        'module',
        'record_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values'
    ];

    public static function log(string $action, string $module, ?int $recordId = null, ?array $oldValues = null, ?array $newValues = null): void
    {
        try {
            $user = \App\Helpers\SessionHelper::get('user');
            $userId = $user['id'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';

            $model = new self();
            $model->create([
                'user_id' => $userId,
                'action' => $action,
                'module' => $module,
                'record_id' => $recordId,
                'ip_address' => $ip,
                'user_agent' => substr($ua, 0, 255),
                'old_values' => $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                'new_values' => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null
            ]);
        } catch (\Throwable $e) {
            // Never break execution if audit log fails
            error_log('Audit log error: ' . $e->getMessage());
        }
    }
}
