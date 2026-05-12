<?php

namespace App\Controllers\Api;

use App\Services\EmergencyService;
use App\Repositories\EmergencyEncounterRepository;
use Exception;

class EmergencyController extends BaseApiController
{
    private static function service(): EmergencyService
    {
        return new EmergencyService(new EmergencyEncounterRepository());
    }

    public static function index(array $query = []): void
    {
        self::success(['data' => self::service()->all($query)]);
    }

    public static function create(array $payload): void
    {
        try {
            $id = self::service()->create($payload);
            self::success(['id' => $id]);
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }
}

