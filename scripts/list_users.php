<?php
require __DIR__ . '/../config/db.php';
$stmt = $pdo->query('SELECT id, username, fullname, cedula, role, created_at FROM users ORDER BY id DESC LIMIT 10');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";
