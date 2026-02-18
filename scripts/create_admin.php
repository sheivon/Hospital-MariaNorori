<?php
// Run from project root: php scripts/create_admin.php
require __DIR__ . '/../config/db.php';

echo "Create admin user\n";
$username = readline('Username (default: admin): ');
if (!trim($username)) $username = 'admin';
$password = readline('Password (default: admin123): ');
if (!trim($password)) $password = 'admin123';
$fullname = readline('Full name (optional): ');
$cedula = readline('Cédula (optional): ');

$hash = password_hash($password, PASSWORD_DEFAULT);
// check cedula uniqueness for users if provided
$cedTrim = trim($cedula ?? '');
if ($cedTrim !== ''){
    $q = $pdo->prepare('SELECT id FROM users WHERE cedula = :ced LIMIT 1');
    $q->execute([':ced'=>$cedTrim]);
    if ($q->fetch()) { echo "Error: cedula already in use\n"; exit(1); }
}
$stmt = $pdo->prepare('INSERT INTO users (username, password, fullname, cedula, role) VALUES (:u,:p,:f,:cedula,:r)');
try {
    $stmt->execute([':u'=>$username,':p'=>$hash,':f'=>$fullname,':cedula'=>$cedula,':r'=>'admin']);
    echo "Admin user created: {$username}\n";
} catch (PDOException $e) {
    echo "Error creating admin: " . $e->getMessage() . "\n";
}
