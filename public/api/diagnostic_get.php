<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/auth.php';

require_login();

use App\Core\ApiResponse;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    ApiResponse::fail('Missing id');
}

try {
    global $pdo;
    $deletedCondition = softDeleteCondition($pdo, 'diagnostics', 'd');
$stmt = $pdo->prepare('SELECT d.*, u1.fullname AS created_by_name, u2.fullname AS updated_by_name FROM diagnostics d LEFT JOIN users u1 ON d.created_by = u1.id LEFT JOIN users u2 ON d.updated_by = u2.id WHERE d.id = :id' . $deletedCondition . ' LIMIT 1');
    $stmt->execute([':id' => $id]);
    $diag = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$diag) {
        ApiResponse::fail('Diagnostic not found');
    }
    ApiResponse::success(['diagnostic' => $diag]);
} catch (\Throwable $e) {
    ApiResponse::fail($e->getMessage());
}
