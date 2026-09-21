<?php

namespace App\Modules;

interface ModuleInterface
{
    public function getSlug(): string;

    public function getLabel(): string;

    public function getLabelKey(): string;

    public function getIcon(): string;

    public function getPath(): string;

    public function getAllowedRoles(): array;

    public function getSubItems(): array;

    public function isVisibleForRole(string $role): bool;
}
