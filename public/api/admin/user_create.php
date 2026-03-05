<?php

require_once __DIR__ . '/../../../app/bootstrap.php';

use App\Controllers\Api\Admin\UsersController;

UsersController::create($_POST);
