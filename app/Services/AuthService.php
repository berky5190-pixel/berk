<?php

namespace App\Services;

use App\Models\User;
use App\Models\AuditLog;
use App\Helpers\SessionHelper;
use App\Helpers\SecurityHelper;

class AuthService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function attempt(string $identifier, string $password): bool
    {
        // Rate limiting check
        $attemptsKey = 'login_attempts_' . md5($identifier);
        $attempts = SessionHelper::get($attemptsKey, 0);

        if ($attempts >= 5) {
            $lockoutKey = 'lockout_until_' . md5($identifier);
            $lockoutUntil = SessionHelper::get($lockoutKey, 0);
            if (time() < $lockoutUntil) {
                $waitSec = $lockoutUntil - time();
                SessionHelper::setFlash('error', "Çok fazla hatalı giriş denemesi. Lütfen {$waitSec} saniye bekleyiniz.");
                return false;
            } else {
                // Reset after lockout expires
                SessionHelper::remove($attemptsKey);
                SessionHelper::remove($lockoutKey);
            }
        }

        $user = $this->userModel->findByUsernameOrEmail($identifier);

        if (!$user) {
            $this->incrementFailedAttempts($identifier);
            return false;
        }

        if ($user['status'] !== 'active') {
            SessionHelper::setFlash('error', 'Hesabınız pasife alınmıştır. Lütfen sistem yöneticinizle görüşün.');
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            $this->incrementFailedAttempts($identifier);
            return false;
        }

        // Login successful: reset attempts
        SessionHelper::remove($attemptsKey);

        // Fetch user permissions
        $permissions = $this->userModel->getPermissions((int)$user['id']);

        // Prevent session fixation
        SessionHelper::regenerate();

        // Store user in session (excluding password hash)
        unset($user['password_hash']);
        $user['permissions'] = $permissions;

        SessionHelper::set('user', $user);
        SessionHelper::set('logged_in_at', time());

        // Update last login
        $this->userModel->updateLastLogin((int)$user['id']);

        // Log to Audit Log
        AuditLog::log('user.login', 'auth', (int)$user['id'], null, [
            'username' => $user['username'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        return true;
    }

    private function incrementFailedAttempts(string $identifier): void
    {
        $attemptsKey = 'login_attempts_' . md5($identifier);
        $attempts = (int)SessionHelper::get($attemptsKey, 0) + 1;
        SessionHelper::set($attemptsKey, $attempts);

        if ($attempts >= 5) {
            $lockoutKey = 'lockout_until_' . md5($identifier);
            SessionHelper::set($lockoutKey, time() + 300); // 5 min lockout
        }

        AuditLog::log('user.login_failed', 'auth', null, null, [
            'identifier' => $identifier,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'attempts' => $attempts
        ]);
    }

    public static function check(): bool
    {
        SessionHelper::start();
        return SessionHelper::has('user') && !empty(SessionHelper::get('user')['id']);
    }

    public static function user(): ?array
    {
        SessionHelper::start();
        return SessionHelper::get('user');
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function hasRole(string|array $roles): bool
    {
        $user = self::user();
        if (!$user || empty($user['role_slug'])) {
            return false;
        }

        $roles = (array)$roles;
        return in_array($user['role_slug'], $roles, true);
    }

    public static function hasPermission(string $permission): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        // Admin has all permissions
        if (($user['role_slug'] ?? '') === 'admin') {
            return true;
        }

        $userPerms = $user['permissions'] ?? [];
        return in_array($permission, $userPerms, true);
    }

    public static function employeeId(): ?int
    {
        $user = self::user();
        if (!$user) {
            return null;
        }

        if (!empty($user['employee_id'])) {
            return (int)$user['employee_id'];
        }

        // Fallback: look up employee by email or user_id
        $db = \App\Core\Database::getInstance()->getConnection();
        if (!empty($user['email'])) {
            $stmt = $db->prepare("SELECT id FROM employees WHERE email = ? LIMIT 1");
            $stmt->execute([$user['email']]);
            $empId = $stmt->fetchColumn();
            if ($empId) {
                // Update session
                $user['employee_id'] = (int)$empId;
                SessionHelper::set('user', $user);
                return (int)$empId;
            }
        }

        return null;
    }

    public static function employee(): ?array
    {
        $empId = self::employeeId();
        if (!$empId) {
            return null;
        }

        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT e.*, d.name as department_name, d.code as department_code 
            FROM employees e 
            LEFT JOIN departments d ON e.department_id = d.id 
            WHERE e.id = ? 
            LIMIT 1
        ");
        $stmt->execute([$empId]);
        $emp = $stmt->fetch();
        return $emp ?: null;
    }

    public static function isStaff(): bool
    {
        return self::hasRole(['admin', 'manager']);
    }

    public static function isEmployee(): bool
    {
        return !self::isStaff();
    }

    public static function logout(): void
    {
        $user = self::user();
        if ($user) {
            AuditLog::log('user.logout', 'auth', (int)$user['id'], null, [
                'username' => $user['username']
            ]);
        }
        SessionHelper::destroy();
    }
}
