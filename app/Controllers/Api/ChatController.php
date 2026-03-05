<?php

namespace App\Controllers\Api;

use App\Core\ApiResponse;
use App\Core\Auth;
use App\Models\ChatModel;
use App\Models\UserModel;

class ChatController
{
    public static function list(array $query): void
    {
        Auth::bootSession();

        $since = isset($query['since']) ? (int)$query['since'] : 0;
        $limit = isset($query['limit']) ? min(500, (int)$query['limit']) : 200;
        $recipient = isset($query['recipient']) && $query['recipient'] !== '' ? (int)$query['recipient'] : null;
        $current = Auth::currentUser();
        $currentId = $current ? (int)$current['id'] : null;

        $chatModel = new ChatModel();
        if ($recipient && $currentId) {
            $data = $chatModel->getMessages($since, $limit, $currentId, $recipient);
        } else {
            $data = $chatModel->getMessages($since, $limit);
        }

        ApiResponse::success(['data' => $data]);
    }

    public static function send(array $payload): void
    {
        Auth::requireLogin();
        $user = Auth::currentUser();

        $message = trim((string)($payload['message'] ?? ''));
        if ($message === '') {
            ApiResponse::fail('Empty message');
        }
        $message = mb_substr($message, 0, 2000);

        $recipient = isset($payload['recipient_id']) && $payload['recipient_id'] !== '' ? (int)$payload['recipient_id'] : null;
        if ($recipient) {
            $userModel = new UserModel();
            if (!$userModel->existsById($recipient)) {
                ApiResponse::fail('Recipient not found');
            }
        }

        $chatModel = new ChatModel();
        $id = $chatModel->addMessage((int)$user['id'], (string)$user['username'], $message, $recipient);

        ApiResponse::success([
            'id' => $id,
            'message' => $message,
            'username' => (string)$user['username'],
            'created_at' => date('c'),
        ]);
    }
}
