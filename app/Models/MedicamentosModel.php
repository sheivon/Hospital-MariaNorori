<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class MedicamentosModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Medicamentos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Medicamentos WHERE IDMedicamento = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO Medicamentos (Nombres, Apellidos, Fecha, Hora, Sala, NombreUnidadSalud, NoExpediente, NoCedula, NoCama, NombreMedicamentos, NombreEnfermera, Codigo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Nombres'], $data['Apellidos'], $data['Fecha'], $data['Hora'], $data['Sala'], $data['NombreUnidadSalud'], $data['NoExpediente'], $data['NoCedula'], $data['NoCama'], $data['NombreMedicamentos'], $data['NombreEnfermera'], $data['Codigo']
        ]);
        return $this->get($this->pdo->lastInsertId());
    }
    public function update($id, $data) {
        $sql = "UPDATE Medicamentos SET Nombres=?, Apellidos=?, Fecha=?, Hora=?, Sala=?, NombreUnidadSalud=?, NoExpediente=?, NoCedula=?, NoCama=?, NombreMedicamentos=?, NombreEnfermera=?, Codigo=? WHERE IDMedicamento=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Nombres'], $data['Apellidos'], $data['Fecha'], $data['Hora'], $data['Sala'], $data['NombreUnidadSalud'], $data['NoExpediente'], $data['NoCedula'], $data['NoCama'], $data['NombreMedicamentos'], $data['NombreEnfermera'], $data['Codigo'], $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Medicamentos WHERE IDMedicamento = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
