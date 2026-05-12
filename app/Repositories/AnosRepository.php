<?php
namespace App\Repositories;

use PDO;

class AnosRepository extends \App\Repositories\BaseRepository {
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Anos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Anos WHERE IDAnos = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO Anos (IDAnos) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$data['IDAnos']]);
        return $this->get($data['IDAnos']);
    }
    public function update($id, $data) {
        $sql = "UPDATE Anos SET IDAnos=? WHERE IDAnos=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$data['IDAnos'], $id]);
        return $this->get($data['IDAnos']);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Anos WHERE IDAnos = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}

