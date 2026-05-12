<?php

namespace App\Services;

use App\Repositories\RadiologyRequestRepository;

class RadiologyService extends BaseService
{
    public function __construct(RadiologyRequestRepository $repository)
    {
        parent::__construct($repository);
    }
}

