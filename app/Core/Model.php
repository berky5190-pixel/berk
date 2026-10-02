<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected ?PDO $db;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    protected function ensureConnection(): void
    {
        if (!$this->db) {
            throw new RuntimeException("Veritabanı bağlantısı kurulamadı. Lütfen veritabanı ayarlarını kontrol edin.");
        }
    }

    public function find(int $id): ?array
    {
        $this->ensureConnection();
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $this->ensureConnection();
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$column}` = :val LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':val' => $value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function all(array $where = [], string $orderBy = 'id DESC', ?int $limit = null, int $offset = 0): array
    {
        $this->ensureConnection();
        $sql = "SELECT * FROM `{$this->table}`";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $col => $val) {
                $conditions[] = "`{$col}` = :w_{$col}";
                $params[":w_{$col}"] = $val;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $this->ensureConnection();
        // Filter fillable attributes if defined
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        $columns = array_keys($data);
        $fields = implode(', ', array_map(fn($col) => "`{$col}`", $columns));
        $placeholders = implode(', ', array_map(fn($col) => ":{$col}", $columns));

        $sql = "INSERT INTO `{$this->table}` ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);

        $params = [];
        foreach ($data as $key => $val) {
            $params[":{$key}"] = $val;
        }

        $stmt->execute($params);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $this->ensureConnection();
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        $assignments = [];
        $params = [":primary_id" => $id];

        foreach ($data as $col => $val) {
            $assignments[] = "`{$col}` = :u_{$col}";
            $params[":u_{$col}"] = $val;
        }

        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $assignments) . " WHERE `{$this->primaryKey}` = :primary_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $this->ensureConnection();
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function count(array $where = []): int
    {
        $this->ensureConnection();
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}`";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $col => $val) {
                $conditions[] = "`{$col}` = :c_{$col}";
                $params[":c_{$col}"] = $val;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $this->ensureConnection();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $this->ensureConnection();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $this->ensureConnection();
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
