<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\ApiResponse;
use App\Controllers\PrintController;

$resource = trim(strtolower($_GET['resource'] ?? ''));
if (!$resource) {
    ApiResponse::fail('Missing resource');
}

$filters = [];
if (!empty($_GET['patient_id']) && ctype_digit(strval($_GET['patient_id']))) {
    $filters['patient_id'] = (int) $_GET['patient_id'];
}
if (!empty($_GET['encounter_id']) && ctype_digit(strval($_GET['encounter_id']))) {
    $filters['encounter_id'] = (int) $_GET['encounter_id'];
}

try {
    $payload = PrintController::datatable($resource, $filters);
    ApiResponse::success(['data' => $payload]);
} catch (Exception $e) {
    ApiResponse::fail($e->getMessage());
}
