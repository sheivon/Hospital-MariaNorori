<?php

require_once __DIR__ . '/../../../app/bootstrap.php';

use App\Controllers\Api\Admin\TableCrudController;

$payload = json_decode(file_get_contents('php://input'), true) ?: $_POST ?: [];
TableCrudController::delete($payload);
