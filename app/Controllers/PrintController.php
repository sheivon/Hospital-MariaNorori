<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\PatientModel;
use App\Models\UserModel;
use App\Models\EncounterModel;
use Exception;

class PrintController
{
    /**
     * Return data required to render a printable patient summary.
     *
     * @param int $patientId
     * @return array{patient: array<string,mixed>, diagnostics: array<array<string,mixed>>}
     * @throws Exception if patient does not exist.
     */
    public static function patient(int $patientId): array
    {
        $patient = (new PatientModel())->find($patientId);
        if (!$patient) {
            throw new Exception('Patient not found');
        }

        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT d.*, u1.fullname AS created_by_name, u2.fullname AS updated_by_name
            FROM diagnostics d
            LEFT JOIN users u1 ON d.created_by = u1.id
            LEFT JOIN users u2 ON d.updated_by = u2.id
            WHERE d.patient_id = :pid AND d.deleted_at IS NULL
            ORDER BY d.date DESC, d.id DESC');
        $stmt->execute([':pid' => $patientId]);
        $diagnostics = $stmt->fetchAll(
            \PDO::FETCH_ASSOC
        );

        return ['patient' => $patient, 'diagnostics' => $diagnostics];
    }

    /**
     * Return rows and metadata for a print table by resource.
     *
     * @param string $resource
     * @return array{title:string,columns:array<array<string,string>>,rows:array<array<string,mixed>>}
     */
    public static function datatable(string $resource): array
    {
        $resource = trim(strtolower($resource));
        switch ($resource) {
            case 'users':
                $rows = (new UserModel())->listAdminUsers();
                $title = 'Users';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Username', 'field' => 'username'],
                    ['label' => 'Full name', 'field' => 'fullname'],
                    ['label' => 'Cédula', 'field' => 'cedula'],
                    ['label' => 'Role', 'field' => 'role'],
                    ['label' => 'Specialty', 'field' => 'specialty'],
                    ['label' => 'Department', 'field' => 'department'],
                    ['label' => 'Created at', 'field' => 'created_at'],
                ];
                break;

            case 'patients':
                $rows = (new PatientModel())->all();
                $title = 'Patients';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'First name', 'field' => 'first_name'],
                    ['label' => 'Last name', 'field' => 'last_name'],
                    ['label' => 'Cédula', 'field' => 'cedula'],
                    ['label' => 'Expediente', 'field' => 'expediente_no'],
                    ['label' => 'DOB', 'field' => 'dob'],
                    ['label' => 'Email', 'field' => 'email'],
                    ['label' => 'Phone', 'field' => 'phone'],
                    ['label' => 'Insurance', 'field' => 'insurance_provider'],
                    ['label' => 'Address', 'field' => 'address'],
                    ['label' => 'Created at', 'field' => 'created_at'],
                ];
                break;

            case 'encounters':
                $rows = (new EncounterModel())->all();
                $title = 'Encounters';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Patient', 'field' => 'patient_first_name'],
                    ['label' => 'Patient Last', 'field' => 'patient_last_name'],
                    ['label' => 'Date', 'field' => 'encounter_date'],
                    ['label' => 'Type', 'field' => 'encounter_type'],
                    ['label' => 'Triage', 'field' => 'triage_level'],
                    ['label' => 'Status', 'field' => 'status'],
                    ['label' => 'Doctor', 'field' => 'attending_name'],
                    ['label' => 'Reason', 'field' => 'reason_for_visit'],
                    ['label' => 'Notes', 'field' => 'notes'],
                ];
                break;

            default:
                throw new Exception('Unsupported print resource');
        }

        return [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
        ];
    }
}
