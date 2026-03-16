<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class PacienteModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Paciente");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($cedula) {
        $stmt = $this->pdo->prepare("SELECT * FROM Paciente WHERE Cedula = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO Paciente (Cedula, Nombre, Apellidos, Sexo, FechaNacimiento, Direccion, Telefono, NoINSS, EstadoCivil, Escolaridad, NombrePadre, NombreMadre, Ocupacion, Empleador, NoExpediente, Procedencia, Edad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Cedula'], $data['Nombre'], $data['Apellidos'], $data['Sexo'], $data['FechaNacimiento'], $data['Direccion'], $data['Telefono'], $data['NoINSS'], $data['EstadoCivil'], $data['Escolaridad'], $data['NombrePadre'], $data['NombreMadre'], $data['Ocupacion'], $data['Empleador'], $data['NoExpediente'], $data['Procedencia'], $data['Edad']
        ]);
        return $this->get($data['Cedula']);
    }
    public function update($cedula, $data) {
        $sql = "UPDATE Paciente SET Nombre=?, Apellidos=?, Sexo=?, FechaNacimiento=?, Direccion=?, Telefono=?, NoINSS=?, EstadoCivil=?, Escolaridad=?, NombrePadre=?, NombreMadre=?, Ocupacion=?, Empleador=?, NoExpediente=?, Procedencia=?, Edad=? WHERE Cedula=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['Nombre'], $data['Apellidos'], $data['Sexo'], $data['FechaNacimiento'], $data['Direccion'], $data['Telefono'], $data['NoINSS'], $data['EstadoCivil'], $data['Escolaridad'], $data['NombrePadre'], $data['NombreMadre'], $data['Ocupacion'], $data['Empleador'], $data['NoExpediente'], $data['Procedencia'], $data['Edad'], $cedula
        ]);
        return $this->get($cedula);
    }
    public function delete($cedula) {
        $stmt = $this->pdo->prepare("DELETE FROM Paciente WHERE Cedula = ?");
        $stmt->execute([$cedula]);
        return $stmt->rowCount();
    }
}
