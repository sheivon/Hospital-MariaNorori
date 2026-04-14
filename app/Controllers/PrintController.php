<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PrintService;
use App\Models\DiagnosticoModel;
use App\Models\PatientModel;
use App\Models\UserModel;
use App\Models\EncounterModel;
use Exception;

class PrintController
{
    private static function service(): PrintService
    {
        return new PrintService(
            new DiagnosticoModel(),
            new PatientModel(),
            new UserModel(),
            new EncounterModel()
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
     * Return rows and metadata for a print table by resource.
     *
     * @param string $resource
     * @return array{title:string,columns:array<array<string,string>>,rows:array<array<string,mixed>>}
     */
    public static function datatable(string $resource): array
    {
        return self::service()->datatable($resource);
    }
}
