<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class DashboardPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Dashboard/Index.php');
    }

    public static function redirectLegacy(): void
    {
        header('Location: /');
        exit;
    }
}