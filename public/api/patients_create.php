<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/patient.php';
header('Content-Type: application/json');
$data = $_POST;
try {
    $id = create_patient($data);
    echo json_encode(['success'=>true,'id'=>$id]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
