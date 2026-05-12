<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\ChildFollowUpNotesController;

$payload = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
ChildFollowUpNotesController::create($payload);

