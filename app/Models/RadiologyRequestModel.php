<?php

namespace App\Models;

use App\Core\Database;
use App\Interfaces\RepositoryInterface;
use PDO;

class RadiologyRequestModel implements RepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT r.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, p.cedula
             FROM radiology_requests r
             LEFT JOIN patients p ON p.id = r.patient_id';
        $params = [];
        $conditions = [];

        if (!empty($filters['patient_id'])) {
            $conditions[] = 'r.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY r.request_date DESC, r.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM radiology_requests WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO radiology_requests (
                patient_id, unit, first_last_name, second_last_name, names, insured, gender, age, request_date,
                clinic_bed, service, code, prior_radiograph, prior_radiograph_code, exam_requested, clinical_data,
                evolution_time, presumptive_diagnosis, observations, doctor_code, technician, plates_used, findings,
                conclusions, radiology_date, radiographs_archived, radiograph_count, dictating_doctor_code,
                status, created_by
            ) VALUES (
                :patient_id, :unit, :first_last_name, :second_last_name, :names, :insured, :gender, :age, :request_date,
                :clinic_bed, :service, :code, :prior_radiograph, :prior_radiograph_code, :exam_requested, :clinical_data,
                :evolution_time, :presumptive_diagnosis, :observations, :doctor_code, :technician, :plates_used, :findings,
                :conclusions, :radiology_date, :radiographs_archived, :radiograph_count, :dictating_doctor_code,
                :status, :created_by
            )'
        );

        $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':unit' => $data['unit'] ?? null,
            ':first_last_name' => $data['first_last_name'] ?? null,
            ':second_last_name' => $data['second_last_name'] ?? null,
            ':names' => $data['names'] ?? null,
            ':insured' => $data['insured'] ?? null,
            ':gender' => $data['gender'] ?? null,
            ':age' => $data['age'] ?? null,
            ':request_date' => $data['request_date'] ?? null,
            ':clinic_bed' => $data['clinic_bed'] ?? null,
            ':service' => $data['service'] ?? null,
            ':code' => $data['code'] ?? null,
            ':prior_radiograph' => $data['prior_radiograph'] ?? null,
            ':prior_radiograph_code' => $data['prior_radiograph_code'] ?? null,
            ':exam_requested' => $data['exam_requested'] ?? null,
            ':clinical_data' => $data['clinical_data'] ?? null,
            ':evolution_time' => $data['evolution_time'] ?? null,
            ':presumptive_diagnosis' => $data['presumptive_diagnosis'] ?? null,
            ':observations' => $data['observations'] ?? null,
            ':doctor_code' => $data['doctor_code'] ?? null,
            ':technician' => $data['technician'] ?? null,
            ':plates_used' => $data['plates_used'] ?? null,
            ':findings' => $data['findings'] ?? null,
            ':conclusions' => $data['conclusions'] ?? null,
            ':radiology_date' => $data['radiology_date'] ?? null,
            ':radiographs_archived' => $data['radiographs_archived'] ?? null,
            ':radiograph_count' => $data['radiograph_count'] ?? null,
            ':dictating_doctor_code' => $data['dictating_doctor_code'] ?? null,
            ':status' => $data['status'] ?? 'pending',
            ':created_by' => $data['created_by'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        // For now, only allow status update and notes updates.
        $stmt = $this->pdo->prepare(
            'UPDATE radiology_requests SET status = :status, observations = :observations WHERE id = :id'
        );
        return $stmt->execute([
            ':status' => $data['status'] ?? 'pending',
            ':observations' => $data['observations'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM radiology_requests WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
