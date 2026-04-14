<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DiagnosticoModel;
use App\Models\PatientModel;
use App\Models\UserModel;
use App\Models\EncounterModel;
use Exception;

class PrintService
{
    private DiagnosticoModel $diagnosticoModel;
    private PatientModel $patientModel;
    private UserModel $userModel;
    private EncounterModel $encounterModel;

    public function __construct(
        DiagnosticoModel $diagnosticoModel,
        PatientModel $patientModel,
        UserModel $userModel,
        EncounterModel $encounterModel
    ) {
        $this->diagnosticoModel = $diagnosticoModel;
        $this->patientModel = $patientModel;
        $this->userModel = $userModel;
        $this->encounterModel = $encounterModel;
    }

    public function patient(int $patientId): array
    {
        $patient = $this->patientModel->find($patientId);
        if ($patient === null) {
            throw new Exception('Patient not found');
        }

        $diagnostics = $this->diagnosticoModel->all(['patient_id' => $patientId]);

        return [
            'patient' => $patient,
            'diagnostics' => $diagnostics,
        ];
    }

    public function datatable(string $resource): array
    {
        $resource = trim(strtolower($resource));

        switch ($resource) {
            case 'users':
                $rows = $this->userModel->listAdminUsers();
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
                $rows = $this->patientModel->all();
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
                $rows = $this->encounterModel->all();
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
