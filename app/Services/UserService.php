<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listUsersForChat(int $currentUserId): array
    {
        return $this->repository->listPublicExcept($currentUserId);
    }

    public function existsById(int $id): bool
    {
        return $this->repository->existsById($id);
    }
}

