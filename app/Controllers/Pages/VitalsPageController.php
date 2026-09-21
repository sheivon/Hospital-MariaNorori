<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class VitalsPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Vitals/Index.php');
    }
}