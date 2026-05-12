<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\ExamRequestsController;

$payload = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
ExamRequestsController::delete($payload);

