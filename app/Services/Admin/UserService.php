<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use Exception;

class UserService
{
    private UserRepository $repository;
    private UserRoleRepository $roleRepository;

    public function __construct(UserRepository $repository, UserRoleRepository $roleRepository)
    {
        $this->repository = $repository;
        $this->roleRepository = $roleRepository;
    }

    public function getRoles(): array
    {
        return $this->roleRepository->all();
    }

    public function listUsers(): array
    {
        return $this->repository->listAdminUsers();
    }

    public function createUser(array $payload): int
    {
        $username = trim((string) ($payload['username'] ?? ''));
        $password = (string) ($payload['password'] ?? '');
        $fullname = trim((string) ($payload['fullname'] ?? ''));
        $cedula = trim((string) ($payload['cedula'] ?? ''));
        $role = strtolower(trim((string) ($payload['role'] ?? 'user')));
        $specialty = trim((string) ($payload['specialty'] ?? ''));
        $department = trim((string) ($payload['department'] ?? ''));

        if ($username === '' || $password === '') {
            throw new Exception('Username and password required');
        }
        if (!$this->roleRepository->exists($role)) {
            throw new Exception('Invalid role');
        }
        if ($this->repository->usernameExists($username)) {
            throw new Exception('Username already taken');
        }
        if ($cedula !== '' && $this->repository->cedulaExists($cedula)) {
            throw new Exception('Cédula already in use');
        }

        return $this->repository->create($username, $password, $fullname, $cedula, $role, $specialty, $department);
    }

    public function updateUser(int $id, array $payload, int $currentUserId): void
    {
        if ($id <= 0) {
            throw new Exception('Missing id');
        }

        $username = trim((string) ($payload['username'] ?? ''));
        $password = (string) ($payload['password'] ?? '');
        $fullname = trim((string) ($payload['fullname'] ?? ''));
        $cedula = trim((string) ($payload['cedula'] ?? ''));
        $role = strtolower(trim((string) ($payload['role'] ?? 'user')));
        $specialty = trim((string) ($payload['specialty'] ?? ''));
        $department = trim((string) ($payload['department'] ?? ''));

        if (!$this->roleRepository->exists($role)) {
            throw new Exception('Invalid role');
        }

        if ($currentUserId === $id && $role !== 'admin') {
            throw new Exception('Cannot remove your own admin role');
        }

        $existing = $this->repository->findById($id);
        if ($existing === null) {
            throw new Exception('User not found');
        }

        if ($username !== '' && $this->repository->usernameExists($username, $id)) {
            throw new Exception('Username already taken');
        }
        if ($cedula !== '' && $this->repository->cedulaExists($cedula, $id)) {
            throw new Exception('Cédula already in use');
        }

        $fields = [
            'fullname' => $fullname,
            'cedula' => $cedula,
            'role' => $role,
            'specialty' => $specialty,
            'department' => $department,
        ];

        if ($username !== '') {
            $fields['username'] = $username;
        }

        $this->repository->update($id, $fields, $password !== '' ? $password : null);
    }

    public function deleteUser(int $id, int $currentUserId): void
    {
        if ($id <= 0) {
            throw new Exception('Missing id');
        }
        if ($currentUserId === $id) {
            throw new Exception('Cannot delete your own account');
        }

        $target = $this->repository->findById($id);
        if ($target === null) {
            throw new Exception('User not found');
        }

        if (strtolower((string) ($target['role'] ?? 'user')) === 'admin' && $this->repository->countAdmins() <= 1) {
            throw new Exception('Cannot delete the last admin');
        }

        $this->repository->delete($id);
    }
}

