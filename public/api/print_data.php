<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\ApiResponse;
use App\Controllers\PrintController;

$resource = trim(strtolower($_GET['resource'] ?? ''));
if (!$resource) {
    ApiResponse::fail('Missing resource');
}

try {
    $payload = PrintController::datatable($resource);
    ApiResponse::success(['data' => $payload]);
} catch (Exception $e) {
    ApiResponse::fail($e->getMessage());
}
