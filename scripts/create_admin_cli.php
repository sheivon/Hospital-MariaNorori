<?php
// Non-interactive helper for CI/dev: create an admin user with fixed credentials.
require __DIR__ . '/../config/db.php';
$username = $argv[1] ?? 'cliadmin_tmp';
$password = $argv[2] ?? 'cliPass_tmp';
$fullname = $argv[3] ?? 'CLI Admin Temp';
$hash = password_hash($password, PASSWORD_DEFAULT);
$cedula = $argv[4] ?? null;
$stmt = $pdo->prepare('INSERT INTO users (username, password, fullname, cedula, role) VALUES (:u,:p,:f,:cedula,:r)');
try {
    // check cedula uniqueness
    if (!empty($cedula)){
        $q = $pdo->prepare('SELECT id FROM users WHERE cedula = :ced LIMIT 1');
        $q->execute([':ced'=>$cedula]);
        if ($q->fetch()) { echo "Error creating admin: cedula already in use\n"; exit(1); }
    }
    $stmt->execute([':u'=>$username,':p'=>$hash,':f'=>$fullname,':cedula'=>$cedula,':r'=>'admin']);
    echo "Admin user created: {$username}\n";
} catch (PDOException $e) {
    echo "Error creating admin: " . $e->getMessage() . "\n";
}
