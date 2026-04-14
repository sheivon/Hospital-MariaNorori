<?php

namespace App\Controllers\Api;

use App\Services\TestService;
use App\Models\TestModel;
use Exception;

class TestsController extends BaseApiController
{
    private static function service(): TestService
    {
        return new TestService(new TestModel());
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
