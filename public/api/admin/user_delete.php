<?php
require_once __DIR__ . '/../../../src/auth.php';
require_once __DIR__ . '/../../../config/db.php';
require_role('admin');
header('Content-Type: application/json');

function bad($m){ echo json_encode(['success'=>false,'error'=>$m]); exit; }

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) bad('Missing id');
$me = current_user();
if ($me && (int)$me['id'] === $id) bad('Cannot delete your own account');

try{
    // check role of target
    $q = $pdo->prepare('SELECT id, role FROM users WHERE id = :id');
    $q->execute([':id'=>$id]);
    $u = $q->fetch(PDO::FETCH_ASSOC);
    if (!$u) bad('User not found');
    if (strtolower($u['role'] ?? 'user') === 'admin'){
        // ensure not last admin
        $c = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        if ($c <= 1) bad('Cannot delete the last admin');
    }
    $del = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $del->execute([':id'=>$id]);
    echo json_encode(['success'=>true]);
}catch(Throwable $e){
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
