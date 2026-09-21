<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class PatientAllergyRepository extends BaseRepository
{
    public function all(?int $patientId = null): array
    {
        $sql = 'SELECT a.id, a.patient_id, CONCAT(IFNULL(p.first_name, \'\'), \' \', IFNULL(p.last_name, \'\')) AS patient_name, a.allergen, a.reaction, a.severity, a.status, IFNULL(a.noted_date, \'\') AS noted_date, IFNULL(a.notes, \'\') AS notes
            FROM patient_allergies a
            LEFT JOIN patients p ON p.id = a.patient_id';
        $params = [];

        if ($patientId !== null && $patientId > 0) {
            $sql .= ' WHERE a.patient_id = :patient_id';
            $params[':patient_id'] = $patientId;
        }

        $sql .= ' ORDER BY a.id DESC';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT id, patient_id, allergen, reaction, severity, status, noted_date, notes FROM patient_allergies WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $allergy = $statement->fetch(PDO::FETCH_ASSOC);
        return $allergy ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare('INSERT INTO patient_allergies (patient_id, allergen, reaction, severity, status, noted_date, notes, created_at) VALUES (:patient_id, :allergen, :reaction, :severity, :status, :noted_date, :notes, NOW())');
        $statement->execute($this->params($data));
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $params = $this->params($data);
        $params[':id'] = $id;
        $statement = $this->pdo->prepare('UPDATE patient_allergies SET patient_id = :patient_id, allergen = :allergen, reaction = :reaction, severity = :severity, status = :status, noted_date = :noted_date, notes = :notes, updated_at = NOW() WHERE id = :id');
        $statement->execute($params);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM patient_allergies WHERE id = :id');
        $statement->execute([':id' => $id]);
    }

    private function params(array $data): array
    {
        return [
            ':patient_id' => (int)$data['patient_id'],
            ':allergen' => (string)$data['allergen'],
            ':reaction' => (string)$data['reaction'],
            ':severity' => (string)$data['severity'],
            ':status' => (string)$data['status'],
            ':noted_date' => $data['noted_date'] !== '' ? $data['noted_date'] : null,
            ':notes' => (string)$data['notes'],
        ];
    }
}
