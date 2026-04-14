<?php

namespace App\Services;

use App\Models\TestModel;

class TestService extends BaseService
{
    public function __construct(TestModel $repository)
    {
        parent::__construct($repository);
    }
}
