<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\Api\ChildFollowUpNotesController;

ChildFollowUpNotesController::index($_GET);

