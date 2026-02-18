<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/patient.php';
header('Content-Type: application/json');
$data = $_POST;
if (empty($data['id'])) {
    echo json_encode(['success'=>false,'error'=>'Missing id']);
    exit;
}
try {
    $ok = update_patient($data['id'], $data);
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
