<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\CedulaController;

$payload = $_GET ?: $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
CedulaController::check($payload);
