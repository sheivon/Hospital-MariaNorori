<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class AltasPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Altas/Index.php');
    }
}
