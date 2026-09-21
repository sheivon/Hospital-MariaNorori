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
            ['path' => '/patients.php', 'label' => 'Ver pacientes', 'labelKey' => 'view_patients'],
            ['path' => '/appointments.php', 'label' => 'Appointments', 'labelKey' => 'Appointments'],
            ['path' => '/alergias.php', 'label' => 'Alergias', 'labelKey' => 'allergies'],
            ['path' => '/diagnostics.php', 'label' => 'Diagnostics', 'labelKey' => 'diagnostics_title'],

        ];
    }
}
