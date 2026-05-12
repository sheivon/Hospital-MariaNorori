<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Repositories\ChildFollowUpRepository;
use App\Services\ChildFollowUpService;

class ChildFollowUpsController extends BaseApiController
{
    private static function service(): ChildFollowUpService
    {
        return new ChildFollowUpService(new ChildFollowUpRepository());
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
            $id = self::service()->createFollowUp($payload);
            self::success(['id' => $id]);
        } catch (\Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function update(int $id, array $payload): void
    {
        Auth::requireLogin();
        try {
            self::service()->update($id, $payload);
            self::success(['id' => $id]);
        } catch (\Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function delete(int $id): void
    {
        Auth::requireLogin();
        try {
            self::service()->delete($id);
            self::success(['id' => $id]);
        } catch (\Throwable $e) {
            self::fail($e->getMessage());
        }
    }
}
