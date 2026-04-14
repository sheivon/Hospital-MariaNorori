<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\RepositoryInterface;
use Exception;

class AdolescentHistoryService extends BaseService
{
    public function createHistory(array $data): int
    {
        $patientId = isset($data['patient_id']) ? (int)$data['patient_id'] : 0;
        if ($patientId <= 0) {
            throw new Exception('Patient is required');
        }

        $visitDate = trim((string)($data['visit_date'] ?? ''));
        if ($visitDate === '') {
            throw new Exception('Visit date is required');
        }

        return $this->repository->create($data);
    }
}
