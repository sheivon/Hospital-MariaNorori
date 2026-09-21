<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Core\Auth;
use App\Repositories\UserRepository;
use App\Services\RegistrationService;

class RegistrationPageController
{
    public static function handle(array $post, string $method): void
    {
        $errorKey = '';
        $hasUsers = (new UserRepository())->countActiveUsers() > 0;

        if ($method === 'POST') {
            $username = trim((string)($post['username'] ?? ''));
            $password = (string)($post['password'] ?? '');
            $fullname = trim((string)($post['fullname'] ?? ''));
            $cedula = trim((string)($post['cedula'] ?? ''));

            $errorKey = (new RegistrationService(new UserRepository()))->register($username, $password, $fullname, $cedula);

            if ($errorKey === null) {
                if (Auth::login($username, $password)) {
                    header('Location: /');
                    exit;
                }
                $errorKey = 'auto_login_failed';
            }
        }

        include APP_ROOT . '/templates/login-header.php';
        include APP_ROOT . '/app/Views/Pages/Login/Index.php';
    }
}