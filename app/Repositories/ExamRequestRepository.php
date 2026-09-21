<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class ExamRequestRepository extends BaseRepository implements RepositoryInterface
{
    private const ALLOWED_COLUMNS = [
        'patient_id',
        'exam_type_id',
        'request_date',
        'unit',
        'insured',
        'clinic_bed',
        'service',
        'code',
        'prior_radiograph',
        'prior_radiograph_code',
        'exam_requested',
        'clinical_data',
        'notes',
        'evolution_time',
        'presumptive_diagnosis',
        'observations',
        'doctor_code',
        'technician',
        'plates_used',
        'findings',
        'conclusions',
        'radiology_date',
        'radiographs_archived',
        'radiograph_count',
        'dictating_doctor_code',
        'result',
        'status',
        'created_by',
    ];

    public function all(array $filters = []): array
    {
        $sql = 'SELECT er.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, p.cedula,
                       et.name AS exam_type_name, et.code AS exam_type_code
                FROM exam_requests er
                LEFT JOIN patients p ON p.id = er.patient_id
                LEFT JOIN exam_types et ON et.id = er.exam_type_id';
        $params = [];
        $conditions = ['er.deleted_at IS NULL'];

        if (!empty($filters['patient_id'])) {
            $conditions[] = 'er.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = 'er.status = :status';
            $params[':status'] = (string)$filters['status'];
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
        $fields = array_values(array_intersect(self::ALLOWED_COLUMNS, array_keys($data)));
        if (empty($fields)) {
            throw new \InvalidArgumentException('No valid fields to insert');
        }
        $columns = implode(', ', $fields);
        $placeholders = implode(', ', array_map(static fn (string $f): string => ':' . $f, $fields));
        $stmt = $this->pdo->prepare("INSERT INTO exam_requests ($columns) VALUES ($placeholders)");
        foreach ($fields as $field) {
            $stmt->bindValue(':' . $field, $data[$field]);
        }
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = array_values(array_intersect(self::ALLOWED_COLUMNS, array_keys($data)));
        if (empty($fields)) {
            return false;
        }
        $sets = implode(', ', array_map(static fn (string $f): string => "$f = :$f", $fields));
        $stmt = $this->pdo->prepare("UPDATE exam_requests SET $sets WHERE id = :id AND deleted_at IS NULL");
        foreach ($fields as $field) {
            $stmt->bindValue(':' . $field, $data[$field]);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE exam_requests SET deleted_at = NOW() WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
