<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class EmergencyEncounterRepository extends \App\Repositories\BaseRepository implements RepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    private function ensureTableExists(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS emergency_encounters (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                patient_id INT UNSIGNED NOT NULL,
                encounter_id INT UNSIGNED DEFAULT NULL,
                admission_date DATE NOT NULL,
                discharge_date DATE DEFAULT NULL,
                status VARCHAR(50) DEFAULT "Activo",
                form_data TEXT DEFAULT NULL,
                created_by INT UNSIGNED DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_emergency_encounters_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
                CONSTRAINT fk_emergency_encounters_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
                CONSTRAINT fk_emergency_encounters_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_emergency_encounters_patient (patient_id),
                INDEX idx_emergency_encounters_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
        );
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT e.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, p.cedula
             FROM emergency_encounters e
             LEFT JOIN patients p ON p.id = e.patient_id';
        $params = [];
        $conditions = [];

        if (!empty($filters['patient_id'])) {
            $conditions[] = 'e.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($filters['encounter_id'])) {
            $conditions[] = 'e.encounter_id = :encounter_id';
            $params[':encounter_id'] = (int)$filters['encounter_id'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY e.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['form_data'] = json_decode($row['form_data'] ?? '{}', true) ?: [];
        }

        return $rows;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM emergency_encounters WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $row['form_data'] = json_decode($row['form_data'] ?? '{}', true) ?: [];
        }
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO emergency_encounters (patient_id, encounter_id, admission_date, discharge_date, status, form_data, created_by)
             VALUES (:patient_id, :encounter_id, :admission_date, :discharge_date, :status, :form_data, :created_by)'
        );
        $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':encounter_id' => $data['encounter_id'] ?? null,
            ':admission_date' => $data['admission_date'] ?? null,
            ':discharge_date' => $data['discharge_date'] ?? null,
            ':status' => $data['status'] ?? 'active',
            ':form_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ':created_by' => $data['created_by'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare('UPDATE emergency_encounters SET status = :status, form_data = :form_data WHERE id = :id');
        return $stmt->execute([
            ':status' => $data['status'] ?? 'active',
            ':form_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM emergency_encounters WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}

