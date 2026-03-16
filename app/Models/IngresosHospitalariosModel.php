<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class IngresosHospitalariosModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM IngresosHospitalarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM IngresosHospitalarios WHERE IDIngreso = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO IngresosHospitalarios (FechaIngreso, FechaAlta, IDDepartamento, IDPaciente, DiagnosticoIngreso, HoraIngreso) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['FechaIngreso'], $data['FechaAlta'], $data['IDDepartamento'], $data['IDPaciente'], $data['DiagnosticoIngreso'], $data['HoraIngreso']
        ]);
        return $this->get($this->pdo->lastInsertId());
    }
    public function update($id, $data) {
        $sql = "UPDATE IngresosHospitalarios SET FechaIngreso=?, FechaAlta=?, IDDepartamento=?, IDPaciente=?, DiagnosticoIngreso=?, HoraIngreso=? WHERE IDIngreso=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['FechaIngreso'], $data['FechaAlta'], $data['IDDepartamento'], $data['IDPaciente'], $data['DiagnosticoIngreso'], $data['HoraIngreso'], $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM IngresosHospitalarios WHERE IDIngreso = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
