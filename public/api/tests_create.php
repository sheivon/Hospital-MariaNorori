<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/auth.php';

require_login();
$user = current_user();
$user_id = $user['id'] ?? null;

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true) ?: $_POST ?: [];

$patient_id = (int)($data['patient_id'] ?? 0);
$test_type = trim($data['test_type'] ?? '');
$result = trim($data['result'] ?? '');
$test_date = !empty($data['test_date']) ? $data['test_date'] : null; // YYYY-MM-DD or datetime
$notes = trim($data['notes'] ?? '');

if (!$patient_id || $test_type === ''){
    echo json_encode(['success'=>false,'error'=>'Missing required fields']);
    exit;
}

try{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO tests (patient_id, test_type, result, test_date, notes, created_by) VALUES (:pid, :tt, :res, :td, :notes, :uid)');
    $stmt->execute([
        ':pid'=>$patient_id,
        ':tt'=>$test_type,
        ':res'=>$result !== '' ? $result : null,
        ':td'=>$test_date,
        ':notes'=>$notes !== '' ? $notes : null,
        ':uid'=>$user_id,
    ]);
    echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
}catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
