<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Auth;
use App\Controllers\Api\EmergencyController;

Auth::requireLogin();
EmergencyController::index($_GET);
