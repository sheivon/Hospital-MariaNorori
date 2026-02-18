<?php
require_once __DIR__ . '/../../../src/auth.php';
require_once __DIR__ . '/../../../config/db.php';
require_role('admin');
header('Content-Type: application/json');

function bad($m){ echo json_encode(['success'=>false,'error'=>$m]); exit; }

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) bad('Missing id');
$username = trim($_POST['username'] ?? ''); // optional in edit
$password = (string)($_POST['password'] ?? ''); // if provided, reset
$fullname = trim($_POST['fullname'] ?? '');
$cedula = trim($_POST['cedula'] ?? '');
$role = strtolower(trim($_POST['role'] ?? 'user'));
if (!in_array($role, ['user','admin'], true)) $role = 'user';

// cannot demote/delete own admin? For update, allow changing others; allow self role change only if remains admin
$me = current_user();
if ($me && (int)$me['id'] === $id && $role !== 'admin') bad('Cannot remove your own admin role');

try{
    // ensure user exists
    $u = $pdo->prepare('SELECT id, role FROM users WHERE id = :id LIMIT 1');
    $u->execute([':id'=>$id]);
    $row = $u->fetch(PDO::FETCH_ASSOC);
    if (!$row) bad('User not found');

    // unique constraints
    if ($username !== ''){
        $q = $pdo->prepare('SELECT id FROM users WHERE username = :u AND id <> :id LIMIT 1');
        $q->execute([':u'=>$username, ':id'=>$id]);
        if ($q->fetch()) bad('Username already taken');
    }
    if ($cedula !== ''){
        $q2 = $pdo->prepare('SELECT id FROM users WHERE cedula = :c AND id <> :id LIMIT 1');
        $q2->execute([':c'=>$cedula, ':id'=>$id]);
        if ($q2->fetch()) bad('Cédula already in use');
    }

    // build update
    $fields = ['fullname' => $fullname, 'cedula' => $cedula, 'role' => $role];
    if ($username !== '') $fields['username'] = $username;
    $set = [];$params = [':id'=>$id];
    foreach ($fields as $k=>$v){ $set[] = "$k = :$k"; $params[":$k"] = $v; }
    if ($password !== ''){ $set[]='password = :p'; $params[':p'] = password_hash($password, PASSWORD_DEFAULT); }
    $sql = 'UPDATE users SET ' . implode(', ',$set) . ' WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success'=>true]);
}catch(Throwable $e){
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
