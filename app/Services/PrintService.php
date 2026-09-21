<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DiagnosticoRepository;
use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;
use App\Repositories\EncounterRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\PatientAllergyRepository;
use App\Repositories\ExamRequestRepository;
use App\Repositories\TableCrudRepository;
use App\Repositories\SeguimientoPediatricRepository;
use Exception;

class PrintService
{
    private DiagnosticoRepository $DiagnosticoRepository;
    private PatientRepository $PatientRepository;
    private UserRepository $UserRepository;
    private EncounterRepository $EncounterRepository;
    private AppointmentRepository $AppointmentRepository;
    private PatientAllergyRepository $PatientAllergyRepository;
    private ExamRequestRepository $ExamRequestRepository;
    public function __construct(
        DiagnosticoRepository $DiagnosticoRepository,
        PatientRepository $PatientRepository,
        UserRepository $UserRepository,
        EncounterRepository $EncounterRepository,
        AppointmentRepository $AppointmentRepository,
        PatientAllergyRepository $PatientAllergyRepository = null,
        ExamRequestRepository $ExamRequestRepository = null
    ) {
        $this->DiagnosticoRepository = $DiagnosticoRepository;
        $this->PatientRepository = $PatientRepository;
        $this->UserRepository = $UserRepository;
        $this->EncounterRepository = $EncounterRepository;
        $this->AppointmentRepository = $AppointmentRepository;
        $this->PatientAllergyRepository = $PatientAllergyRepository ?? new PatientAllergyRepository();
        $this->ExamRequestRepository = $ExamRequestRepository ?? new ExamRequestRepository();
    }

    /**
     * Return a single appointment for the per-record print view.
     */
    public function appointment(int $id): array
    {
        $appointment = $this->AppointmentRepository->find($id);
        if ($appointment === null) {
            throw new Exception('Appointment not found');
        }
        return ['appointment' => $appointment];
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

    public function datatable(string $resource, array $filters = [], string $lang = 'en'): array
    {
        $resource = trim(strtolower($resource));

        switch ($resource) {
            case 'users':
                $rows = $this->UserRepository->listAdminUsers();
                foreach ($rows as &$row) {
                    $row['status'] = !empty($row['is_active']) ? 'Active' : 'Inactive';
                }
                unset($row);
                $title = 'Users';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Username', 'field' => 'username'],
                    ['label' => 'Full name', 'field' => 'fullname'],
                    ['label' => 'Cédula', 'field' => 'cedula'],
                    ['label' => 'Role', 'field' => 'role'],
                    ['label' => 'Specialty', 'field' => 'specialty'],
                    ['label' => 'Department', 'field' => 'department'],
                    ['label' => 'Status', 'field' => 'status'],
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
                if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
                    $rows = $this->filterByDateRange($rows, 'encounter_date', $filters['date_from'] ?? null, $filters['date_to'] ?? null);
                }
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

            case 'appointments':
                $rows = $this->AppointmentRepository->all($filters);
                if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
                    $rows = $this->filterByDateRange($rows, 'appointment_at', $filters['date_from'] ?? null, $filters['date_to'] ?? null);
                }
                $title = 'Appointments';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Patient', 'field' => 'patient_name'],
                    ['label' => 'Provider', 'field' => 'provider_name'],
                    ['label' => 'Date & time', 'field' => 'appointment_at'],
                    ['label' => 'Reason', 'field' => 'reason'],
                    ['label' => 'Status', 'field' => 'status'],
                    ['label' => 'Notes', 'field' => 'notes'],
                ];
                break;

