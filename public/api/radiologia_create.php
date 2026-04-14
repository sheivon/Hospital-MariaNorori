<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Auth;
use App\Controllers\Api\RadiologyController;

Auth::requireLogin();
$payload = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
$user = Auth::currentUser();
$payload['created_by'] = $user['id'] ?? null;

RadiologyController::create($payload);
