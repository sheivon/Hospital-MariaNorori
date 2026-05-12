<?php

namespace App\Services;

use App\Repositories\EncounterRepository;

class EncounterService extends BaseService
{
    public function __construct(EncounterRepository $repository)
    {
        parent::__construct($repository);
    }
}

