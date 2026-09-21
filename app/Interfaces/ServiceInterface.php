<?php

namespace App\Interfaces;

interface ServiceInterface
{
    public function all(array $filters = []): array;
    public function find(int $id): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
