<?php
namespace App\Models;
use App\Core\Database;
use PDO;

// Modelo para la tabla Diagnostico
// DIAGNOSTICO CRUD

class DiagnosticoModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Diagnostico");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Diagnostico WHERE ID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO Diagnostico (NombreUnidad, Nombres, Apellidos, Sala, NoExpediente, NoCedula, NoINSS, Fecha, Hora, Planes, Peso, Talla, Edad, Sexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['NombreUnidad'], $data['Nombres'], $data['Apellidos'], $data['Sala'], $data['NoExpediente'], $data['NoCedula'], $data['NoINSS'], $data['Fecha'], $data['Hora'], $data['Planes'], $data['Peso'], $data['Talla'], $data['Edad'], $data['Sexo']
        ]);
        return $this->get($this->pdo->lastInsertId());
    }
    public function update($id, $data) {
        $sql = "UPDATE Diagnostico SET NombreUnidad=?, Nombres=?, Apellidos=?, Sala=?, NoExpediente=?, NoCedula=?, NoINSS=?, Fecha=?, Hora=?, Planes=?, Peso=?, Talla=?, Edad=?, Sexo=? WHERE ID=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['NombreUnidad'], $data['Nombres'], $data['Apellidos'], $data['Sala'], $data['NoExpediente'], $data['NoCedula'], $data['NoINSS'], $data['Fecha'], $data['Hora'], $data['Planes'], $data['Peso'], $data['Talla'], $data['Edad'], $data['Sexo'], $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Diagnostico WHERE ID = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
