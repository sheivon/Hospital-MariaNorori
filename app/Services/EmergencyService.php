<?php

namespace App\Services;

use App\Repositories\EmergencyEncounterRepository;

class EmergencyService extends BaseService
{
    public function __construct(EmergencyEncounterRepository $repository)
    {
        parent::__construct($repository);
    }
}

