<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\RadiologyController;

RadiologyController::index($_GET);

