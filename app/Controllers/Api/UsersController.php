<?php

namespace App\Controllers\Api;

use App\Core\ApiResponse;
use App\Core\Auth;
use App\Models\UserModel;

class UsersController
{
    public static function listForChat(): void
    {
        Auth::requireLogin();
        $current = Auth::currentUser();
        $userModel = new UserModel();
        $rows = $userModel->listPublicExcept((int)$current['id']);
        ApiResponse::success(['data' => $rows]);
    }
}
