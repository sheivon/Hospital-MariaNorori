<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

class StatsService
{
    public function overview(): array
    {
        $pdo = Database::pdo();

        $totalPatients = (int) $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();

        $patientsByMonth = $pdo->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
             FROM patients
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY ym
             ORDER BY ym ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $encountersByMonth = $pdo->query(
            "SELECT DATE_FORMAT(encounter_date, '%Y-%m') AS ym, COUNT(*) AS cnt
             FROM encounters
             WHERE encounter_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY ym
             ORDER BY ym ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $topDiagnoses = $pdo->query(
            "SELECT type, COUNT(*) AS cnt
             FROM diagnostics
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY type
             ORDER BY cnt DESC
             LIMIT 10"
        )->fetchAll(PDO::FETCH_ASSOC);

        $encountersByDoctor = $pdo->query(
            "SELECT u.fullname AS doctor, COUNT(*) AS cnt
             FROM encounters e
             LEFT JOIN users u ON u.id = e.attending_user_id
             WHERE e.encounter_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY doctor
             ORDER BY cnt DESC
             LIMIT 10"
        )->fetchAll(PDO::FETCH_ASSOC);

        return [
            'totals' => [
                'patients' => $totalPatients,
            ],
            'patients_by_month' => $patientsByMonth,
            'encounters_by_month' => $encountersByMonth,
            'top_diagnoses' => $topDiagnoses,
            'encounters_by_doctor' => $encountersByDoctor,
        ];
    }
}
