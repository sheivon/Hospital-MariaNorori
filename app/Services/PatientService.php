<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\PatientRepositoryInterface;
use Exception;

class PatientService extends BaseService
{
    public function __construct(PatientRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getAllPatients(array $filters = []): array
    {
        return $this->repository->all($filters);
    }

    public function getPatient(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function createPatient(array $data): int
    {
        $this->validatePayload($data);
        return $this->repository->create($data);
    }

    public function updatePatient(int $id, array $data): bool
    {
        $this->ensureValidId($id, 'patient id');
        $this->validatePayload($data, $id);
        return $this->repository->update($id, $data);
    }

    public function deletePatient(int $id): bool
    {
        $this->ensureValidId($id, 'patient id');
        return $this->repository->delete($id);
    }

    private function validatePayload(array $data, ?int $exceptId = null): void
    {
        $cedula = trim((string)($data['cedula'] ?? ''));
        if ($cedula !== '') {
            $existing = $this->repository->findByCedula($cedula, $exceptId);
            if ($existing !== null) {
                throw new Exception('Cédula already in use');
            }
        }

        $email = trim((string)($data['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email');
        }
    }
}
