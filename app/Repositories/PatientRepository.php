<?php

namespace App\Repositories;

use App\Interfaces\PatientRepositoryInterface;
use PDO;

class PatientRepository extends \App\Repositories\BaseRepository implements PatientRepositoryInterface
{
    private ?bool $deletedAtExists = null;
    private ?bool $emergencyTableExists = null;

    protected function hasDeletedAt(): bool
    {
        return $this->hasDeletedAtForTable('patients');
    }

    protected function hasEmergencyTable(): bool
    {
        if ($this->emergencyTableExists !== null) {
            return $this->emergencyTableExists;
        }

        $stmt = $this->pdo->query("SHOW TABLES LIKE 'emergency_encounters'");
        $this->emergencyTableExists = (bool)$stmt->fetch();
        return $this->emergencyTableExists;
    }

    public function all(array $filters = []): array
    {
        $params = [];
        $where = [];

        if ($this->hasDeletedAt()) {
            $where[] = 'deleted_at IS NULL';
        }

        if (isset($filters['encountered']) && $filters['encountered'] !== '') {
            $where[] = 'encountered = :encountered';
            $params[':encountered'] = (int)$filters['encountered'];
        }

        if (!empty($filters['emergency_available']) && $this->hasEmergencyTable()) {
            // Exclude patients that still have an active emergency record.
            $where[] = "NOT EXISTS (
                SELECT 1
                FROM emergency_encounters ee
                WHERE ee.patient_id = patients.id
                  AND ee.discharge_date IS NULL
                  AND LOWER(TRIM(COALESCE(ee.status, 'active'))) NOT IN ('closed', 'cerrado', 'inactive', 'inactivo', 'discharged', 'alta')
            )";
        }

        $sql = 'SELECT id, first_name, last_name, email, cedula, dob, gender, phone, address, marital_status, insurance_provider, insurance_policy_no, father_name, mother_name, expediente_no, procedencia, education_level, employer, notes, created_at, updated_at FROM patients';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $deletedWhere = $this->hasDeletedAt() ? ' AND deleted_at IS NULL' : '';
        $stmt = $this->pdo->prepare('SELECT id, first_name, last_name, email, cedula, dob, gender, phone, address, marital_status, insurance_provider, insurance_policy_no, father_name, mother_name, expediente_no, procedencia, education_level, employer, notes, created_at, updated_at FROM patients WHERE id = :id' . $deletedWhere . ' LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO patients (first_name,last_name,email,cedula,dob,gender,phone,address,marital_status,insurance_provider,insurance_policy_no,father_name,mother_name,expediente_no,procedencia,education_level,employer,notes) VALUES (:fn,:ln,:email,:cedula,:dob,:gender,:phone,:address,:marital_status,:insurance_provider,:insurance_policy_no,:father_name,:mother_name,:expediente_no,:procedencia,:education_level,:employer,:notes)');
        $stmt->execute([
            ':fn' => $data['first_name'] ?? null,
            ':ln' => $data['last_name'] ?? null,
            ':email' => $data['email'] ?? null,
            ':cedula' => $data['cedula'] ?? null,
            ':dob' => !empty($data['dob']) ? $data['dob'] : null,
            ':gender' => $data['gender'] ?? 'O',
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':marital_status' => $data['marital_status'] ?? null,
            ':insurance_provider' => $data['insurance_provider'] ?? null,
            ':insurance_policy_no' => $data['insurance_policy_no'] ?? null,
            ':father_name' => $data['father_name'] ?? null,
            ':mother_name' => $data['mother_name'] ?? null,
            ':expediente_no' => $data['expediente_no'] ?? null,
            ':procedencia' => $data['procedencia'] ?? null,
            ':education_level' => $data['education_level'] ?? null,
            ':employer' => $data['employer'] ?? null,
            ':notes' => $data['notes'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare('UPDATE patients SET first_name=:fn,last_name=:ln,email=:email,cedula=:cedula,dob=:dob,gender=:gender,phone=:phone,address=:address,marital_status=:marital_status,insurance_provider=:insurance_provider,insurance_policy_no=:insurance_policy_no,father_name=:father_name,mother_name=:mother_name,expediente_no=:expediente_no,procedencia=:procedencia,education_level=:education_level,employer=:employer,notes=:notes WHERE id=:id');
        return $stmt->execute([
            ':fn' => $data['first_name'] ?? null,
            ':ln' => $data['last_name'] ?? null,
            ':email' => $data['email'] ?? null,
            ':cedula' => $data['cedula'] ?? null,
            ':dob' => !empty($data['dob']) ? $data['dob'] : null,
            ':gender' => $data['gender'] ?? 'O',
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':marital_status' => $data['marital_status'] ?? null,
            ':insurance_provider' => $data['insurance_provider'] ?? null,
            ':insurance_policy_no' => $data['insurance_policy_no'] ?? null,
            ':father_name' => $data['father_name'] ?? null,
            ':mother_name' => $data['mother_name'] ?? null,
            ':expediente_no' => $data['expediente_no'] ?? null,
            ':procedencia' => $data['procedencia'] ?? null,
            ':education_level' => $data['education_level'] ?? null,
            ':employer' => $data['employer'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        if ($this->hasDeletedAt()) {
            $stmt = $this->pdo->prepare('UPDATE patients SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        } else {
            $stmt = $this->pdo->prepare('DELETE FROM patients WHERE id = :id');
        }
        return $stmt->execute([':id' => $id]);
    }

    public function findByCedula(string $cedula, ?int $exceptId = null): ?array
    {
        $deletedWhere = $this->hasDeletedAt() ? ' AND deleted_at IS NULL' : '';
        if ($exceptId !== null && $exceptId > 0) {
            $stmt = $this->pdo->prepare('SELECT id FROM patients WHERE cedula = :cedula AND id != :id' . $deletedWhere . ' LIMIT 1');
            $stmt->execute([':cedula' => $cedula, ':id' => $exceptId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT id FROM patients WHERE cedula = :cedula' . $deletedWhere . ' LIMIT 1');
            $stmt->execute([':cedula' => $cedula]);
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}



