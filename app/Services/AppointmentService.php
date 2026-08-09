<?php

namespace App\Services;

use App\Repositories\AppointmentRepository;

class AppointmentService extends BaseService
{
    public function __construct(AppointmentRepository $repository)
    {
        parent::__construct($repository);
    }
}
