<?php
// Scan users and remove leading UTF-8 BOM (U+FEFF) from usernames.
require __DIR__ . '/../config/db.php';

echo "Scanning users for leading BOM...\n";
$stmt = $pdo->query('SELECT id, username FROM users');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$changed = 0;
foreach ($rows as $r) {
    $id = $r['id'];
    $username = $r['username'];
    // Remove leading BOM (U+FEFF) if present
    $clean = preg_replace('/^\x{FEFF}/u', '', $username);
    if ($clean !== $username) {
        $upd = $pdo->prepare('UPDATE users SET username = :u WHERE id = :id');
        $upd->execute([':u' => $clean, ':id' => $id]);
        echo "Fixed id={$id}: '" . addslashes($username) . "' -> '" . addslashes($clean) . "'\n";
        $changed++;
    }
}
if ($changed === 0) echo "No BOMs found.\n"; else echo "Done. $changed user(s) updated.\n";
