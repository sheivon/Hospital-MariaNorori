<?php

namespace App\Services;

use App\Interfaces\RepositoryInterface;
use App\Interfaces\ServiceInterface;

abstract class BaseService implements ServiceInterface
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(array $filters = []): array
    {
        return $this->repository->all($filters);
    }

    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function create(array $data): int
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $this->ensureValidId($id);
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $this->ensureValidId($id);
        return $this->repository->delete($id);
    }

    protected function ensureValidId(int $id, string $name = 'id'): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException(sprintf('Invalid %s', $name));
        }
    }
}
