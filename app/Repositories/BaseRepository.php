<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class BaseRepository
{
    protected PDO $pdo;
    private array $deletedAtCache = [];

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::pdo();
    }

    protected function hasDeletedAtForTable(string $table): bool
    {
        if (array_key_exists($table, $this->deletedAtCache)) {
            return $this->deletedAtCache[$table];
        }

        $stmt = $this->pdo->query(sprintf("SHOW COLUMNS FROM `%s` LIKE 'deleted_at'", $table));
        return $this->deletedAtCache[$table] = (bool) $stmt->fetch();
    }
}
