<?php

namespace App\Controllers\Api;

use App\Core\ApiResponse;
use App\Core\Database;

class StatsController
{
    public static function overview(): void
    {
        $pdo = Database::pdo();

        // Total patients
        $totalPatients = (int)$pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();

        // Patients by month (last 12 months)
        $patientsByMonth = $pdo->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
             FROM patients
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY ym
             ORDER BY ym ASC"
        )->fetchAll(\PDO::FETCH_ASSOC);

        // Encounters by month (last 12 months)
        $encountersByMonth = $pdo->query(
            "SELECT DATE_FORMAT(encounter_date, '%Y-%m') AS ym, COUNT(*) AS cnt
             FROM encounters
             WHERE encounter_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY ym
             ORDER BY ym ASC"
        )->fetchAll(\PDO::FETCH_ASSOC);

        // Top diagnoses (last 12 months)
        $topDiagnoses = $pdo->query(
            "SELECT type, COUNT(*) AS cnt
             FROM diagnostics
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY type
             ORDER BY cnt DESC
             LIMIT 10"
        )->fetchAll(\PDO::FETCH_ASSOC);

        // Encounters by doctor (last 12 months)
        $encountersByDoctor = $pdo->query(
            "SELECT u.fullname AS doctor, COUNT(*) AS cnt
             FROM encounters e
             LEFT JOIN users u ON u.id = e.attending_user_id
             WHERE e.encounter_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY doctor
             ORDER BY cnt DESC
             LIMIT 10"
        )->fetchAll(\PDO::FETCH_ASSOC);

        ApiResponse::success([
            'totals' => [
                'patients' => $totalPatients,
            ],
            'patients_by_month' => $patientsByMonth,
            'encounters_by_month' => $encountersByMonth,
            'top_diagnoses' => $topDiagnoses,
            'encounters_by_doctor' => $encountersByDoctor,
        ]);
    }
}
