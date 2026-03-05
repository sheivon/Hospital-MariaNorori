<?php
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

try{
    global $pdo;
    if ($patient_id){
        $stmt = $pdo->prepare('SELECT t.*, p.first_name, p.last_name, u.username AS created_by_name FROM tests t LEFT JOIN patients p ON p.id = t.patient_id LEFT JOIN users u ON u.id = t.created_by WHERE t.patient_id = :pid AND t.deleted_at IS NULL ORDER BY t.test_date DESC, t.id DESC');
        $stmt->execute([':pid'=>$patient_id]);
    } else {
        $stmt = $pdo->query('SELECT t.*, p.first_name, p.last_name, u.username AS created_by_name FROM tests t LEFT JOIN patients p ON p.id = t.patient_id LEFT JOIN users u ON u.id = t.created_by WHERE t.deleted_at IS NULL ORDER BY t.test_date DESC, t.id DESC');
    }
    $rows = $stmt->fetchAll();
    echo json_encode(['success'=>true,'data'=>$rows]);
}catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
