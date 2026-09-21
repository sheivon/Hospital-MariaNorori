<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\Pages\ProfilePageController;

ProfilePageController::handle($_POST, $_SERVER['REQUEST_METHOD'] ?? 'GET');
