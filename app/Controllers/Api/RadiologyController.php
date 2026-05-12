<?php

namespace App\Controllers\Api;

use App\Services\RadiologyService;
use App\Repositories\RadiologyRequestRepository;
use Exception;

class RadiologyController extends BaseApiController
{
    private static function service(): RadiologyService
    {
        return new RadiologyService(new RadiologyRequestRepository());
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

