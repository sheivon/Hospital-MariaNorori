<?php
require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\PatientAllergiesController;

PatientAllergiesController::save($_POST);
