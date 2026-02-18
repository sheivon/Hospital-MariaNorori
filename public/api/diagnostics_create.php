<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/auth.php';

require_login();
$user = current_user();
$user_id = $user['id'] ?? null;

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true) ?: [];

$patient_id = (int)($data['patient_id'] ?? 0);
$type = trim($data['type'] ?? '');
$description = trim($data['description'] ?? '');
$date = !empty($data['date']) ? $data['date'] : null; // YYYY-MM-DD

if (!$patient_id || $type === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO diagnostics (patient_id, type, description, date, created_by) VALUES (:pid, :type, :desc, :dt, :uid)');
    $stmt->execute([
        ':pid' => $patient_id,
        ':type' => $type,
        ':desc' => $description !== '' ? $description : null,
        ':dt' => $date,
        ':uid' => $user_id,
    ]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
