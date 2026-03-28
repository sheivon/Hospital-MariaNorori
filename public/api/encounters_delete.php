<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\EncountersController;

EncountersController::delete($_POST);
