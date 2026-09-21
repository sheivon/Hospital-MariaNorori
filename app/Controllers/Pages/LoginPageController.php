<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Core\Auth;
use App\Repositories\UserRepository;

class LoginPageController
{
    public static function handle(array $post, string $method): void
    {
        $errorKey = '';
        $hasUsers = (new UserRepository())->countActiveUsers() > 0;

        if ($method === 'POST') {
            $username = trim((string)($post['username'] ?? ''));
            $password = (string)($post['password'] ?? '');

            if (Auth::login($username, $password)) {
                header('Location: /');
                exit;
            }

            $errorKey = 'invalid_login';
        }

        include APP_ROOT . '/templates/login-header.php';
        include APP_ROOT . '/app/Views/Pages/Login/Index.php';
    }
}
