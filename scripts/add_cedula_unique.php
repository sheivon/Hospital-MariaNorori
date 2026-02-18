<?php
require_once __DIR__ . '/../config/db.php';

function index_exists($table, $index){
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = :t AND index_name = :i");
    $stmt->execute([':t'=>$table, ':i'=>$index]);
    $r = $stmt->fetch();
    return (bool)$r['c'];
}

if (!index_exists('patients','uq_patients_cedula')){
    $pdo->exec("ALTER TABLE patients ADD UNIQUE INDEX uq_patients_cedula (cedula)");
    echo "Added unique index uq_patients_cedula\n";
} else {
    echo "uq_patients_cedula already exists\n";
}

if (!index_exists('users','uq_users_cedula')){
    $pdo->exec("ALTER TABLE users ADD UNIQUE INDEX uq_users_cedula (cedula)");
    echo "Added unique index uq_users_cedula\n";
} else {
    echo "uq_users_cedula already exists\n";
}
