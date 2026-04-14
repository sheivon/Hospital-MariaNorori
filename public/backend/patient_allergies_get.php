<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, patient_id, allergen, reaction, severity, status, noted_date, notes FROM patient_allergies WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$allergy = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$allergy) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

echo json_encode($allergy);
