<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\CedulaController;

CedulaController::check($_GET);

