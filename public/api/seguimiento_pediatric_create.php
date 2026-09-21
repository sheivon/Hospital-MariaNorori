<?php
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Controllers\Api\SeguimientoPediatricController;
use App\Repositories\SeguimientoPediatricRepository;

$db = new \App\Core\Database();
$repo = new SeguimientoPediatricRepository($db->getConnection());
$controller = new SeguimientoPediatricController($repo);
$controller->create();