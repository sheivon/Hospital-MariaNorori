<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\PatientsController;

PatientsController::create($_POST);
