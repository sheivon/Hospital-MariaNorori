<?php

namespace App\Services;

use App\Repositories\ChatRepository;

class ChatService
{
    private ChatRepository $repository;

    public function __construct(ChatRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getMessages(int $sinceId = 0, int $limit = 200, ?int $userA = null, ?int $userB = null): array
    {
        return $this->repository->getMessages($sinceId, $limit, $userA, $userB);
    }

    public function addMessage(int $userId, string $username, string $message, ?int $recipientId = null): int
    {
        return $this->repository->addMessage($userId, $username, $message, $recipientId);
    }
}

