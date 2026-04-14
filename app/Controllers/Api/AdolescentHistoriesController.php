<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Models\AdolescentHistoryModel;
use App\Services\AdolescentHistoryService;

class AdolescentHistoriesController extends BaseApiController
{
    private static function service(): AdolescentHistoryService
    {
        return new AdolescentHistoryService(new AdolescentHistoryModel());
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
            $payload['created_by'] = $user['id'] ?? null;
            $id = self::service()->createHistory($payload);
            self::success(['id' => $id]);
        } catch (\Throwable $e) {
            self::fail($e->getMessage());
        }
    }
}
