<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UserModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function listPublicExcept(int $currentUserId): array
    {
        $stmt = $this->pdo->prepare('SELECT id, username, fullname, cedula FROM users WHERE id != :me ORDER BY username ASC');
        $stmt->execute([':me' => $currentUserId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listAdminUsers(): array
    {
        $stmt = $this->pdo->query('SELECT id, username, fullname, cedula, role, created_at FROM users ORDER BY id ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, username, fullname, cedula, role FROM users WHERE id = :id LIMIT 1');
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
            $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
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

    public function create(string $username, string $password, string $fullname, string $cedula, string $role): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO users (username,password,fullname,cedula,role) VALUES (:u,:p,:f,:c,:r)');
        $stmt->execute([':u' => $username, ':p' => $hash, ':f' => $fullname, ':c' => $cedula, ':r' => $role]);
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
        return (int)$this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
