<?php

declare(strict_types=1);

namespace App\Controllers\Api\Admin;

use App\Core\Auth;
use App\Controllers\Api\BaseApiController;
use App\Services\Admin\UserService;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use Throwable;

class UsersController extends BaseApiController
{
    private static function service(): UserService
    {
        return new UserService(new UserRepository(), new UserRoleRepository());
    }

    public static function roles(): void
    {
        Auth::requireRole('admin');

        try {
            $roles = self::service()->getRoles();
            self::success(['data' => $roles]);
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function index(): void
    {
        Auth::requireRole('admin');

        try {
            $rows = self::service()->listUsers();
            self::success(['data' => $rows]);
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function create(array $payload): void
    {
        Auth::requireRole('admin');

        try {
            $id = self::service()->createUser($payload);
            self::success(['id' => $id]);
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function update(array $payload): void
    {
        Auth::requireRole('admin');

        $id = (int)($payload['id'] ?? 0);
        $me = Auth::currentUser();
        $currentUserId = $me ? (int)$me['id'] : 0;

        try {
            self::service()->updateUser($id, $payload, $currentUserId);
            self::success();
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function delete(array $payload): void
    {
        Auth::requireRole('admin');

        $id = (int)($payload['id'] ?? 0);
        $me = Auth::currentUser();
        $currentUserId = $me ? (int)$me['id'] : 0;

        try {
            self::service()->deleteUser($id, $currentUserId);
            self::success();
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }
}

