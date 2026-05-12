<?php

namespace App\Controllers\Api;

use App\Services\DiagnosticService;
use App\Repositories\DiagnosticoRepository;
use Exception;

class DiagnosticsController extends BaseApiController
{
    private static function service(): DiagnosticService
    {
        return new DiagnosticService(new DiagnosticoRepository());
    }

    public static function index(array $query = []): void
    {
        self::success(['diagnostics' => self::service()->all($query)]);
    }

    public static function show(array $query): void
    {
        $id = (int)($query['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        $diagnostic = self::service()->find($id);
        if ($diagnostic === null) {
            self::fail('Diagnostic not found');
        }

        self::success(['diagnostic' => $diagnostic]);
    }

    public static function create(array $payload): void
    {
        try {
            $id = self::service()->create($payload);
            self::success(['id' => $id]);
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }

    public static function update(array $payload): void
    {
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        try {
            self::service()->update($id, $payload);
            self::success();
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }
}

