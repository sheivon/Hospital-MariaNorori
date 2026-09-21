<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class ExamTypeRepository extends BaseRepository implements RepositoryInterface
{
    public function all(array $filters = []): array
    {
        $sql = 'SELECT * FROM exam_types';
        $conditions = [];
        $params = [];

        if (!empty($filters['active_only'])) {
            $conditions[] = 'active = 1';
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY name ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM exam_types WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function isActive(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT active FROM exam_types WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false && (int)$row['active'] === 1;
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM exam_types WHERE code = :code LIMIT 1');
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO exam_types (code, name, active) VALUES (:code, :name, :active)'
        );
        $stmt->execute([
            ':code' => $data['code'],
            ':name' => $data['name'],
            ':active' => (int)($data['active'] ?? 1),
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE exam_types SET code = :code, name = :name, active = :active WHERE id = :id'
        );
        return $stmt->execute([
            ':code' => $data['code'],
            ':name' => $data['name'],
            ':active' => (int)($data['active'] ?? 1),
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM exam_types WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
