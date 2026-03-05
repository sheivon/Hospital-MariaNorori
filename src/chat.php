<?php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Models\ChatModel;

function chat_add_message($user_id, $username, $message, $recipient_id = null) {
    return (new ChatModel())->addMessage((int)$user_id, (string)$username, (string)$message, $recipient_id !== null ? (int)$recipient_id : null);
}

function chat_get_messages($since_id = 0, $limit = 200, $userA = null, $userB = null) {
    return (new ChatModel())->getMessages((int)$since_id, (int)$limit, $userA !== null ? (int)$userA : null, $userB !== null ? (int)$userB : null);
}
