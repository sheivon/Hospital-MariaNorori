<?php

namespace App\Services;

use App\Models\DiagnosticoModel;

class DiagnosticService extends BaseService
{
    public function __construct(DiagnosticoModel $repository)
    {
        parent::__construct($repository);
    }
}
