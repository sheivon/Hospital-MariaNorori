<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\ChatController;

ChatController::send($_POST);
