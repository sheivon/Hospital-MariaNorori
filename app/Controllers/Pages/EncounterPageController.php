<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class EncounterPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Encounter/Index.php');
    }
}