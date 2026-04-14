<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Auth;
use App\Controllers\Api\ExamRequestsController;

Auth::requireLogin();
ExamRequestsController::index($_GET);
