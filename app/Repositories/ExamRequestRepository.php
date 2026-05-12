<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class ExamRequestRepository extends \App\Repositories\BaseRepository implements RepositoryInterface
{
    

    public function all(array $filters = []): array
    {
        $sql = 'SELECT er.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, p.cedula
             FROM exam_requests er
             LEFT JOIN patients p ON p.id = er.patient_id';
        $params = [];
        $conditions = ['er.deleted_at IS NULL'];

        if (!empty($filters['patient_id'])) {
            $conditions[] = 'er.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY er.request_date DESC, er.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM exam_requests WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO exam_requests (patient_id, request_date, exam_type, notes, result, status, created_by)
             VALUES (:patient_id, :request_date, :exam_type, :notes, :result, :status, :created_by)'
        );
        $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':request_date' => $data['request_date'] ?? null,
            ':exam_type' => $data['exam_type'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':result' => $data['result'] ?? null,
            ':status' => $data['status'] ?? 'pending',
            ':created_by' => $data['created_by'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE exam_requests SET patient_id = :patient_id, request_date = :request_date, exam_type = :exam_type, notes = :notes, result = :result, status = :status WHERE id = :id'
        );
        return $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':request_date' => $data['request_date'] ?? null,
            ':exam_type' => $data['exam_type'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':result' => $data['result'] ?? null,
            ':status' => $data['status'] ?? 'pending',
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE exam_requests SET deleted_at = NOW() WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}

