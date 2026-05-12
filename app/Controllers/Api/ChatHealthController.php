<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Repositories\ChatRepository;
use App\Repositories\UserRepository;
use App\Services\ChatHealthService;
use Throwable;

class ChatHealthController extends BaseApiController
{
    private static function service(): ChatHealthService
    {
        return new ChatHealthService(new ChatRepository(), new UserRepository());
    }

    public static function health(): void
    {
        Auth::requireLogin();

        try {
            $user = Auth::currentUser();
            $me = (int)($user['id'] ?? 0);
            $schema = self::service()->checkSchema();
            $otherUser = $me > 0 ? self::service()->findOtherUser($me) : null;
            $recipient = $otherUser ? (int)$otherUser['id'] : $me;
            $id = self::service()->sendHealthMessage($me, (string)($user['username'] ?? ''), $recipient);
            $sendOk = (bool)$id;
            $readBack = self::service()->verifyMessageRead($id, $me, $recipient);
            $cleanupOk = self::service()->cleanupMessage($id);

            self::success(['data' => [
                'schema' => $schema,
                'users' => ['ok' => true, 'otherUser' => $otherUser],
                'send' => ['ok' => $sendOk, 'id' => (int)$id, 'recipient' => $recipient, 'readBack' => $readBack],
                'cleanup' => ['ok' => $cleanupOk],
            ]]);
        } catch (Throwable $e) {
            self::fail($e->getMessage(), 500, ['data' => ['schema' => null, 'users' => null, 'send' => null, 'cleanup' => null]]);
        }
    }
}

