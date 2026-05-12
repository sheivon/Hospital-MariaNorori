<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\AdolescentHistoriesController;

$payload = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
AdolescentHistoriesController::create($payload);

