<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/api_helpers.php';
require_once __DIR__ . '/../../src/auth.php';

require_login();

header('Content-Type: application/json');
$data = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
$id = (int)($data['id'] ?? 0);

if (!$id){
    echo json_encode(['success'=>false,'error'=>'Missing id']);
    exit;
}

try{
    global $pdo;
    if (hasColumn($pdo, 'tests', 'deleted_at')) {
        $stmt = $pdo->prepare('UPDATE tests SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        $stmt->execute([':id'=>$id]);
    } else {
        $stmt = $pdo->prepare('DELETE FROM tests WHERE id = :id');
        $stmt->execute([':id'=>$id]);
    }
    echo json_encode(['success'=>true]);
}catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
