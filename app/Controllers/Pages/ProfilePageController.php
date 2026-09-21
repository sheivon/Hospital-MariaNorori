<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Core\Auth;
use App\Repositories\UserRepository;
use App\Services\ProfileService;

class ProfilePageController
{
    public static function handle(array $post, string $method): void
    {
        Auth::requireLogin();

        $user = Auth::currentUser() ?: [];
        $userId = (int)($user['id'] ?? 0);
        $users = new UserRepository();
        $profile = $users->findById($userId) ?: [];
        $errorKey = '';

        if ($method === 'POST') {
            $errorKey = (new ProfileService())->update($userId, $post);
            if ($errorKey === '') {
                header('Location: /profile.php');
                exit;
            }
            $profile = $users->findById($userId) ?: $profile;
        }

        include APP_ROOT . '/templates/header.php';
        include APP_ROOT . '/app/Views/Pages/Profile/Index.php';
    }
}