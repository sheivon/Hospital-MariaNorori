<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class AppointmentsPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('Appointments/Index.php', true, [], ['scripts' => ['patients', 'Appointments']]);
    }
}
