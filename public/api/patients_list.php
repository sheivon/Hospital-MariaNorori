<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/patient.php';
header('Content-Type: application/json');
$rows = get_patients();
echo json_encode(['success'=>true,'data'=>$rows]);
