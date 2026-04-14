<?php

namespace App\Services;

use App\Models\RadiologyRequestModel;

class RadiologyService extends BaseService
{
    public function __construct(RadiologyRequestModel $repository)
    {
        parent::__construct($repository);
    }
}
