<?php
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');
$ced = trim($_REQUEST['cedula'] ?? '');
$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($ced === '') { echo json_encode(['success'=>false,'error'=>'Empty cedula']); exit; }
$stmt = $pdo->prepare('SELECT id FROM patients WHERE cedula = :ced' . ($id ? ' AND id != :id' : '') . ' LIMIT 1');
$params = [':ced'=>$ced]; if ($id) $params[':id']=$id;
$stmt->execute($params);
if ($stmt->fetch()) echo json_encode(['success'=>true,'available'=>false]); else echo json_encode(['success'=>true,'available'=>true]);
