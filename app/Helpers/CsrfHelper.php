<?php

namespace App\Helpers;

class CsrfHelper
{
    private const SESSION_KEY = '_csrf_token';

    public static function generateToken(): string
    {
        SessionHelper::start();
        $token = bin2hex(random_bytes(32));
        SessionHelper::set(self::SESSION_KEY, $token);
        return $token;
    }

    public static function getToken(): string
    {
        SessionHelper::start();
        $token = SessionHelper::get(self::SESSION_KEY);
        if (!$token) {
            $token = self::generateToken();
        }
        return $token;
    }

    public static function validateToken(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        $stored = SessionHelper::get(self::SESSION_KEY);
        return is_string($stored) && hash_equals($stored, $token);
    }

    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function meta(): string
    {
        $token = self::getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
