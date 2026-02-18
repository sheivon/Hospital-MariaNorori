<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/auth.php';

require_login();

header('Content-Type: application/json');

$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : null;

try {
    global $pdo;
    if ($patient_id) {
        $stmt = $pdo->prepare('SELECT d.*, u1.fullname AS created_by_name, u2.fullname AS updated_by_name
            FROM diagnostics d
            LEFT JOIN users u1 ON d.created_by = u1.id
            LEFT JOIN users u2 ON d.updated_by = u2.id
            WHERE d.patient_id = :pid
            ORDER BY d.date DESC, d.id DESC');
        $stmt->execute([':pid' => $patient_id]);
    } else {
        $stmt = $pdo->query('SELECT d.*, u1.fullname AS created_by_name, u2.fullname AS updated_by_name
            FROM diagnostics d
            LEFT JOIN users u1 ON d.created_by = u1.id
            LEFT JOIN users u2 ON d.updated_by = u2.id
            ORDER BY d.date DESC, d.id DESC');
    }
    $rows = $stmt->fetchAll();
    echo json_encode(['success' => true, 'diagnostics' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
