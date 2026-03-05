<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ChatModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function addMessage(int $userId, string $username, string $message, ?int $recipientId = null): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO chat_messages (user_id, username, message, recipient_id) VALUES (:uid, :un, :msg, :rid)');
        $stmt->execute([
            ':uid' => $userId ?: null,
            ':un' => $username,
            ':msg' => $message,
            ':rid' => $recipientId ?: null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getMessages(int $sinceId = 0, int $limit = 200, ?int $userA = null, ?int $userB = null): array
    {
        if ($userA && $userB) {
            return $this->getPrivateMessages($sinceId, $limit, $userA, $userB);
        }
        return $this->getPublicMessages($sinceId, $limit);
    }

    private function getPrivateMessages(int $sinceId, int $limit, int $userA, int $userB): array
    {
        if ($sinceId > 0) {
            $stmt = $this->pdo->prepare('SELECT * FROM chat_messages WHERE ((user_id = :a AND recipient_id = :b) OR (user_id = :b AND recipient_id = :a)) AND id > :since ORDER BY id ASC LIMIT :lim');
            $stmt->bindValue(':since', $sinceId, PDO::PARAM_INT);
        } else {
            $stmt = $this->pdo->prepare('SELECT * FROM chat_messages WHERE (user_id = :a AND recipient_id = :b) OR (user_id = :b AND recipient_id = :a) ORDER BY id DESC LIMIT :lim');
        }

        $stmt->bindValue(':a', $userA, PDO::PARAM_INT);
        $stmt->bindValue(':b', $userB, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($sinceId <= 0) {
            return array_reverse($rows);
        }
        return $rows;
    }

    private function getPublicMessages(int $sinceId, int $limit): array
    {
        if ($sinceId > 0) {
            $stmt = $this->pdo->prepare('SELECT * FROM chat_messages WHERE recipient_id IS NULL AND id > :since ORDER BY id ASC LIMIT :lim');
            $stmt->bindValue(':since', $sinceId, PDO::PARAM_INT);
        } else {
            $stmt = $this->pdo->prepare('SELECT * FROM chat_messages WHERE recipient_id IS NULL ORDER BY id DESC LIMIT :lim');
        }

        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($sinceId <= 0) {
            return array_reverse($rows);
        }
        return $rows;
    }
}
