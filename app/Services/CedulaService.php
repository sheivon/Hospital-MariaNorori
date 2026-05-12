<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PatientRepository;

class CedulaService
{
    private PatientRepository $PatientRepository;

    public function __construct(PatientRepository $PatientRepository)
    {
        $this->PatientRepository = $PatientRepository;
    }

    public function isCedulaAvailable(string $cedula, ?int $exceptId = null): bool
    {
        $existing = $this->PatientRepository->findByCedula($cedula, $exceptId);
        return $existing === null;
    }
}

