<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PatientAllergyRepository;

class PatientAllergyService
{
    public function __construct(private PatientAllergyRepository $allergies)
    {
    }

    public function list(?int $patientId = null): array
    {
        return $this->allergies->all($patientId);
    }

    public function get(int $id): ?array
    {
        return $this->allergies->find($id);
    }

    public function create(array $data): int
    {
        return $this->allergies->create($this->sanitize($data));
    }

    public function update(int $id, array $data): void
    {
        $this->allergies->update($id, $this->sanitize($data));
    }

    public function delete(int $id): void
    {
        $this->allergies->delete($id);
    }

    private function sanitize(array $data): array
    {
        return [
            'patient_id' => (int)($data['patient_id'] ?? 0),
            'allergen' => trim((string)($data['allergen'] ?? '')),
            'reaction' => trim((string)($data['reaction'] ?? '')),
            'severity' => trim((string)($data['severity'] ?? '')),
            'status' => trim((string)($data['status'] ?? 'active')),
            'noted_date' => (string)($data['noted_date'] ?? ''),
            'notes' => trim((string)($data['notes'] ?? '')),
        ];
    }
}