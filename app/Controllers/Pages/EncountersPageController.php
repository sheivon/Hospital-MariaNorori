<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class EncountersPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Encounters/Index.php');
    }
}