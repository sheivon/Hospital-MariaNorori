<?php
// Usage: php scripts/apply_sql.php migrations/init.sql
$file = $argv[1] ?? null;
if (!$file || !file_exists($file)) {
    fwrite(STDERR, "Usage: php scripts/apply_sql.php <sql-file>\n");
    exit(2);
}
require_once __DIR__ . '/../config/db.php';
$sql = file_get_contents($file);
try{
    $pdo->exec($sql);
    echo "OK: applied $file\n";
}catch (PDOException $e){
    fwrite(STDERR, "ERROR: " . $e->getMessage() . "\n");
    exit(1);
}
