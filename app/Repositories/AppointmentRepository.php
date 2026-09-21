<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class AppointmentRepository extends BaseRepository implements RepositoryInterface
{
    public function all(array $filters = []): array
    {
        $hasDeletedAt = $this->hasDeletedAtForTable('appointments');
        $sql = 'SELECT a.*,
                   TRIM(CONCAT(p.first_name, \' \', p.last_name)) AS patient_name,
                   u.fullname AS provider_name
             FROM appointments a
             LEFT JOIN patients p ON p.id = a.patient_id
             LEFT JOIN users u ON u.id = a.provider_user_id
             WHERE 1=1';
        $params = [];

        if ($hasDeletedAt) {
            $sql .= ' AND a.deleted_at IS NULL';
        }

        if (!empty($filters['patient_id'])) {
            $sql .= ' AND a.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($filters['provider_user_id'])) {
            $sql .= ' AND a.provider_user_id = :provider_user_id';
            $params[':provider_user_id'] = (int)$filters['provider_user_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND a.status = :status';
            $params[':status'] = (string)$filters['status'];
        }

        $sql .= ' ORDER BY a.appointment_at DESC, a.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function find(int $id): ?array
    {
        $hasDeletedAt = $this->hasDeletedAtForTable('appointments');
        $sql = 'SELECT a.*,
                   TRIM(CONCAT(p.first_name, \' \', p.last_name)) AS patient_name,
                   u.fullname AS provider_name
             FROM appointments a
             LEFT JOIN patients p ON p.id = a.patient_id
             LEFT JOIN users u ON u.id = a.provider_user_id
             WHERE a.id = :id';
        if ($hasDeletedAt) {
            $sql .= ' AND a.deleted_at IS NULL';
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
            'INSERT INTO appointments (patient_id, encounter_id, provider_user_id, appointment_at, reason, status, notes, created_by)
             VALUES (:patient_id, :encounter_id, :provider_user_id, :appointment_at, :reason, :status, :notes, :created_by)'
        );
        $stmt->execute([
            ':patient_id'       => $this->nullableInt($data['patient_id'] ?? null),
            ':encounter_id'     => $this->nullableInt($data['encounter_id'] ?? null),
            ':provider_user_id' => $this->nullableInt($data['provider_user_id'] ?? null),
            ':appointment_at'   => $this->normalizeDateTime($data['appointment_at'] ?? null),
            ':reason'           => $this->nullableString($data['reason'] ?? null),
            ':status'           => $this->nullableString($data['status'] ?? null) ?? 'scheduled',
            ':notes'            => $this->nullableString($data['notes'] ?? null),
            ':created_by'       => $this->nullableInt($data['created_by'] ?? null),
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE appointments
                SET patient_id       = :patient_id,
                    encounter_id     = :encounter_id,
                    provider_user_id = :provider_user_id,
                    appointment_at   = :appointment_at,
                    reason           = :reason,
                    status           = :status,
                    notes            = :notes
              WHERE id = :id'
        );
        return $stmt->execute([
            ':patient_id'       => $this->nullableInt($data['patient_id'] ?? null),
            ':encounter_id'     => $this->nullableInt($data['encounter_id'] ?? null),
            ':provider_user_id' => $this->nullableInt($data['provider_user_id'] ?? null),
            ':appointment_at'   => $this->normalizeDateTime($data['appointment_at'] ?? null),
            ':reason'           => $this->nullableString($data['reason'] ?? null),
            ':status'           => $this->nullableString($data['status'] ?? null) ?? 'scheduled',
            ':notes'            => $this->nullableString($data['notes'] ?? null),
            ':id'               => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        if ($this->hasDeletedAtForTable('appointments')) {
            $stmt = $this->pdo->prepare('UPDATE appointments SET deleted_at = NOW() WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        }

        $stmt = $this->pdo->prepare('DELETE FROM appointments WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Convert empty string or 'null' to NULL; otherwise cast to int.
     */
    private function nullableInt($value): ?int
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }
        return (int)$value;
    }

    /**
     * Convert empty string to NULL; otherwise trim and return as string.
     */
    private function nullableString($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim((string)$value);
        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * Accept 'YYYY-MM-DDTHH:MM' (HTML5 datetime-local) and 'YYYY-MM-DD HH:MM:SS',
     * convert to MySQL DATETIME. Return null if input is blank.
     */
    private function normalizeDateTime($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim((string)$value);
        if ($trimmed === '') {
            return null;
        }
        // HTML5 datetime-local: "2026-08-15T14:30"
        $normalized = str_replace('T', ' ', $trimmed);
        // Add seconds if missing
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $normalized) === 1) {
            $normalized .= ':00';
        }
        return $normalized;
    }
}
