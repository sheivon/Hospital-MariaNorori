<?php

namespace App\Core;

use App\Modules\Encounters\EncountersModule;
use App\Modules\Patients\PatientsModule;
use App\Modules\Reports\ReportsModule;
use App\Modules\Users\UsersModule;
use App\Modules\ModuleInterface;

class ModuleRegistry
{
    /**
     * @return ModuleInterface[]
     */
    public static function all(): array
    {
        return [
            new EncountersModule(),
            new PatientsModule(),
            new ReportsModule(),
            new UsersModule(),
        ];
    }

    /**
     * @param string $role
     * @return ModuleInterface[]
     */
    public static function visibleForRole(string $role): array
    {
        return array_values(array_filter(self::all(), static fn (ModuleInterface $module): bool => $module->isVisibleForRole($role)));
    }
}
