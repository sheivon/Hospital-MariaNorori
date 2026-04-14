<?php
require 'db.php';

$stmt = $pdo->query("SELECT * FROM patient_allergies");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["data" => $data]);