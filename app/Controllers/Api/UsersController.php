<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Services\UserService;
use App\Repositories\UserRepository;

class UsersController extends BaseApiController
{
    private static function service(): UserService
    {
        return new UserService(new UserRepository());
    }

    public static function listForChat(): void
    {
        Auth::requireLogin();
        $current = Auth::currentUser();
        $role = isset($_GET['role']) && is_string($_GET['role']) && $_GET['role'] !== ''
            ? trim($_GET['role'])
            : null;
        $rows = self::service()->listUsersForChat((int)$current['id'], $role);
        self::success(['data' => $rows]);
    }
}
