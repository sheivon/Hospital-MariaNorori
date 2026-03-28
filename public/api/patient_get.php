<?php
require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\ApiResponse;
use App\Models\PatientModel;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    ApiResponse::fail('Missing id');
}

try {
    $patient = (new PatientModel())->find($id);
    if (!$patient) {
        ApiResponse::fail('Patient not found');
    }
    ApiResponse::success(['patient' => $patient]);
} catch (\Throwable $e) {
    ApiResponse::fail($e->getMessage());
}
