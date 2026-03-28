<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class EncounterModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT e.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, u.fullname AS attending_name
             FROM encounters e
             LEFT JOIN patients p ON p.id = e.patient_id
             LEFT JOIN users u ON u.id = e.attending_user_id
             WHERE e.deleted_at IS NULL
             ORDER BY e.encounter_date DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT e.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, u.fullname AS attending_name
             FROM encounters e
             LEFT JOIN patients p ON p.id = e.patient_id
             LEFT JOIN users u ON u.id = e.attending_user_id
             WHERE e.id = :id AND e.deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO encounters (patient_id, encounter_date, encounter_type, reason_for_visit, triage_level, status, attending_user_id, notes, created_by)
             VALUES (:patient_id, :encounter_date, :encounter_type, :reason_for_visit, :triage_level, :status, :attending_user_id, :notes, :created_by)'
        );
        $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':encounter_date' => $data['encounter_date'] ?: date('Y-m-d H:i:s'),
            ':encounter_type' => $data['encounter_type'] ?? 'outpatient',
            ':reason_for_visit' => $data['reason_for_visit'] ?? null,
            ':triage_level' => $data['triage_level'] ?? null,
            ':status' => $data['status'] ?? 'open',
            ':attending_user_id' => $data['attending_user_id'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':created_by' => $data['created_by'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE encounters SET patient_id=:patient_id, encounter_date=:encounter_date, encounter_type=:encounter_type,
             reason_for_visit=:reason_for_visit, triage_level=:triage_level, status=:status, attending_user_id=:attending_user_id, notes=:notes
             WHERE id=:id'
        );
        return $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':encounter_date' => $data['encounter_date'] ?: date('Y-m-d H:i:s'),
            ':encounter_type' => $data['encounter_type'] ?? 'outpatient',
            ':reason_for_visit' => $data['reason_for_visit'] ?? null,
            ':triage_level' => $data['triage_level'] ?? null,
            ':status' => $data['status'] ?? 'open',
            ':attending_user_id' => $data['attending_user_id'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE encounters SET deleted_at = NOW() WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
