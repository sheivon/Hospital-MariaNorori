<?php

namespace App\Modules\Encounters;

use App\Modules\BaseModule;

class EncountersModule extends BaseModule
{
    public function getSlug(): string
    {
        return 'encounters';
    }

    public function getLabel(): string
    {
        return 'Encounters';
    }

    public function getLabelKey(): string
    {
        return 'encounters';
    }

    public function getIcon(): string
    {
        return 'fa-stethoscope';
    }

    public function getPath(): string
    {
        return '/encounters.php';
    }

    public function getAllowedRoles(): array
    {
        return ['admin', 'doctor', 'user'];
    }

    public function getSubItems(): array
    {
        return [
            ['path' => '/encounters.php',  'label' => 'Encounters',  'labelKey' => 'encounters'],
            ['path' => '/emergency.php',    'label' => 'Emergency',   'labelKey' => 'emergency'],
        ];
    }
}
