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
$unit = trim($data['unit'] ?? '');
$room = trim($data['room'] ?? '');
$expediente_no = trim($data['expediente_no'] ?? '');
$cedula = trim($data['cedula'] ?? '');
$inss_no = trim($data['inss_no'] ?? '');
$description = trim($data['description'] ?? '');
$date = !empty($data['date']) ? $data['date'] : null; // YYYY-MM-DD
$time = !empty($data['time']) ? $data['time'] : null; // HH:MM:SS
$plan = trim($data['plan'] ?? '');
$weight = isset($data['weight']) ? trim($data['weight']) : null;
$height = isset($data['height']) ? trim($data['height']) : null;
$age = isset($data['age']) ? (int)$data['age'] : null;
$sex = trim($data['sex'] ?? '');

if (!$patient_id || $type === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO diagnostics (patient_id, type, unit, room, expediente_no, cedula, inss_no, description, date, time, plan, weight, height, age, sex, created_by) VALUES (:pid, :type, :unit, :room, :exp, :ced, :inss, :desc, :dt, :tm, :plan, :weight, :height, :age, :sex, :uid)');
    $stmt->execute([
        ':pid' => $patient_id,
        ':type' => $type,
        ':unit' => $unit ?: null,
        ':room' => $room ?: null,
        ':exp' => $expediente_no ?: null,
        ':ced' => $cedula ?: null,
        ':inss' => $inss_no ?: null,
        ':desc' => $description !== '' ? $description : null,
        ':dt' => $date,
        ':tm' => $time,
        ':plan' => $plan ?: null,
        ':weight' => $weight !== '' ? $weight : null,
        ':height' => $height !== '' ? $height : null,
        ':age' => $age !== null ? $age : null,
        ':sex' => $sex ?: null,
        ':uid' => $user_id,
    ]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
