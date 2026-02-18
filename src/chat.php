<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Add a chat message. Optionally provide a recipient_id for private messages.
 * recipient_id is nullable (null = public/global message)
 */
function chat_add_message($user_id, $username, $message, $recipient_id = null) {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO chat_messages (user_id, username, message, recipient_id) VALUES (:uid, :un, :msg, :rid)');
    $stmt->execute([':uid' => $user_id ?: null, ':un' => $username, ':msg' => $message, ':rid' => $recipient_id ?: null]);
    return $pdo->lastInsertId();
}

/**
 * Get messages. If $userA and $userB are provided, returns only messages exchanged between them.
 * Otherwise behaves like before and returns public/global messages.
 */
function chat_get_messages($since_id = 0, $limit = 200, $userA = null, $userB = null) {
    global $pdo;
    if ($userA && $userB) {
        // messages where (sender=userA and recipient=userB) OR (sender=userB and recipient=userA)
        if ($since_id > 0) {
            $stmt = $pdo->prepare('SELECT * FROM chat_messages WHERE ((user_id = :a AND recipient_id = :b) OR (user_id = :b AND recipient_id = :a)) AND id > :since ORDER BY id ASC LIMIT :lim');
            $stmt->bindValue(':since', (int)$since_id, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare('SELECT * FROM chat_messages WHERE (user_id = :a AND recipient_id = :b) OR (user_id = :b AND recipient_id = :a) ORDER BY id DESC LIMIT :lim');
        }
        $stmt->bindValue(':a', (int)$userA, PDO::PARAM_INT);
        $stmt->bindValue(':b', (int)$userB, PDO::PARAM_INT);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($since_id <= 0) return array_reverse($rows);
        return $rows;
    }

    // fallback to public/global messages
    if ($since_id > 0) {
        $stmt = $pdo->prepare('SELECT * FROM chat_messages WHERE recipient_id IS NULL AND id > :since ORDER BY id ASC LIMIT :lim');
        $stmt->bindValue(':since', (int)$since_id, PDO::PARAM_INT);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM chat_messages WHERE recipient_id IS NULL ORDER BY id DESC LIMIT :lim');
    }
    $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($since_id <= 0) {
        // when asking latest N we returned DESC, but clients prefer ASC
        return array_reverse($rows);
    }
    return $rows;
}
