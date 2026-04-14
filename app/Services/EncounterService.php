<?php

namespace App\Services;

use App\Models\EncounterModel;

class EncounterService extends BaseService
{
    public function __construct(EncounterModel $repository)
    {
        parent::__construct($repository);
    }
}
