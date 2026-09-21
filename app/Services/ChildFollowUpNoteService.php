<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

class ChildFollowUpNoteService extends BaseService
{
    public function createNote(array $data): int
    {
        $seguimientoId = isset($data['seguimiento_id']) ? (int)$data['seguimiento_id'] : 0;
        if ($seguimientoId <= 0) {
            throw new Exception('Follow-up record is required');
        }

        $contenido = trim((string)($data['contenido'] ?? ''));
        if ($contenido === '') {
            throw new Exception('Note content is required');
        }

        return $this->repository->create($data);
    }
}
