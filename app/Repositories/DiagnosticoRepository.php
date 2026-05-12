<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class DiagnosticoRepository extends \App\Repositories\BaseRepository implements RepositoryInterface
{
    private ?bool $deletedAtExists = null;

    protected function hasDeletedAt(): bool
    {
        return $this->hasDeletedAtForTable('diagnostics');
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT d.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, u1.fullname AS created_by_name, u2.fullname AS updated_by_name
            FROM diagnostics d
            LEFT JOIN patients p ON d.patient_id = p.id
            LEFT JOIN users u1 ON d.created_by = u1.id
            LEFT JOIN users u2 ON d.updated_by = u2.id';

        $params = [];
        $conditions = [];

        if ($this->hasDeletedAt()) {
            $conditions[] = 'd.deleted_at IS NULL';
        }

        if (!empty($filters['patient_id'])) {
            $conditions[] = 'd.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($filters['encounter_id'])) {
            $conditions[] = 'd.encounter_id = :encounter_id';
            $params[':encounter_id'] = (int)$filters['encounter_id'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY d.date DESC, d.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $sql = 'SELECT d.*, u1.fullname AS created_by_name, u2.fullname AS updated_by_name
             FROM diagnostics d
             LEFT JOIN users u1 ON d.created_by = u1.id
             LEFT JOIN users u2 ON d.updated_by = u2.id
             WHERE d.id = :id';

        if ($this->hasDeletedAt()) {
            $sql .= ' AND d.deleted_at IS NULL';
        }

        $sql .= ' LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO diagnostics (patient_id, encounter_id, type, unit, room, icd10_code, description, status, severity, date, time, plan, weight, height, age, sex, expediente_no, cedula, inss_no, created_by)
             VALUES (:patient_id, :encounter_id, :type, :unit, :room, :icd10_code, :description, :status, :severity, :date, :time, :plan, :weight, :height, :age, :sex, :expediente_no, :cedula, :inss_no, :created_by)'
        );

        $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':encounter_id' => $data['encounter_id'] ?? null,
            ':type' => $data['type'] ?? null,
            ':unit' => $data['unit'] ?? null,
            ':room' => $data['room'] ?? null,
            ':icd10_code' => $data['icd10_code'] ?? null,
            ':description' => $data['description'] ?? null,
            ':status' => $data['status'] ?? 'active',
            ':severity' => $data['severity'] ?? null,
            ':date' => $data['date'] ?? null,
            ':time' => $data['time'] ?? null,
            ':plan' => $data['plan'] ?? null,
            ':weight' => $data['weight'] ?? null,
            ':height' => $data['height'] ?? null,
            ':age' => $data['age'] ?? null,
            ':sex' => $data['sex'] ?? null,
            ':expediente_no' => $data['expediente_no'] ?? null,
            ':cedula' => $data['cedula'] ?? null,
            ':inss_no' => $data['inss_no'] ?? null,
            ':created_by' => $data['created_by'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE diagnostics SET patient_id = :patient_id, encounter_id = :encounter_id, type = :type, unit = :unit, room = :room, icd10_code = :icd10_code,
             description = :description, status = :status, severity = :severity, date = :date, time = :time, plan = :plan, weight = :weight,
             height = :height, age = :age, sex = :sex, expediente_no = :expediente_no, cedula = :cedula, inss_no = :inss_no, updated_by = :updated_by
             WHERE id = :id';

        if ($this->hasDeletedAt()) {
            $sql .= ' AND deleted_at IS NULL';
        }

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':encounter_id' => $data['encounter_id'] ?? null,
            ':type' => $data['type'] ?? null,
            ':unit' => $data['unit'] ?? null,
            ':room' => $data['room'] ?? null,
            ':icd10_code' => $data['icd10_code'] ?? null,
            ':description' => $data['description'] ?? null,
            ':status' => $data['status'] ?? 'active',
            ':severity' => $data['severity'] ?? null,
            ':date' => $data['date'] ?? null,
            ':time' => $data['time'] ?? null,
            ':plan' => $data['plan'] ?? null,
            ':weight' => $data['weight'] ?? null,
            ':height' => $data['height'] ?? null,
            ':age' => $data['age'] ?? null,
            ':sex' => $data['sex'] ?? null,
            ':expediente_no' => $data['expediente_no'] ?? null,
            ':cedula' => $data['cedula'] ?? null,
            ':inss_no' => $data['inss_no'] ?? null,
            ':updated_by' => $data['updated_by'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        if ($this->hasDeletedAt()) {
            $stmt = $this->pdo->prepare('UPDATE diagnostics SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        } else {
            $stmt = $this->pdo->prepare('DELETE FROM diagnostics WHERE id = :id');
        }

        return $stmt->execute([':id' => $id]);
    }
}



