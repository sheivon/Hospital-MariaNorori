<?php

namespace App\Models;

use App\Core\Database;
use App\Interfaces\RepositoryInterface;
use PDO;

class AdolescentHistoryModel implements RepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    private function encodeFormData(array $data): string
    {
        $formData = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $formData[$key] = $value;
            }
        }

        return json_encode($formData, JSON_UNESCAPED_UNICODE);
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT h.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, p.cedula
            FROM adolescent_clinical_histories h
            LEFT JOIN patients p ON p.id = h.patient_id';

        $params = [];
        $conditions = [];

        if (!empty($filters['patient_id'])) {
            $conditions[] = 'h.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY h.visit_date DESC, h.id DESC';

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
        $stmt = $this->pdo->prepare('SELECT * FROM adolescent_clinical_histories WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $row['form_data'] = json_decode($row['form_data'] ?? '{}', true) ?: [];
        return $row;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO adolescent_clinical_histories (
                patient_id, encounter_id, visit_date, reason_for_consultation, personal_pathological_history,
                risk_factors, family_pathological_history, family_environment, education_work_living,
                activities_social, physical_activity, notes, form_data, created_by
            ) VALUES (
                :patient_id, :encounter_id, :visit_date, :reason_for_consultation, :personal_pathological_history,
                :risk_factors, :family_pathological_history, :family_environment, :education_work_living,
                :activities_social, :physical_activity, :notes, :form_data, :created_by
            )'
        );

        $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':encounter_id' => $data['encounter_id'] ?? null,
            ':visit_date' => $data['visit_date'] ?? null,
            ':reason_for_consultation' => $data['reason_for_consultation'] ?? null,
            ':personal_pathological_history' => $data['personal_pathological_history'] ?? null,
            ':risk_factors' => $data['risk_factors'] ?? null,
            ':family_pathological_history' => $data['family_pathological_history'] ?? null,
            ':family_environment' => $data['family_environment'] ?? null,
            ':education_work_living' => $data['education_work_living'] ?? null,
            ':activities_social' => $data['activities_social'] ?? null,
            ':physical_activity' => $data['physical_activity'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':form_data' => $this->encodeFormData($data),
            ':created_by' => $data['created_by'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE adolescent_clinical_histories SET
                visit_date = :visit_date,
                reason_for_consultation = :reason_for_consultation,
                personal_pathological_history = :personal_pathological_history,
                risk_factors = :risk_factors,
                family_pathological_history = :family_pathological_history,
                family_environment = :family_environment,
                education_work_living = :education_work_living,
                activities_social = :activities_social,
                physical_activity = :physical_activity,
                notes = :notes,
                form_data = :form_data,
                updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            ':visit_date' => $data['visit_date'] ?? null,
            ':reason_for_consultation' => $data['reason_for_consultation'] ?? null,
            ':personal_pathological_history' => $data['personal_pathological_history'] ?? null,
            ':risk_factors' => $data['risk_factors'] ?? null,
            ':family_pathological_history' => $data['family_pathological_history'] ?? null,
            ':family_environment' => $data['family_environment'] ?? null,
            ':education_work_living' => $data['education_work_living'] ?? null,
            ':activities_social' => $data['activities_social'] ?? null,
            ':physical_activity' => $data['physical_activity'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':form_data' => $this->encodeFormData($data),
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM adolescent_clinical_histories WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
