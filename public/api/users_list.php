<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');
if (empty($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}
$current = (int)$_SESSION['user']['id'];
$stmt = $pdo->prepare('SELECT id, username, fullname, cedula FROM users WHERE id != :me ORDER BY username ASC');
$stmt->execute([':me' => $current]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['success' => true, 'data' => $rows]);
