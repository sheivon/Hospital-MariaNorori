<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/auth.php';

require_login();
$user = current_user();

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true) ?: $_POST ?: [];

$id = (int)($data['id'] ?? 0);
$test_type = trim($data['test_type'] ?? '');
$result = trim($data['result'] ?? '');
$test_date = !empty($data['test_date']) ? $data['test_date'] : null;
$notes = trim($data['notes'] ?? '');

if (!$id || $test_type === ''){
    echo json_encode(['success'=>false,'error'=>'Missing required fields']);
    exit;
}

try{
    global $pdo;
    $stmt = $pdo->prepare('UPDATE tests SET test_type=:tt, result=:res, test_date=:td, notes=:notes WHERE id=:id');
    $stmt->execute([
        ':tt'=>$test_type,
        ':res'=>$result !== '' ? $result : null,
        ':td'=>$test_date,
        ':notes'=>$notes !== '' ? $notes : null,
        ':id'=>$id,
    ]);
    echo json_encode(['success'=>true]);
}catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
