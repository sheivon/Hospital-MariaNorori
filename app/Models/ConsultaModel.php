<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class ConsultaModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Consulta");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Consulta WHERE IDConsulta = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO Consulta (Fecha, IDPaciente, IDMedico, TipoConsulta) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Fecha'], $data['IDPaciente'], $data['IDMedico'], $data['TipoConsulta']
        ]);
        return $this->get($this->pdo->lastInsertId());
    }
    public function update($id, $data) {
        $sql = "UPDATE Consulta SET Fecha=?, IDPaciente=?, IDMedico=?, TipoConsulta=? WHERE IDConsulta=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Fecha'], $data['IDPaciente'], $data['IDMedico'], $data['TipoConsulta'], $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Consulta WHERE IDConsulta = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
