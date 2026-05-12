<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DiagnosticoRepository;
use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;
use App\Repositories\EncounterRepository;
use App\Repositories\TestRepository;
use Exception;

class PrintService
{
    private DiagnosticoRepository $DiagnosticoRepository;
    private PatientRepository $PatientRepository;
    private UserRepository $UserRepository;
    private EncounterRepository $EncounterRepository;
    private TestRepository $TestRepository;

    public function __construct(
        DiagnosticoRepository $DiagnosticoRepository,
        PatientRepository $PatientRepository,
        UserRepository $UserRepository,
        EncounterRepository $EncounterRepository,
        TestRepository $TestRepository
    ) {
        $this->DiagnosticoRepository = $DiagnosticoRepository;
        $this->PatientRepository = $PatientRepository;
        $this->UserRepository = $UserRepository;
        $this->EncounterRepository = $EncounterRepository;
        $this->TestRepository = $TestRepository;
    }

    public function patient(int $patientId): array
    {
        $patient = $this->PatientRepository->find($patientId);
        if ($patient === null) {
            throw new Exception('Patient not found');
        }

        $diagnostics = $this->DiagnosticoRepository->all(['patient_id' => $patientId]);

        return [
            'patient' => $patient,
            'diagnostics' => $diagnostics,
        ];
    }

    public function datatable(string $resource, array $filters = []): array
    {
        $resource = trim(strtolower($resource));

        switch ($resource) {
            case 'users':
                $rows = $this->UserRepository->listAdminUsers();
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
                $rows = $this->PatientRepository->all();
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
                $rows = $this->EncounterRepository->all($filters);
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

            case 'diagnostics':
                $rows = $this->DiagnosticoRepository->all($filters);
                $title = 'Diagnostics';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Patient first name', 'field' => 'patient_first_name'],
                    ['label' => 'Patient last name', 'field' => 'patient_last_name'],
                    ['label' => 'Type', 'field' => 'type'],
                    ['label' => 'Description', 'field' => 'description'],
                    ['label' => 'Date', 'field' => 'date'],
                    ['label' => 'Status', 'field' => 'status'],
                    ['label' => 'Created by', 'field' => 'created_by_name'],
                ];
                break;

            case 'tests':
                $rows = $this->TestRepository->all($filters);
                $title = 'Tests';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Test type', 'field' => 'test_type'],
                    ['label' => 'Patient first name', 'field' => 'patient_first_name'],
                    ['label' => 'Patient last name', 'field' => 'patient_last_name'],
                    ['label' => 'Result', 'field' => 'result'],
                    ['label' => 'Test date', 'field' => 'test_date'],
                    ['label' => 'Created by', 'field' => 'created_by_name'],
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

