<?php
namespace App\Repositories;

use PDO;

class AppointmentsRepository extends \App\Repositories\BaseRepository {

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM appointments");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO appointments (patient_id, provider_user_id, appointment_at, reason, status, notes) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['patient_id'], $data['provider_user_id'], $data['appointment_at'], $data['reason'], $data['status'], $data['notes'] ?? null
        ]);
        return $this->get((int)$this->pdo->lastInsertId());
    }
    public function update($id, $data) {
        $sql = "UPDATE appointments SET patient_id=?, provider_user_id=?, appointment_at=?, reason=?, status=?, notes=? WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['patient_id'], $data['provider_user_id'], $data['appointment_at'], $data['reason'], $data['status'], $data['notes'] ?? null, $id
        ]);
        return $this->get($id);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
