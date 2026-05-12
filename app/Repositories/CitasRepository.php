<?php
namespace App\Repositories;

use PDO;

class CitasRepository extends \App\Repositories\BaseRepository {
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Citas");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Citas WHERE IDCita = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO Citas (IDPaciente, IDMedico, Fecha, Hora, MotivoCita, Estado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['IDPaciente'], $data['IDMedico'], $data['Fecha'], $data['Hora'], $data['MotivoCita'], $data['Estado']
        ]);
        return $this->get($this->pdo->lastInsertId());
    }
    public function update($id, $data) {
        $sql = "UPDATE Citas SET IDPaciente=?, IDMedico=?, Fecha=?, Hora=?, MotivoCita=?, Estado=? WHERE IDCita=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['IDPaciente'], $data['IDMedico'], $data['Fecha'], $data['Hora'], $data['MotivoCita'], $data['Estado'], $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Citas WHERE IDCita = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}

