<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\ApiResponse;
use App\Services\CedulaService;
use App\Models\PatientModel;

class CedulaController extends BaseApiController
{
    private static function service(): CedulaService
    {
        return new CedulaService(new PatientModel());
    }

    public static function check(array $query): void
    {
        Auth::requireLogin();

        $cedula = trim((string)($query['cedula'] ?? ''));
        $exceptId = isset($query['id']) ? (int)$query['id'] : null;

        if ($cedula === '') {
            self::fail('Empty cedula');
        }

        self::success(['available' => self::service()->isCedulaAvailable($cedula, $exceptId)]);
    }
}
