<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\ChildFollowUpsController;

$payload = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
$id = isset($payload['id']) ? (int)$payload['id'] : 0;
ChildFollowUpsController::update($id, $payload);

