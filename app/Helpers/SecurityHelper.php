<?php

namespace App\Helpers;

class SecurityHelper
{
    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function sanitizeString(string $input): string
    {
        return trim(strip_tags($input));
    }

    public static function hashPassword(string $password): string
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($password, PASSWORD_ARGON2ID);
        }
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes((int)ceil($length / 2)));
    }

    public static function sanitizeFilename(string $filename): string
    {
        // Strip directory separators and null bytes
        $filename = basename(str_replace(['\\', '/', "\0"], '', $filename));
        // Remove unsafe characters, leave alphanumeric, dots, dashes, underscores
        return preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
    }
}
