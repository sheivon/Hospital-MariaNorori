<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class ChildFollowUpNoteRepository extends \App\Repositories\BaseRepository implements RepositoryInterface
{
    public function all(array $filters = []): array
    {
        $sql = 'SELECT n.*
            FROM seguimiento_notas n';

        $params = [];
        $conditions = [];

        if (!empty($filters['seguimiento_id'])) {
            $conditions[] = 'n.seguimiento_id = :seguimiento_id';
            $params[':seguimiento_id'] = (int)$filters['seguimiento_id'];
        }

        if (!empty($filters['id'])) {
            $conditions[] = 'n.id = :id';
            $params[':id'] = (int)$filters['id'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY n.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM seguimiento_notas WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO seguimiento_notas (seguimiento_id, tipo, contenido)
             VALUES (:seguimiento_id, :tipo, :contenido)'
        );

        $stmt->execute([
            ':seguimiento_id' => $data['seguimiento_id'] ?? null,
            ':tipo' => $data['tipo'] ?? null,
            ':contenido' => $data['contenido'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE seguimiento_notas SET tipo = :tipo, contenido = :contenido, updated_at = NOW() WHERE id = :id'
        );

        return $stmt->execute([
            ':tipo' => $data['tipo'] ?? null,
            ':contenido' => $data['contenido'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM seguimiento_notas WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
