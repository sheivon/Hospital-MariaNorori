<?php
require_once __DIR__ . '/../../../src/auth.php';
require_once __DIR__ . '/../../../config/db.php';
require_role('admin');
header('Content-Type: application/json');

function bad($m){ echo json_encode(['success'=>false,'error'=>$m]); exit; }

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');
$fullname = trim($_POST['fullname'] ?? '');
$cedula = trim($_POST['cedula'] ?? '');
$role = strtolower(trim($_POST['role'] ?? 'user'));
if ($username === '' || $password === '') bad('Username and password required');
if (!in_array($role, ['user','admin'], true)) $role = 'user';

try{
    // unique username
    $q = $pdo->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
    $q->execute([':u'=>$username]);
    if ($q->fetch()) bad('Username already taken');
    if ($cedula !== ''){
        $q2 = $pdo->prepare('SELECT id FROM users WHERE cedula = :c LIMIT 1');
        $q2->execute([':c'=>$cedula]);
        if ($q2->fetch()) bad('Cédula already in use');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username,password,fullname,cedula,role) VALUES (:u,:p,:f,:c,:r)');
    $stmt->execute([':u'=>$username,':p'=>$hash,':f'=>$fullname,':c'=>$cedula,':r'=>$role]);
    echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
}catch(Throwable $e){
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
