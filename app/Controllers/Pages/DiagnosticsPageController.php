<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class DiagnosticsPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Diagnostics/Index.php');
    }
}