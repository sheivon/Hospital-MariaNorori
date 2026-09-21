<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

class ProfileService
{
    public function update(int $userId, array $data): string
    {
        $fullname = trim((string)($data['fullname'] ?? ''));
        $cedula = trim((string)($data['cedula'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if ($fullname === '') {
            return 'label_fullname';
        }

        $users = new UserRepository();
        if ($cedula !== '' && $users->cedulaExists($cedula, $userId)) {
            return 'cedula_in_use';
        }

        $users->update($userId, [
            'fullname' => $fullname,
            'cedula' => $cedula,
        ], $password !== '' ? $password : null);

        return '';
    }
}