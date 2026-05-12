<?php

namespace App\Services;

use App\Core\Database;
use App\Repositories\ChatRepository;
use App\Repositories\UserRepository;
use PDO;

class ChatHealthService
{
    private ChatRepository $ChatRepository;
    private UserRepository $UserRepository;
    private PDO $pdo;

    public function __construct(ChatRepository $ChatRepository, UserRepository $UserRepository)
    {
        $this->ChatRepository = $ChatRepository;
        $this->UserRepository = $UserRepository;
        $this->pdo = Database::pdo();
    }

    public function checkSchema(): array
    {
        $schemaOk = true;
        $missing = [];

        $tableStmt = $this->pdo->prepare("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'chat_messages'");
        $tableStmt->execute();
        $exists = (int)$tableStmt->fetchColumn() > 0;

        if (!$exists) {
            $schemaOk = false;
            $missing[] = 'table chat_messages';
        } else {
            $needCols = ['id', 'user_id', 'username', 'message', 'recipient_id', 'created_at'];
            $cols = $this->pdo->query('SHOW COLUMNS FROM chat_messages')->fetchAll(PDO::FETCH_COLUMN, 0);
            foreach ($needCols as $column) {
                if (!in_array($column, $cols, true)) {
                    $schemaOk = false;
                    $missing[] = "column $column";
                }
            }
        }

        return ['ok' => $schemaOk, 'missing' => $missing];
    }

    public function findOtherUser(int $currentUserId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, username FROM users WHERE id != :me ORDER BY id LIMIT 1');
        $stmt->execute([':me' => $currentUserId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function sendHealthMessage(int $userId, string $username, int $recipient): int
    {
        return $this->ChatRepository->addMessage($userId, $username, '[health-check] ' . date('c'), $recipient);
    }

    public function verifyMessageRead(int $messageId, int $userId, int $recipient): bool
    {
        $messages = $this->ChatRepository->getMessages(max(0, $messageId - 1), 50, $userId, $recipient);
        foreach ($messages as $message) {
            if ((int)($message['id'] ?? 0) === $messageId) {
                return true;
            }
        }

        return false;
    }

    public function cleanupMessage(int $messageId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM chat_messages WHERE id = :id');
        $stmt->execute([':id' => $messageId]);
        return $stmt->rowCount() >= 0;
    }
}

