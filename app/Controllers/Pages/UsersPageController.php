<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Core\Auth;

class UsersPageController extends BasePageController
{
    public static function index(): void
    {
        Auth::requireLogin();
        Auth::requireRole('admin');
        self::renderPage('Users/Index.php', false, [], ['scripts' => ['users']]);
    }
}