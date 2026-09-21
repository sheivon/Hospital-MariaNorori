<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class MedicationsPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Medications/Index.php');
    }
}
