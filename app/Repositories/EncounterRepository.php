<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class EncounterRepository extends \App\Repositories\BaseRepository implements RepositoryInterface
{
    public function all(array $filters = []): array
    {
        $hasDeletedAt = $this->hasDeletedAtForTable('encounters');
        $sql = 'SELECT e.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, p.cedula, u.fullname AS attending_name
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

        if (!empty($filters['encounter_type'])) {
            $sql .= ' AND e.encounter_type = :encounter_type';
            $params[':encounter_type'] = (string)$filters['encounter_type'];
        }

        if (!empty($filters['encounter_date'])) {
            $encounterDate = substr((string)$filters['encounter_date'], 0, 10);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $encounterDate) === 1) {
                $sql .= ' AND DATE(e.encounter_date) = :encounter_date';
                $params[':encounter_date'] = $encounterDate;
            }
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND e.status = :status';
            $params[':status'] = (string)$filters['status'];
        }

        if (!empty($filters['status_not'])) {
            $sql .= ' AND e.status <> :status_not';
            $params[':status_not'] = (string)$filters['status_not'];
        }

        $sql .= ' ORDER BY e.encounter_date DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode form_data JSON for emergency rows (back-compat with
        // the old emergency_encounters table shape).
        foreach ($rows as &$row) {
            if (!empty($row['form_data'])) {
                $decoded = json_decode($row['form_data'], true);
                if (is_array($decoded)) {
                    $row['form_data_decoded'] = $decoded;
                }
            }
        }

        return $rows;
    }

    /**
     * Historical report: every encounter joined with its patient and attending
     * doctor, optionally narrowed to one patient and/or a date window.
     * Empty filters return the complete longitudinal history for all patients.
     *
     * @param array{patient_id?:int,date_from?:string,date_to?:string} $filters
     * @return array<array<string,mixed>>
     */
    public function historyReport(array $filters = []): array
    {
        $sql = 'SELECT e.id, e.patient_id, e.encounter_date, e.encounter_type,
                       e.triage_level, e.status, e.reason_for_visit, e.notes,
                       p.first_name, p.last_name, p.cedula, p.dob,
                       u.fullname AS attending_name';
        $params = [];

        // Per-encounter activity counts. Guarded on table presence so the
        // report still renders on schemas created before these tables.
        foreach (['diagnostics', 'tests', 'vitals'] as $countTable) {
            $alias = substr($countTable, 0, 1);
            if (!$this->tableExists($countTable)) {
                $sql .= ", 0 AS {$countTable}_count";
                continue;
            }
            $softDelete = $this->hasDeletedAtForTable($countTable)
                ? " AND {$alias}.deleted_at IS NULL"
                : '';
            $sql .= ", (SELECT COUNT(*) FROM {$countTable} {$alias}
                        WHERE {$alias}.encounter_id = e.id{$softDelete}) AS {$countTable}_count";
        }

        $sql .= '
             FROM encounters e
             LEFT JOIN patients p ON p.id = e.patient_id
             LEFT JOIN users u ON u.id = e.attending_user_id
             WHERE 1=1';

        if ($this->hasDeletedAtForTable('encounters')) {
            $sql .= ' AND e.deleted_at IS NULL';
        }

        if (!empty($filters['patient_id'])) {
            $sql .= ' AND e.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        foreach (['date_from' => 'e.encounter_date >= :date_from', 'date_to' => 'e.encounter_date < :date_to + INTERVAL 1 DAY'] as $key => $clause) {
            $value = substr((string)($filters[$key] ?? ''), 0, 10);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
                $sql .= ' AND ' . $clause;
                $params[':' . $key] = $value;
            }
        }

        // Chronological per patient so the printout reads as a timeline.
        $sql .= ' ORDER BY p.first_name ASC, p.last_name ASC, e.encounter_date ASC, e.id ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function tableExists(string $table): bool
    {
        $stmt = $this->pdo->prepare('SHOW TABLES LIKE :table');
        $stmt->execute([':table' => $table]);
        return (bool)$stmt->fetchColumn();
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

        $stmt = $this->pdo->prepare('SELECT id FROM patients WHERE id = :patient_id LIMIT 1');
        $stmt->execute([':patient_id' => $patientId]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$patient) {
            throw new \RuntimeException('Patient not found');
        }

        $hasDeletedAt = $this->hasDeletedAtForTable('encounters');
        $sql = 'SELECT id FROM encounters WHERE patient_id = :patient_id';
        if ($hasDeletedAt) {
            $sql .= ' AND deleted_at IS NULL';
        }
        $existingStatement = $this->pdo->prepare($sql . ' LIMIT 1');
        $existingStatement->execute([':patient_id' => $patientId]);

        if ($existingStatement->fetch(PDO::FETCH_ASSOC)) {
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
                'INSERT INTO encounters (patient_id, encounter_date, encounter_type, reason_for_visit, triage_level, status, attending_user_id, notes, admission_date, discharge_date, emergency_status, form_data, created_by)
                 VALUES (:patient_id, :encounter_date, :encounter_type, :reason_for_visit, :triage_level, :status, :attending_user_id, :notes, :admission_date, :discharge_date, :emergency_status, :form_data, :created_by)'
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
                ':admission_date' => $data['admission_date'] ?? null,
                ':discharge_date' => $data['discharge_date'] ?? null,
                ':emergency_status' => $data['emergency_status'] ?? null,
                ':form_data' => $data['form_data'] ?? null,
                ':created_by' => $data['created_by'] ?? null,
            ]);
            $encounterId = (int)$this->pdo->lastInsertId();

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

        $allowed = [
            'patient_id',
            'encounter_date',
            'encounter_type',
            'reason_for_visit',
            'triage_level',
            'status',
            'attending_user_id',
            'notes',
            'admission_date',
            'discharge_date',
            'emergency_status',
            'form_data',
        ];
        $fields = array_values(array_intersect($allowed, array_keys($data)));
        if (empty($fields)) {
            return false;
        }

        $params = [':id' => $id];
        $sets = [];
        foreach ($fields as $field) {
            $sets[] = "$field = :$field";
            $value = $data[$field];
            if ($field === 'attending_user_id' && $value === 'null') {
                $value = null;
            }
            $params[":$field"] = $value;
        }

        $sql = 'UPDATE encounters SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
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

