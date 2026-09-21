<?php

namespace App\Helpers;

use App\Core\Auth;

class AuthHelper
{
    public static function bootSession(): void
    {
        Auth::bootSession();
    }

    public static function login(string $username, string $password)
    {
        return Auth::login($username, $password);
    }

    public static function requireLogin(): void
    {
        Auth::requireLogin();
    }

    public static function currentUser(): ?array
    {
        return Auth::currentUser();
    }

    public static function logout(): void
    {
        Auth::logout();
    }

    public static function requireRole(string $role): void
    {
        Auth::requireRole($role);
    }

    public static function isAdmin(): bool
    {
        return Auth::isAdmin();
    }
}
