<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Auth;
use App\Controllers\Api\DiagnosticsController;

Auth::requireLogin();
DiagnosticsController::show($_GET);
