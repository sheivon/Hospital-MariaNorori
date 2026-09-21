<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Services\StatsService;
use Throwable;

class StatsController extends BaseApiController
{
    private static function service(): StatsService
    {
        return new StatsService();
    }

    public static function overview(): void
    {
        Auth::requireLogin();

        try {
            self::success(self::service()->overview());
        } catch (Throwable $e) {
            self::fail($e->getMessage(), 500);
        }
    }
}
