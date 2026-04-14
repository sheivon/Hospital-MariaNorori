<?php

namespace App\Services;

use App\Models\UserModel;

class UserService
{
    private UserModel $repository;

    public function __construct(UserModel $repository)
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
