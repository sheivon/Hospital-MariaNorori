<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UserRoleModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function all(): array
    {
        if (!$this->tableExists('user_roles')) {
            return $this->fallbackRoles();
        }

        $rows = $this->pdo->query('SELECT role_id, role, accesstype FROM user_roles ORDER BY role_id ASC')->fetchAll(PDO::FETCH_ASSOC) ?: [];
        if (!empty($rows)) {
            return $rows;
        }

        return $this->fallbackRoles();
    }

    public function exists(string $role): bool
    {
        $role = strtolower(trim($role));
        if ($role === '') {
            return false;
        }

        if (!$this->tableExists('user_roles')) {
            foreach ($this->fallbackRoles() as $item) {
                if (strtolower((string)$item['role']) === $role) {
                    return true;
                }
            }
            return false;
        }

        $stmt = $this->pdo->prepare('SELECT role_id FROM user_roles WHERE role = :role LIMIT 1');
        $stmt->execute([':role' => $role]);
        return (bool)$stmt->fetchColumn();
    }

    private function tableExists(string $table): bool
    {
        $stmt = $this->pdo->prepare('SHOW TABLES LIKE :table');
        $stmt->execute([':table' => $table]);
        return (bool)$stmt->fetchColumn();
    }

    private function fallbackRoles(): array
    {
        return [
            ['role_id' => 1, 'role' => 'admin', 'accesstype' => 'full'],
            ['role_id' => 2, 'role' => 'doctor', 'accesstype' => 'clinical'],
            ['role_id' => 3, 'role' => 'user', 'accesstype' => 'basic'],
        ];
    }
}
