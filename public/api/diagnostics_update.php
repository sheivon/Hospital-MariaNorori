<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/auth.php';

require_login();
$user = current_user();
$user_id = $user['id'] ?? null;

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true) ?: [];

$id = (int)($data['id'] ?? 0);
$type = trim($data['type'] ?? '');
$description = trim($data['description'] ?? '');
$date = !empty($data['date']) ? $data['date'] : null;

if (!$id || $type === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE diagnostics SET type = :type, description = :desc, date = :dt, updated_by = :uid WHERE id = :id AND deleted_at IS NULL');
    $stmt->execute([
        ':type' => $type,
        ':desc' => $description !== '' ? $description : null,
        ':dt' => $date,
        ':uid' => $user_id,
        ':id' => $id,
    ]);
    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
