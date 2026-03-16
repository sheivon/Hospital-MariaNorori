<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class ExamenesModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Examenes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Examenes WHERE IDExamen = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO Examenes (Unidad, Nombres, Apellidos, Edad, Sexo, Fecha, NoExpediente, Localidad, Codigo, NoCama, Servicio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Unidad'], $data['Nombres'], $data['Apellidos'], $data['Edad'], $data['Sexo'], $data['Fecha'], $data['NoExpediente'], $data['Localidad'], $data['Codigo'], $data['NoCama'], $data['Servicio']
        ]);
        return $this->get($this->pdo->lastInsertId());
    }
    public function update($id, $data) {
        $sql = "UPDATE Examenes SET Unidad=?, Nombres=?, Apellidos=?, Edad=?, Sexo=?, Fecha=?, NoExpediente=?, Localidad=?, Codigo=?, NoCama=?, Servicio=? WHERE IDExamen=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Unidad'], $data['Nombres'], $data['Apellidos'], $data['Edad'], $data['Sexo'], $data['Fecha'], $data['NoExpediente'], $data['Localidad'], $data['Codigo'], $data['NoCama'], $data['Servicio'], $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Examenes WHERE IDExamen = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
