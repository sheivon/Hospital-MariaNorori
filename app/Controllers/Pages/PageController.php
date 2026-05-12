<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Core\Auth;

class PageController
{
    private static function render(string $viewPath, bool $requireLogin = true, array $layout = []): void
    {
        if ($requireLogin) {
            Auth::requireLogin();
        }

        $hideSidebar = (bool)($layout['hideSidebar'] ?? false);
        $hideLanguageSelect = (bool)($layout['hideLanguageSelect'] ?? false);

        include APP_ROOT . '/templates/header.php';
        include APP_ROOT . '/app/Views/Pages/' . $viewPath;
        include APP_ROOT . '/templates/footer.php';
    }

    public static function dashboard(): void
    {
        self::render('Dashboard/Index.php');
    }

    public static function reports(): void
    {
        self::render('Reports/Index.php');
    }

    public static function childFollowups(): void
    {
        self::render('ChildFollowups/Index.php');
    }

    public static function printData(): void
    {
        self::render('Print/Data.php', true, [
            'hideSidebar' => true,
            'hideLanguageSelect' => true,
        ]);
    }

    public static function printFollowup(): void
    {
        self::render('Print/Followup.php', true, [
            'hideSidebar' => true,
            'hideLanguageSelect' => true,
        ]);
    }
}
