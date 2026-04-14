<?php

namespace App\Models;

use App\Core\Database;
use App\Interfaces\RepositoryInterface;
use PDO;

class TestModel implements RepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT t.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, u.username AS created_by_name
             FROM tests t
             LEFT JOIN patients p ON p.id = t.patient_id
             LEFT JOIN users u ON u.id = t.created_by
             WHERE t.deleted_at IS NULL';
        $params = [];

        if (!empty($filters['patient_id'])) {
            $sql .= ' AND t.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($filters['encounter_id'])) {
            $sql .= ' AND t.encounter_id = :encounter_id';
            $params[':encounter_id'] = (int)$filters['encounter_id'];
        }

        $sql .= ' ORDER BY t.test_date DESC, t.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tests WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tests (patient_id, test_type, result, test_date, notes, created_by)
             VALUES (:patient_id, :test_type, :result, :test_date, :notes, :created_by)'
        );
        $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':test_type' => $data['test_type'] ?? null,
            ':result' => $data['result'] ?? null,
            ':test_date' => $data['test_date'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':created_by' => $data['created_by'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tests SET patient_id = :patient_id, test_type = :test_type, result = :result, test_date = :test_date, notes = :notes WHERE id = :id'
        );
        return $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':test_type' => $data['test_type'] ?? null,
            ':result' => $data['result'] ?? null,
            ':test_date' => $data['test_date'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE tests SET deleted_at = NOW() WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
