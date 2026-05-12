<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use PDO;

class ChildFollowUpRepository extends \App\Repositories\BaseRepository implements RepositoryInterface
{
    public function all(array $filters = []): array
    {
        $sql = 'SELECT f.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name, p.cedula,
            (SELECT COUNT(*) FROM seguimiento_notas n WHERE n.seguimiento_id = f.id) AS note_count
            FROM seguimiento_integral_ninez_adolescencia f
            LEFT JOIN patients p ON p.id = f.patient_id';

        $params = [];
        $conditions = [];

        if (!empty($filters['id'])) {
            $conditions[] = 'f.id = :id';
            $params[':id'] = (int)$filters['id'];
        }

        if (!empty($filters['patient_id'])) {
            $conditions[] = 'f.patient_id = :patient_id';
            $params[':patient_id'] = (int)$filters['patient_id'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY f.visit_date DESC, f.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM seguimiento_integral_ninez_adolescencia WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO seguimiento_integral_ninez_adolescencia (
                patient_id, encounter_id, visit_date, respira_rapida, dificultad_alimentarse,
                dificultad_respirar, convulsiones, fiebre, malnutricion, vacuna,
                vitamina_a, hierro, buen_trato, relacion_afectivo, notas, created_by
            ) VALUES (
                :patient_id, :encounter_id, :visit_date, :respira_rapida, :dificultad_alimentarse,
                :dificultad_respirar, :convulsiones, :fiebre, :malnutricion, :vacuna,
                :vitamina_a, :hierro, :buen_trato, :relacion_afectivo, :notas, :created_by
            )'
        );

        $stmt->execute([
            ':patient_id' => $data['patient_id'] ?? null,
            ':encounter_id' => $data['encounter_id'] ?? null,
            ':visit_date' => $data['visit_date'] ?? null,
            ':respira_rapida' => !empty($data['respira_rapida']) ? 1 : 0,
            ':dificultad_alimentarse' => !empty($data['dificultad_alimentarse']) ? 1 : 0,
            ':dificultad_respirar' => !empty($data['dificultad_respirar']) ? 1 : 0,
            ':convulsiones' => !empty($data['convulsiones']) ? 1 : 0,
            ':fiebre' => !empty($data['fiebre']) ? 1 : 0,
            ':malnutricion' => !empty($data['malnutricion']) ? 1 : 0,
            ':vacuna' => !empty($data['vacuna']) ? 1 : 0,
            ':vitamina_a' => !empty($data['vitamina_a']) ? 1 : 0,
            ':hierro' => !empty($data['hierro']) ? 1 : 0,
            ':buen_trato' => !empty($data['buen_trato']) ? 1 : 0,
            ':relacion_afectivo' => $data['relacion_afectivo'] ?? null,
            ':notas' => $data['notas'] ?? null,
            ':created_by' => $data['created_by'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE seguimiento_integral_ninez_adolescencia SET
                visit_date = :visit_date,
                respira_rapida = :respira_rapida,
                dificultad_alimentarse = :dificultad_alimentarse,
                dificultad_respirar = :dificultad_respirar,
                convulsiones = :convulsiones,
                fiebre = :fiebre,
                malnutricion = :malnutricion,
                vacuna = :vacuna,
                vitamina_a = :vitamina_a,
                hierro = :hierro,
                buen_trato = :buen_trato,
                relacion_afectivo = :relacion_afectivo,
                notas = :notas,
                updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            ':visit_date' => $data['visit_date'] ?? null,
            ':respira_rapida' => !empty($data['respira_rapida']) ? 1 : 0,
            ':dificultad_alimentarse' => !empty($data['dificultad_alimentarse']) ? 1 : 0,
            ':dificultad_respirar' => !empty($data['dificultad_respirar']) ? 1 : 0,
            ':convulsiones' => !empty($data['convulsiones']) ? 1 : 0,
            ':fiebre' => !empty($data['fiebre']) ? 1 : 0,
            ':malnutricion' => !empty($data['malnutricion']) ? 1 : 0,
            ':vacuna' => !empty($data['vacuna']) ? 1 : 0,
            ':vitamina_a' => !empty($data['vitamina_a']) ? 1 : 0,
            ':hierro' => !empty($data['hierro']) ? 1 : 0,
            ':buen_trato' => !empty($data['buen_trato']) ? 1 : 0,
            ':relacion_afectivo' => $data['relacion_afectivo'] ?? null,
            ':notas' => $data['notas'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM seguimiento_integral_ninez_adolescencia WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
