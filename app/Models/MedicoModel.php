<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class MedicoModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Medico");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Medico WHERE IDMedico = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO Medico (Nombre, Especialidad, IDDepartamento) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Nombre'], $data['Especialidad'], $data['IDDepartamento']
        ]);
        return $this->get($this->pdo->lastInsertId());
    }
    public function update($id, $data) {
        $sql = "UPDATE Medico SET Nombre=?, Especialidad=?, IDDepartamento=? WHERE IDMedico=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Nombre'], $data['Especialidad'], $data['IDDepartamento'], $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Medico WHERE IDMedico = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
