<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\MedicationsCatalogController;

MedicationsCatalogController::index($_GET);
