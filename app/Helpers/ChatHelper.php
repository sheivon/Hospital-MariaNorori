<?php

namespace App\Helpers;

use App\Repositories\ChatRepository;

class ChatHelper
{
    private static ?ChatRepository $model = null;

    private static function model(): ChatRepository
    {
        if (self::$model === null) {
            self::$model = new ChatRepository();
        }
        return self::$model;
    }

    public static function addMessage(int $userId, string $username, string $message, ?int $recipientId = null)
    {
        return self::model()->addMessage($userId, $username, $message, $recipientId);
    }

    public static function getMessages(int $sinceId = 0, int $limit = 200, ?int $userA = null, ?int $userB = null)
    {
        return self::model()->getMessages($sinceId, $limit, $userA, $userB);
    }
}

