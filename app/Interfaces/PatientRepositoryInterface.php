<?php

namespace App\Interfaces;

interface PatientRepositoryInterface extends RepositoryInterface
{
    public function findByCedula(string $cedula, ?int $exceptId = null): ?array;
}
