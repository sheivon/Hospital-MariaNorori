<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class PrintPageController extends BasePageController
{
    public static function data(): void
    {
        self::renderPage('Print/Data.php', true, [
            'hideSidebar' => true,
            'hideLanguageSelect' => true,
        ]);
    }

    public static function followup(): void
    {
        self::renderPage('Print/Followup.php', true, [
            'hideSidebar' => true,
            'hideLanguageSelect' => true,
        ]);
    }

    public static function patientRecord(): void
    {
        self::renderPage('Print/PatientRecord.php', true, [
            'hideSidebar' => true,
            'hideLanguageSelect' => true,
        ]);
    }
}