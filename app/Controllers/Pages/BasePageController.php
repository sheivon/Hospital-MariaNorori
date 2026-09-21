<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Core\Auth;

class BasePageController
{
    protected static function renderPage(string $viewPath, bool $requireLogin = true, array $layout = []): void
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
}