<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id']);
    exit;
}

$stmt = $pdo->prepare('DELETE FROM patient_allergies WHERE id = :id');
$success = $stmt->execute([':id' => $id]);
if (!$success) {
    http_response_code(500);
    echo json_encode(['error' => 'Delete failed']);
    exit;
}

echo json_encode(['success' => true]);
