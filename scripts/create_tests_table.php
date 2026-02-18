<?php
require_once __DIR__ . '/../config/db.php';

echo "Creating tests table...\n";
$sql = file_get_contents(__DIR__ . '/../migrations/create_tests_table.sql');
try{
    $pdo->exec($sql);
    echo "Done.\n";
}catch(PDOException $e){
    echo "Failed: " . $e->getMessage() . "\n";
}
