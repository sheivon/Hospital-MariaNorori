<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

class RegistrationService
{
    public function __construct(private UserRepository $users)
    {
    }

    public function register(string $username, string $password, string $fullname, string $cedula): ?string
    {
        if ($username === '' || $password === '') {
            return 'reg_user_pass_required';
        }

        if ($this->users->usernameExists($username)) {
            return 'username_taken';
        }

        if ($cedula !== '' && $this->users->cedulaExists($cedula)) {
            return 'cedula_in_use';
        }

        $this->users->create($username, $password, $fullname, $cedula, 'user');
        return null;
    }
}
