<?php
require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\SetupController;

header('Content-Type: application/json; charset=utf-8');

$payload = json_decode(file_get_contents('php://input'), true) ?: $_POST ?: [];

try {
    $result = SetupController::processAction($payload);
    echo json_encode([
        'success' => $result['success'],
        'messages' => $result['messages'],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
