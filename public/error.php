<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\ErrorController;

$code = isset($_GET['code']) ? (int)$_GET['code'] : 404;
ErrorController::render($code);
