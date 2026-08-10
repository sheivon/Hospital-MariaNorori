<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\VitalsController;

VitalsController::index($_GET);
