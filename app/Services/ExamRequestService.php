<?php

namespace App\Services;

use App\Repositories\ExamRequestRepository;

class ExamRequestService extends BaseService
{
    public function __construct(ExamRequestRepository $repository)
    {
        parent::__construct($repository);
    }
}

