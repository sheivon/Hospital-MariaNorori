<?php
require_once __DIR__ . '/../config/db.php';

function column_exists($table, $column){
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c");
    $stmt->execute([':t'=>$table, ':c'=>$column]);
    $r = $stmt->fetch();
    return (bool)$r['c'];
}

if (!column_exists('patients','cedula')){
    $pdo->exec("ALTER TABLE patients ADD cedula VARCHAR(50) DEFAULT NULL AFTER last_name");
    echo "Added patients.cedula\n";
} else {
    echo "patients.cedula already exists\n";
}

if (!column_exists('users','cedula')){
    $pdo->exec("ALTER TABLE users ADD cedula VARCHAR(50) DEFAULT NULL AFTER fullname");
    echo "Added users.cedula\n";
} else {
    echo "users.cedula already exists\n";
}
