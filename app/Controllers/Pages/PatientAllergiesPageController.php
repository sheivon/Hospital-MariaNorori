<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class PatientAllergiesPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('PatientAllergies/Index.php');
    }
}
