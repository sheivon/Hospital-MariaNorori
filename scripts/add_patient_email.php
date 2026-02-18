<?php
require __DIR__ . '/../config/db.php';
try{
    $q = $pdo->query("SHOW COLUMNS FROM patients LIKE 'email'");
    if ($q->fetch()){
        echo "email column already exists\n";
        exit;
    }
    $pdo->exec("ALTER TABLE patients ADD COLUMN email VARCHAR(255) NULL AFTER last_name");
    echo "Added email column to patients\n";
}catch(Exception $e){
    echo "Error: ".$e->getMessage()."\n";
}
