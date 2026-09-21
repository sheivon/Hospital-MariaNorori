<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class ChatHealthPageController extends BasePageController
{
    public static function index(): void
    {
        self::renderPage('ChatHealth/Index.php');
    }
}
