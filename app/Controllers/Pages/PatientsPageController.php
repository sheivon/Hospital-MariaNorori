<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class PatientsPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Patients/Index.php', true);
    }
}
