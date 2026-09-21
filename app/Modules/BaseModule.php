<?php

namespace App\Modules;

abstract class BaseModule implements ModuleInterface
{
    public function getIcon(): string
    {
        return 'fa-layer-group';
    }

    public function getPath(): string
    {
        $items = $this->getSubItems();
        return $items[0]['path'] ?? '/';
    }

    public function getAllowedRoles(): array
    {
        return ['admin', 'doctor', 'user'];
    }

    public function isVisibleForRole(string $role): bool
    {
        $role = strtolower(trim($role));
        return in_array($role, array_map('strtolower', $this->getAllowedRoles()), true);
    }

    public function getSubItems(): array
    {
        return [];
    }

    public function hasSubItems(): bool
    {
        return count($this->getSubItems()) > 0;
    }
}
