<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\TestsController;

$payload = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
TestsController::delete($payload);

