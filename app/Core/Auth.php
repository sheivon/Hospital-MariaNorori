<?php

namespace App\Core;

use App\Models\UserModel;

class Auth
{
    private static bool $sessionBooted = false;

    public static function bootSession(): void
    {
        if (self::$sessionBooted) {
            return;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        self::$sessionBooted = true;
    }

    public static function login(string $username, string $password): bool
    {
        self::bootSession();
        $userModel = new UserModel();
        $user = $userModel->findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'fullname' => $user['fullname'] ?? $user['username'],
            'role' => $user['role'] ?? 'user',
        ];

        return true;
    }

    public static function requireLogin(): void
    {
        self::bootSession();
        if (!empty($_SESSION['user'])) {
            return;
        }

        if (self::isApiRequest()) {
            ApiResponse::fail('Unauthorized', 401);
        }

        header('Location: /login.php');
        exit;
    }

    public static function currentUser(): ?array
    {
        self::bootSession();
        return $_SESSION['user'] ?? null;
    }

    public static function logout(): void
    {
        self::bootSession();
        session_unset();
        session_destroy();
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();
        $user = self::currentUser();
        if (!$user || strtolower((string)($user['role'] ?? 'user')) !== strtolower($role)) {
            if (self::isApiRequest()) {
                ApiResponse::fail('Forbidden', 403);
            }
            http_response_code(403);
            echo '<div style="padding:2rem; font-family: system-ui, sans-serif;">Access denied</div>';
            exit;
        }
    }

    public static function isAdmin(): bool
    {
        $user = self::currentUser();
        return (bool)($user && strtolower((string)($user['role'] ?? 'user')) === 'admin');
    }

    private static function isApiRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return strpos($uri, '/api/') === 0;
    }
}
