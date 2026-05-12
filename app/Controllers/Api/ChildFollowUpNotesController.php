<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Repositories\ChildFollowUpNoteRepository;
use App\Services\ChildFollowUpNoteService;

class ChildFollowUpNotesController extends BaseApiController
{
    private static function service(): ChildFollowUpNoteService
    {
        return new ChildFollowUpNoteService(new ChildFollowUpNoteRepository());
    }

    public static function index(array $query = []): void
    {
        Auth::requireLogin();
        self::success(['data' => self::service()->all($query)]);
    }

    public static function create(array $payload): void
    {
        Auth::requireLogin();

        try {
            $id = self::service()->createNote($payload);
            self::success(['id' => $id]);
        } catch (\Throwable $e) {
            self::fail($e->getMessage());
        }
    }
}
