<?php

namespace App\Repositories;

use PDO;

class SeguimientoPediatricRepository extends BaseRepository
{
    public function getList(int $limit = 200)
    {
        $sql = 'SELECT s.*,
                       CONCAT(p.first_name, " ", p.last_name) AS patient_name,
                       p.dob AS patient_dob
                FROM seguimiento_pediatric s
                LEFT JOIN patients p ON p.id = s.patient_id
                ORDER BY s.visit_date DESC, s.id DESC
                LIMIT ' . (int)$limit;
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT s.*, 
                                            CONCAT(p.first_name, " ", p.last_name) AS patient_name,
                                            p.dob AS patient_dob
                                     FROM seguimiento_pediatric s
                                     LEFT JOIN patients p ON p.id = s.patient_id
                                     WHERE s.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data)
    {
        // Add robust column handling to automatically ignore non-matching fields if necessary
        // Using PDO directly 
        $columns = [
            'patient_id', 'encounter_id', 'visit_date', 'reason_for_consultation',
            'personal_pathological_history', 'risk_factors', 'family_pathological_history',
            'family_environment', 'education_work_living', 'activities_social', 'physical_activity',
            'respira_rapida', 'dificultad_alimentarse', 'dificultad_respirar', 'convulsiones',
            'letargia', 'inconciencia', 'flacidez', 'vomitos', 'diarrea', 'dias_diarrea',
            'fiebre', 'fiebre_mas_7_dias', 'cianosis_central', 'ombligo_rojizo', 'ombligo_supurando',
            'pustulas_extensas', 'pustulas_escasas', 'tiraje_subcostal', 'placas_blancas_bucales',
            'hipotermia', 'se_ve_mal', 'supuracion_oido', 'supuracion_ojos', 'manifestacion_sangrado',
            'distension_abdominal', 'apnea', 'quejido', 'aleteo_nasal', 'palidez_intensa',
            'llenado_capilar_lento', 'fontanela_abombada', 'sangrado_heces', 'anormalmente_somnoliento',
            'ojos_hundidos', 'inquieto_irritable', 'peso_g', 'talla_cm', 'perimetro_cefalico_cm',
            'imc', 'peso_edad', 'talla_edad', 'peso_talla', 'edema_pies', 'emaciacion', 'malnutricion',
            'lactancia_materna', 'lactancia_nocturna', 'lactancia_mas_8_veces', 'otros_liquidos',
            'uso_biberon', 'problemas_posicion', 'problemas_agarre', 'problemas_succion', 'vacuna',
            'vacuna_edad', 'edad', 'DOB', 'gender'
        ];

        $insertCols = [];
        $insertVals = [];
        $placeholders = [];

        foreach ($columns as $c) {
            if (array_key_exists($c, $data)) {
                $insertCols[] = $c;
                $insertVals[] = $data[$c] === '' ? null : $data[$c];
                $placeholders[] = '?';
            }
        }

        $sql = sprintf(
            'INSERT INTO seguimiento_pediatric (%s) VALUES (%s)',
            implode(', ', $insertCols),
            implode(', ', $placeholders)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($insertVals);
        return $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data)
    {
        $columns = [
            'patient_id', 'encounter_id', 'visit_date', 'reason_for_consultation',
            'personal_pathological_history', 'risk_factors', 'family_pathological_history',
            'family_environment', 'education_work_living', 'activities_social', 'physical_activity',
            'respira_rapida', 'dificultad_alimentarse', 'dificultad_respirar', 'convulsiones',
            'letargia', 'inconciencia', 'flacidez', 'vomitos', 'diarrea', 'dias_diarrea',
            'fiebre', 'fiebre_mas_7_dias', 'cianosis_central', 'ombligo_rojizo', 'ombligo_supurando',
            'pustulas_extensas', 'pustulas_escasas', 'tiraje_subcostal', 'placas_blancas_bucales',
            'hipotermia', 'se_ve_mal', 'supuracion_oido', 'supuracion_ojos', 'manifestacion_sangrado',
            'distension_abdominal', 'apnea', 'quejido', 'aleteo_nasal', 'palidez_intensa',
            'llenado_capilar_lento', 'fontanela_abombada', 'sangrado_heces', 'anormalmente_somnoliento',
            'ojos_hundidos', 'inquieto_irritable', 'peso_g', 'talla_cm', 'perimetro_cefalico_cm',
            'imc', 'peso_edad', 'talla_edad', 'peso_talla', 'edema_pies', 'emaciacion', 'malnutricion',
            'lactancia_materna', 'lactancia_nocturna', 'lactancia_mas_8_veces', 'otros_liquidos',
            'uso_biberon', 'problemas_posicion', 'problemas_agarre', 'problemas_succion', 'vacuna',
            'vacuna_edad', 'edad', 'DOB', 'gender'
        ];

        $setParts = [];
        $updateVals = [];

        foreach ($columns as $c) {
            if (array_key_exists($c, $data)) {
                $setParts[] = "$c = ?";
                $updateVals[] = $data[$c] === '' ? null : $data[$c];
            }
        }

        if (empty($setParts)) {
            return false;
        }

        $updateVals[] = $id;

        $sql = sprintf(
            'UPDATE seguimiento_pediatric SET %s WHERE id = ?',
            implode(', ', $setParts)
        );

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($updateVals);
    }

    public function delete(int $id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM seguimiento_pediatric WHERE id = ?');
        return $stmt->execute([$id]);
    }
}