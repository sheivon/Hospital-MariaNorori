<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/chat.php';
header('Content-Type: application/json');
$since = isset($_GET['since']) ? (int)$_GET['since'] : 0;
$limit = isset($_GET['limit']) ? min(500, (int)$_GET['limit']) : 200;
// optional recipient param to load private conversation between current user and recipient
$recipient = isset($_GET['recipient']) && $_GET['recipient'] !== '' ? (int)$_GET['recipient'] : null;
$current = !empty($_SESSION['user']) ? (int)$_SESSION['user']['id'] : null;
if ($recipient && $current) {
	$msgs = chat_get_messages($since, $limit, $current, $recipient);
} else {
	$msgs = chat_get_messages($since, $limit);
}
echo json_encode(['success' => true, 'data' => $msgs]);
