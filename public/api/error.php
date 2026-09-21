<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\ErrorController;

$code = isset($_GET['code']) ? (int)$_GET['code'] : 500;
ErrorController::json($code);
