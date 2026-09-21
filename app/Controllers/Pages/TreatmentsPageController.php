<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class TreatmentsPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Treatments/Index.php');
    }
}