<?php

namespace App\Repositories;

use PDO;

class UserRepository extends \App\Repositories\BaseRepository
{
    private ?bool $deletedAtEnabled = null;

    protected function hasDeletedAt(): bool
    {
        return $this->hasDeletedAtForTable('users');
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :u AND is_active = 1 LIMIT 1');
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function countActiveUsers(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM users WHERE is_active = 1');
        return (int)$stmt->fetchColumn();
    }

    public function listPublicExcept(int $currentUserId, ?string $role = null): array
    {
        $sql = 'SELECT id, username, fullname, cedula, role FROM users WHERE id != :me AND is_active = 1';
        $params = [':me' => $currentUserId];
        if ($role !== null && $role !== '') {
            $sql .= ' AND role = :role';
            $params[':role'] = $role;
        }
        $sql .= ' ORDER BY username ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listAdminUsers(): array
    {
        $stmt = $this->pdo->query('SELECT id, username, fullname, cedula, role, specialty, department, created_at FROM users ORDER BY id ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, username, fullname, cedula, role, specialty, department FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function existsById(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return (bool)$stmt->fetch();
    }

    public function usernameExists(string $username, ?int $exceptId = null): bool
    {
        if ($exceptId) {
            $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = :u AND id <> :id LIMIT 1');
            $stmt->execute([':u' => $username, ':id' => $exceptId]);
        } else {
            if ($this->hasDeletedAt()) {
                $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = :u AND deleted_at IS NULL LIMIT 1');
            } else {
                $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
            }
            $stmt->execute([':u' => $username]);
        }
        return (bool)$stmt->fetch();
    }

    public function cedulaExists(string $cedula, ?int $exceptId = null): bool
    {
        if ($exceptId) {
            $stmt = $this->pdo->prepare('SELECT id FROM users WHERE cedula = :c AND id <> :id LIMIT 1');
            $stmt->execute([':c' => $cedula, ':id' => $exceptId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT id FROM users WHERE cedula = :c LIMIT 1');
            $stmt->execute([':c' => $cedula]);
        }
        return (bool)$stmt->fetch();
    }

    public function create(string $username, string $password, string $fullname, string $cedula, string $role, string $specialty = '', string $department = ''): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO users (username,password,fullname,cedula,role,specialty,department) VALUES (:u,:p,:f,:c,:r,:s,:d)');
        $stmt->execute([':u' => $username, ':p' => $hash, ':f' => $fullname, ':c' => $cedula, ':r' => $role, ':s' => $specialty, ':d' => $department]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $fields, ?string $password = null): void
    {
        $set = [];
        $params = [':id' => $id];
        foreach ($fields as $k => $v) {
            $set[] = "$k = :$k";
            $params[":$k"] = $v;
        }
        if ($password !== null && $password !== '') {
            $set[] = 'password = :p';
            $params[':p'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $sql = 'UPDATE users SET ' . implode(', ', $set) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function countAdmins(): int
    {
        $sql = "SELECT COUNT(*) FROM users WHERE role = 'admin'";
        if ($this->hasDeletedAt()) {
            $sql .= ' AND deleted_at IS NULL';
        }
        return (int)$this->pdo->query($sql)->fetchColumn();
    }

    public function delete(int $id): void
    {
        if ($this->hasDeletedAt()) {
            $stmt = $this->pdo->prepare('UPDATE users SET is_active = 0, deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        } else {
            $stmt = $this->pdo->prepare('UPDATE users SET is_active = 0 WHERE id = :id');
        }
        $stmt->execute([':id' => $id]);
    }
}



