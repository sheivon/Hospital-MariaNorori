<?php

namespace App\Services;

use App\Repositories\DiagnosticoRepository;

class DiagnosticService extends BaseService
{
    public function __construct(DiagnosticoRepository $repository)
    {
        parent::__construct($repository);
    }
}

