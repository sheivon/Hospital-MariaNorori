<?php

namespace App\Modules\Pharmacy;

use App\Modules\BaseModule;

class PharmacyModule extends BaseModule
{
    public function getSlug(): string
    {
        return 'pharmacy';
    }

    public function getLabel(): string
    {
        return 'Pharmacy';
    }

    public function getLabelKey(): string
    {
        return 'pharmacy';
    }

    public function getIcon(): string
    {
        return 'fa-prescription-bottle-medical';
    }

    public function getAllowedRoles(): array
    {
        return ['admin', 'doctor', 'user'];
    }

    public function getSubItems(): array
    {
        return [
            ['path' => '/medications.php', 'label' => 'Medications Catalog', 'labelKey' => 'medications_catalog'],
            ['path' => '/treatments.php', 'label' => 'Treatments Catalog', 'labelKey' => 'Treatments_catalog'],
        ];
    }
}
