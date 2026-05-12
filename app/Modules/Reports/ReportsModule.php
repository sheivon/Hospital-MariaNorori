<?php

namespace App\Modules\Reports;

use App\Modules\BaseModule;

class ReportsModule extends BaseModule
{
    public function getSlug(): string
    {
        return 'reports';
    }

    public function getLabel(): string
    {
        return 'Reports';
    }

    public function getLabelKey(): string
    {
        return 'reports_title';
    }

    public function getIcon(): string
    {
        return 'fa-file-lines';
    }

    public function getAllowedRoles(): array
    {
        return ['admin', 'doctor', 'user'];
    }

    public function getSubItems(): array
    {
        return [
            ['path' => '/reports.php', 'label' => 'Reports', 'labelKey' => 'reports_title'],
        ];
    }
}
