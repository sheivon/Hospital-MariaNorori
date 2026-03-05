<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class PatientModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, first_name, last_name, email, cedula, dob, gender, phone, address, notes, created_at, updated_at FROM patients WHERE deleted_at IS NULL ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, first_name, last_name, email, cedula, dob, gender, phone, address, notes, created_at, updated_at FROM patients WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $this->validate($data);
        $stmt = $this->pdo->prepare('INSERT INTO patients (first_name,last_name,email,cedula,dob,gender,phone,address,notes) VALUES (:fn,:ln,:email,:cedula,:dob,:gender,:phone,:address,:notes)');
        $stmt->execute([
            ':fn' => $data['first_name'] ?? null,
            ':ln' => $data['last_name'] ?? null,
            ':email' => $data['email'] ?? null,
            ':cedula' => $data['cedula'] ?? null,
            ':dob' => !empty($data['dob']) ? $data['dob'] : null,
            ':gender' => $data['gender'] ?? 'O',
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':notes' => $data['notes'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $this->validate($data, $id);
        $stmt = $this->pdo->prepare('UPDATE patients SET first_name=:fn,last_name=:ln,email=:email,cedula=:cedula,dob=:dob,gender=:gender,phone=:phone,address=:address,notes=:notes WHERE id=:id');
        return $stmt->execute([
            ':fn' => $data['first_name'] ?? null,
            ':ln' => $data['last_name'] ?? null,
            ':email' => $data['email'] ?? null,
            ':cedula' => $data['cedula'] ?? null,
            ':dob' => !empty($data['dob']) ? $data['dob'] : null,
            ':gender' => $data['gender'] ?? 'O',
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE patients SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $stmt->execute([':id' => $id]);
    }

    private function validate(array $data, ?int $exceptId = null): void
    {
        $cedula = trim((string)($data['cedula'] ?? ''));
        if ($cedula !== '') {
            if ($exceptId) {
                $stmt = $this->pdo->prepare('SELECT id FROM patients WHERE cedula = :ced AND id != :id AND deleted_at IS NULL LIMIT 1');
                $stmt->execute([':ced' => $cedula, ':id' => $exceptId]);
            } else {
                $stmt = $this->pdo->prepare('SELECT id FROM patients WHERE cedula = :ced AND deleted_at IS NULL LIMIT 1');
                $stmt->execute([':ced' => $cedula]);
            }
            if ($stmt->fetch()) {
                throw new Exception('Cédula already in use');
            }
        }

        $email = trim((string)($data['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email');
        }
    }
}
