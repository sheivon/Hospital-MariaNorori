<?php

namespace App\Services;

use App\Models\EmergencyEncounterModel;

class EmergencyService extends BaseService
{
    public function __construct(EmergencyEncounterModel $repository)
    {
        parent::__construct($repository);
    }
}
