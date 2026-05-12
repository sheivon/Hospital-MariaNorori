<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Repositories\TableCrudRepository;

class TableCrudService
{
    private TableCrudRepository $repository;

    public function __construct(TableCrudRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listTables(): array
    {
        return $this->repository->listTables();
    }

    public function describe(string $table): array
    {
        return $this->repository->describe($table);
    }

    public function listRows(string $table, int $limit = 200): array
    {
        return $this->repository->listRows($table, $limit);
    }

    public function createRow(string $table, array $data): int
    {
        return $this->repository->createRow($table, $data);
    }

    public function updateRow(string $table, int $id, array $data): void
    {
        $this->repository->updateRow($table, $id, $data);
    }

    public function softDelete(string $table, int $id): void
    {
        $this->repository->softDelete($table, $id);
    }
}

