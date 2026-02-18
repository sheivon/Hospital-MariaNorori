<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/chat.php';
header('Content-Type: application/json');

if (empty($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$out = [
    'schema' => null,
    'users' => null,
    'send' => null,
    'cleanup' => null,
];

try {
    // 1) Schema check
    $schemaOk = true; $missing = [];
    $tableStmt = $pdo->prepare("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'chat_messages'");
    $tableStmt->execute();
    $exists = (int)$tableStmt->fetchColumn() > 0;
    if (!$exists) { $schemaOk = false; $missing[] = 'table chat_messages'; }
    else {
        $needCols = ['id','user_id','username','message','recipient_id','created_at'];
        $cols = $pdo->query("SHOW COLUMNS FROM chat_messages")->fetchAll(PDO::FETCH_COLUMN, 0);
        foreach ($needCols as $c) { if (!in_array($c, $cols, true)) { $schemaOk = false; $missing[] = "column $c"; } }
    }
    $out['schema'] = [ 'ok' => $schemaOk, 'missing' => $missing ];

    // 2) Users check
    $me = (int)$_SESSION['user']['id'];
    $stmt = $pdo->prepare('SELECT id, username FROM users WHERE id != :me ORDER BY id LIMIT 1');
    $stmt->execute([':me' => $me]);
    $other = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    $out['users'] = [ 'ok' => true, 'otherUser' => $other ];

    // 3) Send test message (to other user if present, else to self)
    $recipient = $other ? (int)$other['id'] : $me;
    $msg = '[health-check] ' . date('c');
    $id = chat_add_message($me, $_SESSION['user']['username'], $msg, $recipient);
    $send = [ 'ok' => (bool)$id, 'id' => (int)$id, 'recipient' => $recipient ];

    // 4) Read back minimal set
    $msgs = chat_get_messages(((int)$id) - 1, 50, $me, $recipient);
    $found = false;
    foreach ($msgs as $m) { if ((int)$m['id'] === (int)$id) { $found = true; break; } }
    $send['readBack'] = $found;

    // 5) Cleanup
    $del = $pdo->prepare('DELETE FROM chat_messages WHERE id = :id');
    $del->execute([':id' => $id]);
    $out['cleanup'] = [ 'ok' => $del->rowCount() >= 0 ];
    $out['send'] = $send;

    echo json_encode(['success' => true, 'data' => $out]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'data' => $out]);
}
