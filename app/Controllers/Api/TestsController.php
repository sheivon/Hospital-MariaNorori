<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Services\TestService;
use App\Repositories\TestRepository;
use Exception;

class TestsController extends BaseApiController
{
    private static function service(): TestService
    {
        return new TestService(new TestRepository());
    }

    public static function index(array $query = []): void
    {
        Auth::requireLogin();
        self::success(['data' => self::service()->all($query)]);
    }

    public static function create(array $payload): void
    {
        Auth::requireLogin();
        try {
            $user = Auth::currentUser();
            if (!isset($payload['created_by']) && !empty($user['id'])) {
                $payload['created_by'] = (int)$user['id'];
            }
            $id = self::service()->create($payload);
            self::success(['id' => $id]);
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }

    public static function update(array $payload): void
    {
        Auth::requireLogin();
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
        Auth::requireLogin();
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

