<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PatientModel;

class CedulaService
{
    private PatientModel $patientModel;

    public function __construct(PatientModel $patientModel)
    {
        $this->patientModel = $patientModel;
    }

    public function isCedulaAvailable(string $cedula, ?int $exceptId = null): bool
    {
        $existing = $this->patientModel->findByCedula($cedula, $exceptId);
        return $existing === null;
    }
}
