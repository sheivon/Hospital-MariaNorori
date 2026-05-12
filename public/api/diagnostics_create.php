<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\DiagnosticsController;

$payload = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
DiagnosticsController::create($payload);

