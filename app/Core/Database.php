<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;
    private bool $isConnected = false;
    private ?string $lastError = null;
    private string $activeDriver = 'mysql';

    private function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $driver = $config['driver'] ?? 'mysql';

        if ($driver === 'sqlite') {
            $this->connectSqlite($config['sqlite_path'] ?? dirname(__DIR__, 2) . '/database/database.sqlite');
        } else {
            // Attempt MySQL connection
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                $this->isConnected = true;
                $this->activeDriver = 'mysql';
            } catch (PDOException $e) {
                // If MySQL is not running and SQLite database exists or can be used, fall back to SQLite
                $sqlitePath = $config['sqlite_path'] ?? dirname(__DIR__, 2) . '/database/database.sqlite';
                if (file_exists($sqlitePath)) {
                    $this->connectSqlite($sqlitePath);
                } else {
                    $this->isConnected = false;
                    $this->lastError = $e->getMessage();
                }
            }
        }
    }

    private function connectSqlite(string $path): void
    {
        try {
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $this->pdo = new PDO("sqlite:{$path}", null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $this->pdo->exec('PRAGMA foreign_keys = ON;');
            $this->isConnected = true;
            $this->activeDriver = 'sqlite';
        } catch (PDOException $e) {
            $this->isConnected = false;
            $this->lastError = $e->getMessage();
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): ?PDO
    {
        return $this->pdo;
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function getActiveDriver(): string
    {
        return $this->activeDriver;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function beginTransaction(): bool
    {
        if ($this->pdo && !$this->pdo->inTransaction()) {
            return $this->pdo->beginTransaction();
        }
        return false;
    }

    public function commit(): bool
    {
        if ($this->pdo && $this->pdo->inTransaction()) {
            return $this->pdo->commit();
        }
        return false;
    }

    public function rollBack(): bool
    {
        if ($this->pdo && $this->pdo->inTransaction()) {
            return $this->pdo->rollBack();
        }
        return false;
    }

    private function __clone() {}
    public function __wakeup() {}
}
