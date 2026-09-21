<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

class LogoutController
{
    public static function handle(): void
    {
        Auth::logout();
        header('Location: /login.php');
        exit;
    }
}