            case 'allergies':
                $rows = $this->PatientAllergyRepository->all(
                    isset($filters['patient_id']) && (int)$filters['patient_id'] > 0 ? (int)$filters['patient_id'] : null
                );
                $title = 'Allergies';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Patient', 'field' => 'patient_name'],
                    ['label' => 'Allergen', 'field' => 'allergen'],
                    ['label' => 'Reaction', 'field' => 'reaction'],
                    ['label' => 'Severity', 'field' => 'severity'],
                    ['label' => 'Status', 'field' => 'status'],
                    ['label' => 'Noted date', 'field' => 'noted_date'],
                    ['label' => 'Notes', 'field' => 'notes'],
                ];
                break;

            case 'diagnostics':
                if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
                    $rows = $this->filterByDateRange($rows, 'date', $filters['date_from'] ?? null, $filters['date_to'] ?? null);
                }
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

            case 'treatments':
                $rows = (new TableCrudRepository())->listRows('medications_catalog', 500);
                $title = 'Treatments';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Medication name', 'field' => 'medication_name'],
                    ['label' => 'Generic name', 'field' => 'generic_name'],
                    ['label' => 'Form', 'field' => 'form'],
                    ['label' => 'Strength', 'field' => 'strength'],
                ];
                break;

            case 'seguimiento_pediatric':
                $rows = (new SeguimientoPediatricRepository())->getList(500);
                $title = 'Pediatric Follow-up';
                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Patient', 'field' => 'patient_name'],
                    ['label' => 'Visit date', 'field' => 'visit_date'],
                ];
                break;

            case 'emergency':
                $rows = $this->EncounterRepository->all(['encounter_type' => 'emergency']);
                if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
                    $rows = $this->filterByDateRange($rows, 'encounter_date', $filters['date_from'] ?? null, $filters['date_to'] ?? null);
                }

                foreach ($rows as &$row) {
                    $row['patient'] = trim(
                        ($row['patient_first_name'] ?? '') . ' ' .
                        ($row['patient_last_name'] ?? '')
                    );

                    $row['admission_date'] = $row['admission_date'] ?? '';
                    $formData = is_array($row['form_data_decoded'] ?? null) ? $row['form_data_decoded'] : [];
                    $row['service'] = $formData['admission_service'] ?? '';
                    $row['diagnosis'] = $formData['admission_diagnosis'] ?? '';
                }

                $title = 'Emergency';

                $columns = [
                    ['label' => 'ID', 'field' => 'id'],
                    ['label' => 'Patient', 'field' => 'patient'],
                    ['label' => 'Cédula', 'field' => 'cedula'],
                    ['label' => 'Admission', 'field' => 'admission_date'],
                    ['label' => 'Service', 'field' => 'service'],
                    ['label' => 'Diagnosis', 'field' => 'diagnosis'],
                    ['label' => 'Status', 'field' => 'status'],
                ];
                break;

            case 'patient_history':
                $rows = $this->EncounterRepository->historyReport($filters);

                foreach ($rows as &$row) {
                    $row['patient'] = trim(
                        ($row['first_name'] ?? '') . ' ' .
                        ($row['last_name'] ?? '')
                    );
                }
                unset($row);

                $title = 'Patient History';
                $columns = [
                    ['label' => 'Patient', 'field' => 'patient'],
                    ['label' => 'Cédula', 'field' => 'cedula'],
                    ['label' => 'DOB', 'field' => 'dob'],
                    ['label' => 'Encounter date', 'field' => 'encounter_date'],
                    ['label' => 'Type', 'field' => 'encounter_type'],
                    ['label' => 'Triage', 'field' => 'triage_level'],
                    ['label' => 'Status', 'field' => 'status'],
                    ['label' => 'Doctor', 'field' => 'attending_name'],
                    ['label' => 'Reason for visit', 'field' => 'reason_for_visit'],
                    ['label' => 'Notes', 'field' => 'notes'],
                    ['label' => 'Diagnostics', 'field' => 'diagnostics_count'],
                    ['label' => 'Tests', 'field' => 'tests_count'],
                    ['label' => 'Vitals', 'field' => 'vitals_count'],
                ];
                break;

            case 'patient_full':
                return $this->patientFull($filters);

            default:
                throw new Exception('Unsupported print resource');
        }

        foreach ($columns as &$column) {
            $column['label'] = $this->localize($column['label'], $lang);
        }
        unset($column);
        $title = $this->localize($title, $lang);

        return [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
        ];
    }

    private function localize(string $text, string $lang): string
    {
        if ($lang !== 'es') {
            return $text;
        }
        $map = [
            'ID' => 'ID',
            'Username' => 'Usuario',
            'Full name' => 'Nombre completo',
            'Cédula' => 'Cédula',
            'Role' => 'Rol',
            'Specialty' => 'Especialidad',
            'Department' => 'Departamento',
            'Status' => 'Estado',
            'Created at' => 'Creado el',
            'Active' => 'Activo',
            'Inactive' => 'Inactivo',
            'First name' => 'Nombre',
            'Last name' => 'Apellido',
            'Expediente' => 'Expediente',
            'DOB' => 'Fecha de nacimiento',
            'Address' => 'Dirección',
            'Phone' => 'Teléfono',
            'Email' => 'Correo',
            'Insurance' => 'Aseguradora',
            'Patient' => 'Paciente',
            'Patient Last' => 'Apellido del paciente',
            'Patient first name' => 'Nombre del paciente',
            'Patient last name' => 'Apellido del paciente',
            'Date' => 'Fecha',
            'Date & time' => 'Fecha y hora',
            'Type' => 'Tipo',
            'Triage' => 'Triaje',
            'Doctor' => 'Médico',
            'Provider' => 'Proveedor',
            'Reason' => 'Motivo',
            'Reason for visit' => 'Motivo de visita',
            'Notes' => 'Notas',
            'Allergen' => 'Alérgeno',
            'Reaction' => 'Reacción',
            'Severity' => 'Gravedad',
            'Noted date' => 'Fecha de registro',
            'Description' => 'Descripción',
            'Created by' => 'Creado por',
            'Admission' => 'Ingreso',
            'Service' => 'Servicio',
            'Diagnosis' => 'Diagnóstico',
            'Encounter date' => 'Fecha de consulta',
            'Diagnostics' => 'Diagnósticos',
            'Tests' => 'Pruebas',
            'Vitals' => 'Signos vitales',
            'Medication name' => 'Nombre del medicamento',
            'Generic name' => 'Nombre genérico',
            'Form' => 'Forma',
            'Strength' => 'Dosis',
            'Visit date' => 'Fecha de visita',
            'Users' => 'Usuarios',
            'Patients' => 'Pacientes',
            'Encounters' => 'Consultas',
            'Appointments' => 'Citas',
            'Allergies' => 'Alergias',
            'Emergency' => 'Emergencia',
            'Patient History' => 'Historial del paciente',
            'Treatments' => 'Tratamientos',
            'Pediatric Follow-up' => 'Seguimiento pediátrico',
        ];
        return $map[$text] ?? $text;
    }

    /**
     * Return all patient-related data grouped by section for the full record print view.
     */
    public function patientFull(array $filters): array
    {
        $patientId = $filters['patient_id'] ?? null;
        if (!$patientId) {
            throw new Exception('Patient ID is required for full record');
        }

        $patient = $this->PatientRepository->find((int)$patientId);
        if ($patient === null) {
            throw new Exception('Patient not found');
        }

        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        $encounters = $this->EncounterRepository->all(['patient_id' => (int)$patientId]);
        $diagnostics = $this->DiagnosticoRepository->all(['patient_id' => (int)$patientId]);
        $appointments = $this->AppointmentRepository->all(['patient_id' => (int)$patientId]);
        $allergies = $this->PatientAllergyRepository->all((int)$patientId);
        $exams = $this->ExamRequestRepository->all(['patient_id' => (int)$patientId]);

        // Apply date range filter if dates are set
        if ($dateFrom || $dateTo) {
            $encounters = $this->filterByDateRange($encounters, 'encounter_date', $dateFrom, $dateTo);
            $diagnostics = $this->filterByDateRange($diagnostics, 'date', $dateFrom, $dateTo);
            $appointments = $this->filterByDateRange($appointments, 'appointment_at', $dateFrom, $dateTo);
            $exams = $this->filterByDateRange($exams, 'request_date', $dateFrom, $dateTo);
        }

        return [
            'title' => 'Patient Complete Record',
            'patient' => $patient,
            'encounters' => $encounters,
            'diagnostics' => $diagnostics,
            'appointments' => $appointments,
            'allergies' => $allergies,
            'exams' => $exams,
        ];
    }

    private function filterByDateRange(array $rows, string $dateField, ?string $from, ?string $to): array
    {
        if (!$from && !$to) {
            return $rows;
        }

        return array_values(array_filter($rows, function ($row) use ($dateField, $from, $to) {
            $date = substr($row[$dateField] ?? '', 0, 10);
            if ($date === '') {
                return false;
            }
            if ($from && $date < $from) {
                return false;
            }
            if ($to && $date > $to) {
                return false;
            }
            return true;
        }));
    }
}

