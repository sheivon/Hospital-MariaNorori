<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\Pages\LoginPageController;

LoginPageController::handle($_POST, $_SERVER['REQUEST_METHOD'] ?? 'GET');
