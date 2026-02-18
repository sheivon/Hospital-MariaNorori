<?php
require_once __DIR__ . '/../../../src/auth.php';
require_once __DIR__ . '/../../../config/db.php';
require_role('admin');
header('Content-Type: application/json');
try{
    $stmt = $pdo->query('SELECT id, username, fullname, cedula, role, created_at FROM users ORDER BY id ASC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true,'data'=>$rows]);
}catch(Throwable $e){
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
