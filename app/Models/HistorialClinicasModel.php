<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class HistorialClinicasModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM HistorialClinicas");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM HistorialClinicas WHERE IDHistorial = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO HistorialClinicas (Nombres, PrimerApellido, SegundoApellido, Direccion, NoINSS, SILAS, Municipio, UnidadSalud, NoExpediente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Nombres'], $data['PrimerApellido'], $data['SegundoApellido'], $data['Direccion'], $data['NoINSS'], $data['SILAS'], $data['Municipio'], $data['UnidadSalud'], $data['NoExpediente']
        ]);
        return $this->get($this->pdo->lastInsertId());
    }
    public function update($id, $data) {
        $sql = "UPDATE HistorialClinicas SET Nombres=?, PrimerApellido=?, SegundoApellido=?, Direccion=?, NoINSS=?, SILAS=?, Municipio=?, UnidadSalud=?, NoExpediente=? WHERE IDHistorial=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Nombres'], $data['PrimerApellido'], $data['SegundoApellido'], $data['Direccion'], $data['NoINSS'], $data['SILAS'], $data['Municipio'], $data['UnidadSalud'], $data['NoExpediente'], $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM HistorialClinicas WHERE IDHistorial = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
