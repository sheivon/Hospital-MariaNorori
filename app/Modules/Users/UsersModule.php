<?php

namespace App\Modules\Users;

use App\Modules\BaseModule;

class UsersModule extends BaseModule
{
    public function getSlug(): string
    {
        return 'users';
    }

    public function getLabel(): string
    {
        return 'Users';
    }

    public function getLabelKey(): string
    {
        return 'admin_users';
    }

    public function getIcon(): string
    {
        return 'fa-user-shield';
    }

    public function getAllowedRoles(): array
    {
        return ['admin'];
    }

    public function getSubItems(): array
    {
        return [
            ['path' => '/admin/users.php', 'label' => 'Users', 'labelKey' => 'admin_users'],
        ];
    }
}
