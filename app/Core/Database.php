<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $dbh;
    private ?PDOStatement $stmt = null;

    private function __construct()
    {
        $host = env('DB_HOST', 'localhost');
        $user = env('DB_USER', 'root');
        $pass = env('DB_PASS', '');
        $dbName = env('DB_NAME', 'my_database');

        $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => true,
        ];

        try {
            $this->dbh = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            exit("DB Connection Failed: " . $e->getMessage());
        }
    }


    public static function getInstance(): static
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }


    public function query(string $sql): static
    {
        $this->stmt = $this->dbh->prepare($sql);
        return $this;
    }

    public function bind(string|int $param, mixed $value, int $type = 0): static
    {
        if ($type === 0) {
            $type = match (true) {
                is_int($value)   => PDO::PARAM_INT,
                is_bool($value)  => PDO::PARAM_BOOL,
                is_null($value)  => PDO::PARAM_NULL,
                default          => PDO::PARAM_STR,
            };
        }

        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }

    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    public function resultSet(): array
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    public function single(): array|false
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    public function fetchColumn(): mixed
    {
        $this->execute();
        return $this->stmt->fetchColumn();
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    public function lastInsertId(): int
    {
        return (int) $this->dbh->lastInsertId();
    }

    // Tambahan opsional: Transaksi
    public function beginTransaction(): void
    {
        $this->dbh->beginTransaction();
    }

    public function commit(): void
    {
        $this->dbh->commit();
    }

    public function rollBack(): void
    {
        $this->dbh->rollBack();
    }

    // Opsional: Shortcut untuk eksekusi insert/update/delete tanpa perlu call bind+execute
    public function run(string $sql, array $params = []): bool
    {
        $this->stmt = $this->dbh->prepare($sql);
        return $this->stmt->execute($params);
    }

    // Opsional: fetch satu baris dengan bind otomatis
    public function fetch(string $sql, array $params = []): array|false
    {
        $this->stmt = $this->dbh->prepare($sql);
        $this->stmt->execute($params);
        return $this->stmt->fetch();
    }

    // Opsional: fetch semua baris dengan bind otomatis
    public function fetchAll(string $sql, array $params = []): array
    {
        $this->stmt = $this->dbh->prepare($sql);
        $this->stmt->execute($params);
        return $this->stmt->fetchAll();
    }
}
