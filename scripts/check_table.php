<?php
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'chat_messages'");
$stmt->execute();
$r = $stmt->fetch();
echo ($r['c'] ? "chat_messages exists\n" : "chat_messages missing\n");
