<?php

namespace App\Controllers\Api;

use App\Core\ApiResponse;

abstract class BaseApiController
{
    protected static function success(array $data = [], int $statusCode = 200): void
    {
        ApiResponse::success($data, $statusCode);
    }

    protected static function fail(string $message, int $statusCode = 400, array $extra = []): void
    {
        ApiResponse::fail($message, $statusCode, $extra);
    }
}
