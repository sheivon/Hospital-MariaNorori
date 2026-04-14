<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Auth;
use App\Controllers\Api\RadiologyController;

Auth::requireLogin();
RadiologyController::index($_GET);
