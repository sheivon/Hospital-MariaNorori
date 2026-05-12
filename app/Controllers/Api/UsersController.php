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
        $rows = self::service()->listUsersForChat((int)$current['id']);
        self::success(['data' => $rows]);
    }
}
