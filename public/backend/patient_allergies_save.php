<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$patient_id = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
$allergen = trim((string)($_POST['allergen'] ?? ''));
$reaction = trim((string)($_POST['reaction'] ?? ''));
$severity = trim((string)($_POST['severity'] ?? ''));
$status = trim((string)($_POST['status'] ?? 'active'));
$noted_date = trim((string)($_POST['noted_date'] ?? ''));
$notes = trim((string)($_POST['notes'] ?? ''));

if ($patient_id <= 0 || $allergen === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Patient and allergen are required']);
    exit;
}

$status = in_array($status, ['active', 'inactive'], true) ? $status : 'active';
$noted_date = $noted_date !== '' ? $noted_date : null;

if ($id > 0) {
    $stmt = $pdo->prepare(
        'UPDATE patient_allergies SET patient_id = :patient_id, allergen = :allergen, reaction = :reaction, severity = :severity, status = :status, noted_date = :noted_date, notes = :notes, updated_at = NOW() WHERE id = :id'
    );
    $success = $stmt->execute([
        ':patient_id' => $patient_id,
        ':allergen' => $allergen,
        ':reaction' => $reaction,
        ':severity' => $severity,
        ':status' => $status,
        ':noted_date' => $noted_date,
        ':notes' => $notes,
        ':id' => $id,
    ]);
} else {
    $stmt = $pdo->prepare(
        'INSERT INTO patient_allergies (patient_id, allergen, reaction, severity, status, noted_date, notes, created_at) VALUES (:patient_id, :allergen, :reaction, :severity, :status, :noted_date, :notes, NOW())'
    );
    $success = $stmt->execute([
        ':patient_id' => $patient_id,
        ':allergen' => $allergen,
        ':reaction' => $reaction,
        ':severity' => $severity,
        ':status' => $status,
        ':noted_date' => $noted_date,
        ':notes' => $notes,
    ]);
}

if (!$success) {
    http_response_code(500);
    echo json_encode(['error' => 'Save failed']);
    exit;
}

echo json_encode(['success' => true]);
