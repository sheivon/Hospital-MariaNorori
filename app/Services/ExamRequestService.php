<?php

namespace App\Services;

use App\Models\ExamRequestModel;

class ExamRequestService extends BaseService
{
    public function __construct(ExamRequestModel $repository)
    {
        parent::__construct($repository);
    }
}
