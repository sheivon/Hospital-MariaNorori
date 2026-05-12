<?php

namespace App\Services;

use App\Repositories\TestRepository;

class TestService extends BaseService
{
    public function __construct(TestRepository $repository)
    {
        parent::__construct($repository);
    }
}

