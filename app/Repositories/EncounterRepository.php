<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class EncounterRepository extends \App\Repositories\BaseRepository implements RepositoryInterface
{
    public function all(array $filters = []): array
    {
        $hasDeletedAt = $this->hasDeletedAtForTable('encounters');
        $sql = 'SELECT e.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, u.fullname AS attending_name
             FROM encounters e
             LEFT JOIN patients p ON p.id = e.patient_id
             LEFT JOIN users u ON u.id = e.attending_user_id
             WHERE 1=1';
        $params = [];

        if ($hasDeletedAt) {
            $sql .= ' AND e.deleted_at IS NULL';
        }

        if (!empty($filters['patient_id'])) {
            $sql .= ' AND e.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($filters['attending_user_id'])) {
            $sql .= ' AND e.attending_user_id = :attending_user_id';
            $params[':attending_user_id'] = (int)$filters['attending_user_id'];
        }

        if (!empty($filters['encounter_date'])) {
            $encounterDate = substr((string)$filters['encounter_date'], 0, 10);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $encounterDate) === 1) {
                $sql .= ' AND DATE(e.encounter_date) = :encounter_date';
                $params[':encounter_date'] = $encounterDate;
            }
        }

        $sql .= ' ORDER BY e.encounter_date DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $hasDeletedAt = $this->hasDeletedAtForTable('encounters');
        $sql =
            'SELECT e.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, u.fullname AS attending_name
             FROM encounters e
             LEFT JOIN patients p ON p.id = e.patient_id
             LEFT JOIN users u ON u.id = e.attending_user_id
             WHERE e.id = :id';

        if ($hasDeletedAt) {
            $sql .= ' AND e.deleted_at IS NULL';
        }

        $sql .= ' LIMIT 1';

        $stmt = $this->pdo->prepare(
            $sql
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function ensurePatientNotAlreadyEncountered(?int $patientId, ?int $currentEncounterId = null): void
    {
        if ($patientId === null) {
            return;
        }

        $stmt = $this->pdo->prepare('SELECT encountered FROM patients WHERE id = :patient_id LIMIT 1');
        $stmt->execute([':patient_id' => $patientId]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$patient) {
            throw new \RuntimeException('Patient not found');
        }

        if ((int)$patient['encountered'] === 1) {
            if ($currentEncounterId === null) {
                throw new \RuntimeException('Patient has already been encountered');
            }

            $existingEncounter = $this->find($currentEncounterId);
            if ($existingEncounter === null || (int)$existingEncounter['patient_id'] !== $patientId) {
                throw new \RuntimeException('Patient has already been encountered');
            }
        }
    }

    public function create(array $data): int
    {
        $patientId = isset($data['patient_id']) ? (int)$data['patient_id'] : null;
        $this->ensurePatientNotAlreadyEncountered($patientId);

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO encounters (patient_id, encounter_date, encounter_type, reason_for_visit, triage_level, status, attending_user_id, notes, created_by)
                 VALUES (:patient_id, :encounter_date, :encounter_type, :reason_for_visit, :triage_level, :status, :attending_user_id, :notes, :created_by)'
            );
            $stmt->execute([
                ':patient_id' => $patientId,
                ':encounter_date' => $data['encounter_date'] ?: date('Y-m-d H:i:s'),
                ':encounter_type' => $data['encounter_type'] ?? 'outpatient',
                ':reason_for_visit' => $data['reason_for_visit'] ?? null,
                ':triage_level' => $data['triage_level'] ?? null,
                ':status' => $data['status'] ?? 'open',
                ':attending_user_id' => isset($data['attending_user_id']) && $data['attending_user_id'] === 'null' ? null : ($data['attending_user_id'] ?? null),
                ':notes' => $data['notes'] ?? null,
                ':created_by' => $data['created_by'] ?? null,
            ]);
            $encounterId = (int)$this->pdo->lastInsertId();

            if (!empty($patientId)) {
                $update = $this->pdo->prepare('UPDATE patients SET encountered = 1 WHERE id = :patient_id');
                $update->execute([':patient_id' => $patientId]);
            }

            $this->pdo->commit();
            return $encounterId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        if (isset($data['patient_id'])) {
            $this->ensurePatientNotAlreadyEncountered((int)$data['patient_id'], $id);
        }

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
            ':attending_user_id' => isset($data['attending_user_id']) && $data['attending_user_id'] === 'null' ? null : ($data['attending_user_id'] ?? null),
            ':notes' => $data['notes'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        if ($this->hasDeletedAtForTable('encounters')) {
            $stmt = $this->pdo->prepare('UPDATE encounters SET deleted_at = NOW() WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        }

        $stmt = $this->pdo->prepare('DELETE FROM encounters WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}

