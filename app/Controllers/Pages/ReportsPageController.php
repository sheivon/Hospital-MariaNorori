<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class ReportsPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Reports/Index.php');
    }
}
