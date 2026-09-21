<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PrintService;
use App\Repositories\DiagnosticoRepository;
use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;
use App\Repositories\EncounterRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\PatientAllergyRepository;
use App\Repositories\ExamRequestRepository;
use Exception;

class PrintController
{
    private static function service(): PrintService
    {
        return new PrintService(
            new DiagnosticoRepository(),
            new PatientRepository(),
            new UserRepository(),
            new EncounterRepository(),
            new AppointmentRepository(),
            new PatientAllergyRepository(),
            new ExamRequestRepository()
        );
    }

    /**
     * Return data required to render a printable patient summary.
     *
     * @param int $patientId
     * @return array{patient: array<string,mixed>, diagnostics: array<array<string,mixed>>}
     * @throws Exception if patient does not exist.
     */
    public static function patient(int $patientId): array
    {
        return self::service()->patient($patientId);
    }

    /**
     * Return all patient data for the full record print view.
     *
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public static function patientFull(array $filters): array
    {
        return self::service()->patientFull($filters);
    }

    /**
     * Return rows and metadata for a print table by resource.
     *
     * @param string $resource
     * @return array{title:string,columns:array<array<string,string>>,rows:array<array<string,mixed>>}
     */
    public static function datatable(string $resource, array $filters = []): array
    {
        return self::service()->datatable($resource, $filters);
    }
}

