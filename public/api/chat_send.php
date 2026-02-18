<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/chat.php';
header('Content-Type: application/json');
if (empty($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}
$user = $_SESSION['user'];
$msg = trim($_POST['message'] ?? '');
if ($msg === '') {
    echo json_encode(['success' => false, 'error' => 'Empty message']);
    exit;
}
$msg = mb_substr($msg, 0, 2000); // limit
// optional recipient_id for private message
$recipient = isset($_POST['recipient_id']) && $_POST['recipient_id'] !== '' ? (int)$_POST['recipient_id'] : null;
// if recipient provided, ensure it exists (simple check)
if ($recipient) {
    $q = $pdo->prepare('SELECT id FROM users WHERE id = :id LIMIT 1');
    $q->execute([':id' => $recipient]);
    if (!$q->fetch()){
        echo json_encode(['success' => false, 'error' => 'Recipient not found']);
        exit;
    }
}

$id = chat_add_message($user['id'], $user['username'], $msg, $recipient);
echo json_encode(['success' => true, 'id' => $id, 'message' => $msg, 'username' => $user['username'], 'created_at' => date('c')]);
