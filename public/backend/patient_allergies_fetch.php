<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$patientId = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

if ($patientId > 0) {
    $stmt = $pdo->prepare(
        'SELECT a.id, a.patient_id, CONCAT(IFNULL(p.first_name, \'\'), \' \' , IFNULL(p.last_name, \'\')) AS patient_name, a.allergen, a.reaction, a.severity, a.status, IFNULL(a.noted_date, \'\') AS noted_date, IFNULL(a.notes, \'\') AS notes
         FROM patient_allergies a
         LEFT JOIN patients p ON p.id = a.patient_id
         WHERE a.patient_id = :pid
         ORDER BY a.id DESC'
    );
    $stmt->execute([':pid' => $patientId]);
} else {
    $stmt = $pdo->query(
        'SELECT a.id, a.patient_id, CONCAT(IFNULL(p.first_name, \'\'), \' \' , IFNULL(p.last_name, \'\')) AS patient_name, a.allergen, a.reaction, a.severity, a.status, IFNULL(a.noted_date, \'\') AS noted_date, IFNULL(a.notes, \'\') AS notes
         FROM patient_allergies a
         LEFT JOIN patients p ON p.id = a.patient_id
         ORDER BY a.id DESC'
    );
}
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['data' => $data]);
