<?php

namespace App\Modules\Patients;

use App\Modules\BaseModule;

class PatientsModule extends BaseModule
{
    public function getSlug(): string
    {
        return 'patients';
    }

    public function getLabel(): string
    {
        return 'Patients';
    }

    public function getLabelKey(): string
    {
        return 'patients';
    }

    public function getIcon(): string
    {
        return 'fa-users';
    }

    public function getAllowedRoles(): array
    {
        return ['admin', 'doctor', 'user'];
    }

    public function getSubItems(): array
    {
        return [
            ['path' => '/pacientes.php', 'label' => 'Ver pacientes', 'labelKey' => 'view_patients'],
            ['path' => '/appointments.php', 'label' => 'Appointments', 'labelKey' => 'Appointments'],
            ['path' => '/tests.php', 'label' => 'Test Results', 'labelKey' => 'tests_results'],
            ['path' => '/adolescent_history.php', 'label' => 'Adolescent History', 'labelKey' => 'adolescent_history'],
            ['path' => '/seguimiento_integral_ninez_adolescencia.php', 'label' => 'Seguimiento Infantíl', 'labelKey' => 'child_followups'],
            ['path' => '/alergias.php', 'label' => 'Alergias', 'labelKey' => 'allergies'],
            ['path' => '/diagnostics.php', 'label' => 'Diagnostics', 'labelKey' => 'diagnostics_title'],
            ['path' => '/medications.php', 'label' => 'Medications Catalog', 'labelKey' => 'medications_catalog'],
            ['path' => '/vitals.php', 'label' => 'Vitals', 'labelKey' => 'vitals_title'],
            ['path' => '/examen.php', 'label' => 'Exámenes', 'labelKey' => 'exams_title'],
            ['path' => '/solicitud_de_examen.php', 'label' => 'Solicitud de examen', 'labelKey' => 'exam_request'],
            ['path' => '/radiologia.php', 'label' => 'Radiología', 'labelKey' => 'radiology'],

        ];
    }
}
