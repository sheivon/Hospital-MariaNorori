<?php
require_once __DIR__ . '/../config/db.php';

function get_patients() {
    global $pdo;
    $stmt = $pdo->query('SELECT id, first_name, last_name, email, cedula, dob, gender, phone, address, notes, created_at, updated_at FROM patients ORDER BY id DESC');
    return $stmt->fetchAll();
}

function get_patient($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, cedula, dob, gender, phone, address, notes, created_at, updated_at FROM patients WHERE id = :id LIMIT 1');
    $stmt->execute([':id'=>$id]);
    return $stmt->fetch();
}

function create_patient($data) {
    global $pdo;
    // server-side cedula uniqueness check
    $ced = trim($data['cedula'] ?? '');
    if ($ced !== ''){
        $stmt = $pdo->prepare('SELECT id FROM patients WHERE cedula = :ced LIMIT 1');
        $stmt->execute([':ced'=>$ced]);
        if ($stmt->fetch()) throw new Exception('Cédula already in use');
    }
    // email basic validation (optional)
    $email = trim($data['email'] ?? '');
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email');
    }
    $stmt = $pdo->prepare('INSERT INTO patients (first_name,last_name,email,cedula,dob,gender,phone,address,notes) VALUES (:fn,:ln,:email,:cedula,:dob,:gender,:phone,:address,:notes)');
    $stmt->execute([
        ':fn'=>$data['first_name'] ?? null,
        ':ln'=>$data['last_name'] ?? null,
        ':email'=>$data['email'] ?? null,
        ':cedula'=>$data['cedula'] ?? null,
        ':dob'=>!empty($data['dob']) ? $data['dob'] : null,
        ':gender'=>$data['gender'] ?? 'O',
        ':phone'=>$data['phone'] ?? null,
        ':address'=>$data['address'] ?? null,
        ':notes'=>$data['notes'] ?? null,
    ]);
    return $pdo->lastInsertId();
}

function update_patient($id, $data) {
    global $pdo;
    // server-side cedula uniqueness check (exclude current id)
    $ced = trim($data['cedula'] ?? '');
    if ($ced !== ''){
        $stmt = $pdo->prepare('SELECT id FROM patients WHERE cedula = :ced AND id != :id LIMIT 1');
        $stmt->execute([':ced'=>$ced, ':id'=>$id]);
        if ($stmt->fetch()) throw new Exception('Cédula already in use');
    }
    // email basic validation (optional)
    $email = trim($data['email'] ?? '');
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email');
    }
    $stmt = $pdo->prepare('UPDATE patients SET first_name=:fn,last_name=:ln,email=:email,cedula=:cedula,dob=:dob,gender=:gender,phone=:phone,address=:address,notes=:notes WHERE id=:id');
    return $stmt->execute([
        ':fn'=>$data['first_name'] ?? null,
        ':ln'=>$data['last_name'] ?? null,
        ':email'=>$data['email'] ?? null,
        ':cedula'=>$data['cedula'] ?? null,
        ':dob'=>!empty($data['dob']) ? $data['dob'] : null,
        ':gender'=>$data['gender'] ?? 'O',
        ':phone'=>$data['phone'] ?? null,
        ':address'=>$data['address'] ?? null,
        ':notes'=>$data['notes'] ?? null,
        ':id'=>$id,
    ]);
}

function delete_patient($id) {
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM patients WHERE id = :id');
    return $stmt->execute([':id'=>$id]);
}
