<?php
require __DIR__ . '/../config/db.php';
// safe idempotent alter
try{
    $q = $pdo->query("SHOW COLUMNS FROM chat_messages LIKE 'recipient_id'");
    $exists = $q->fetch();
    if ($exists) {
        echo "recipient_id already exists\n";
        exit;
    }
    $pdo->exec("ALTER TABLE chat_messages ADD COLUMN recipient_id INT NULL AFTER message");
    $pdo->exec("CREATE INDEX idx_chat_recipient ON chat_messages (recipient_id)");
    echo "Added recipient_id to chat_messages\n";
}catch(Exception $e){
    echo "Error: " . $e->getMessage() . "\n";
}
