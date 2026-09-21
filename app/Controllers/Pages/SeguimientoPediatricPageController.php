<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class SeguimientoPediatricPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('SeguimientoPediatric/Index.php');
    }
}