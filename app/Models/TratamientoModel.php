<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class TratamientoModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Tratamiento");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Tratamiento WHERE IDConsulta = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO Tratamiento (IDConsulta, Descripcion, Medicamentos, Examenes, Alimentacion, Normas, Seguimiento, Traslado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['IDConsulta'], $data['Descripcion'], $data['Medicamentos'], $data['Examenes'], $data['Alimentacion'], $data['Normas'], $data['Seguimiento'], $data['Traslado']
        ]);
        return $this->get($data['IDConsulta']);
    }
    public function update($id, $data) {
        $sql = "UPDATE Tratamiento SET Descripcion=?, Medicamentos=?, Examenes=?, Alimentacion=?, Normas=?, Seguimiento=?, Traslado=? WHERE IDConsulta=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Descripcion'], $data['Medicamentos'], $data['Examenes'], $data['Alimentacion'], $data['Normas'], $data['Seguimiento'], $data['Traslado'], $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Tratamiento WHERE IDConsulta = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
