<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\ChildFollowUpsController;

$payload = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
ChildFollowUpsController::create($payload);

