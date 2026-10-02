<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'role_id',
        'employee_id',
        'username',
        'email',
        'password_hash',
        'full_name',
        'status',
        'last_login_at',
        'remember_token'
    ];

    public function findByUsernameOrEmail(string $identifier): ?array
    {
        $this->ensureConnection();
        $sql = "SELECT u.*, r.slug as role_slug, r.name as role_name 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.role_id = r.id 
                WHERE (u.username = :id1 OR u.email = :id2) 
                LIMIT 1";
        return $this->fetchOne($sql, [
            ':id1' => $identifier,
            ':id2' => $identifier
        ]);
    }

    public function getPermissions(int $userId): array
    {
        $this->ensureConnection();
        $sql = "SELECT p.slug 
                FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                JOIN users u ON u.role_id = rp.role_id
                WHERE u.id = :user_id";
        $rows = $this->fetchAll($sql, [':user_id' => $userId]);
        return array_column($rows, 'slug');
    }

    public function updateLastLogin(int $userId): void
    {
        $this->update($userId, [
            'last_login_at' => date('Y-m-d H:i:s')
        ]);
    }
}
