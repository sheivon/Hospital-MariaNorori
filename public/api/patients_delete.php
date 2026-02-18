<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/patient.php';
header('Content-Type: application/json');
$id = $_POST['id'] ?? null;
if (!$id) { echo json_encode(['success'=>false,'error'=>'Missing id']); exit; }
try {
    delete_patient($id);
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
