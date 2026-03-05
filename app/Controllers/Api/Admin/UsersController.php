<?php

namespace App\Controllers\Api\Admin;

use App\Core\ApiResponse;
use App\Core\Auth;
use App\Models\UserModel;
use Throwable;

class UsersController
{
    public static function index(): void
    {
        Auth::requireRole('admin');
        try {
            $rows = (new UserModel())->listAdminUsers();
            ApiResponse::success(['data' => $rows]);
        } catch (Throwable $e) {
            ApiResponse::fail($e->getMessage());
        }
    }

    public static function create(array $payload): void
    {
        Auth::requireRole('admin');

        $username = trim((string)($payload['username'] ?? ''));
        $password = (string)($payload['password'] ?? '');
        $fullname = trim((string)($payload['fullname'] ?? ''));
        $cedula = trim((string)($payload['cedula'] ?? ''));
        $role = strtolower(trim((string)($payload['role'] ?? 'user')));
        if ($username === '' || $password === '') {
            ApiResponse::fail('Username and password required');
        }
        if (!in_array($role, ['user', 'admin'], true)) {
            $role = 'user';
        }

        try {
            $userModel = new UserModel();
            if ($userModel->usernameExists($username)) {
                ApiResponse::fail('Username already taken');
            }
            if ($cedula !== '' && $userModel->cedulaExists($cedula)) {
                ApiResponse::fail('Cédula already in use');
            }

            $id = $userModel->create($username, $password, $fullname, $cedula, $role);
            ApiResponse::success(['id' => $id]);
        } catch (Throwable $e) {
            ApiResponse::fail($e->getMessage());
        }
    }

    public static function update(array $payload): void
    {
        Auth::requireRole('admin');

        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            ApiResponse::fail('Missing id');
        }

        $username = trim((string)($payload['username'] ?? ''));
        $password = (string)($payload['password'] ?? '');
        $fullname = trim((string)($payload['fullname'] ?? ''));
        $cedula = trim((string)($payload['cedula'] ?? ''));
        $role = strtolower(trim((string)($payload['role'] ?? 'user')));
        if (!in_array($role, ['user', 'admin'], true)) {
            $role = 'user';
        }

        $me = Auth::currentUser();
        if ($me && (int)$me['id'] === $id && $role !== 'admin') {
            ApiResponse::fail('Cannot remove your own admin role');
        }

        try {
            $userModel = new UserModel();
            if (!$userModel->findById($id)) {
                ApiResponse::fail('User not found');
            }

            if ($username !== '' && $userModel->usernameExists($username, $id)) {
                ApiResponse::fail('Username already taken');
            }
            if ($cedula !== '' && $userModel->cedulaExists($cedula, $id)) {
                ApiResponse::fail('Cédula already in use');
            }

            $fields = ['fullname' => $fullname, 'cedula' => $cedula, 'role' => $role];
            if ($username !== '') {
                $fields['username'] = $username;
            }

            $userModel->update($id, $fields, $password);
            ApiResponse::success();
        } catch (Throwable $e) {
            ApiResponse::fail($e->getMessage());
        }
    }

    public static function delete(array $payload): void
    {
        Auth::requireRole('admin');

        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            ApiResponse::fail('Missing id');
        }

        $me = Auth::currentUser();
        if ($me && (int)$me['id'] === $id) {
            ApiResponse::fail('Cannot delete your own account');
        }

        try {
            $userModel = new UserModel();
            $target = $userModel->findById($id);
            if (!$target) {
                ApiResponse::fail('User not found');
            }

            if (strtolower((string)($target['role'] ?? 'user')) === 'admin' && $userModel->countAdmins() <= 1) {
                ApiResponse::fail('Cannot delete the last admin');
            }

            $userModel->delete($id);
            ApiResponse::success();
        } catch (Throwable $e) {
            ApiResponse::fail($e->getMessage());
        }
    }
}
