<?php

namespace App\Controllers\Api;

use App\Repositories\EncounterRepository;
use App\Services\EncounterService;
use Exception;

class EncountersController extends BaseApiController
{
    private static function service(): EncounterService
    {
        return new EncounterService(new EncounterRepository());
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

    public static function update(array $payload): void
    {
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        try {
            self::service()->update($id, $payload);
            self::success();
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }

    public static function delete(array $payload): void
    {
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        try {
            self::service()->delete($id);
            self::success();
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }
}

