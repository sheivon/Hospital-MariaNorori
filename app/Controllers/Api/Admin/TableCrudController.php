<?php

declare(strict_types=1);

namespace App\Controllers\Api\Admin;

use App\Core\Auth;
use App\Controllers\Api\BaseApiController;
use App\Services\Admin\TableCrudService;
use App\Repositories\TableCrudRepository;
use Throwable;

class TableCrudController extends BaseApiController
{
    private static function service(): TableCrudService
    {
        return new TableCrudService(new TableCrudRepository());
    }

    public static function meta(): void
    {
        Auth::requireRole('admin');

        try {
            self::success(['tables' => self::service()->listTables()]);
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function rows(array $query): void
    {
        Auth::requireRole('admin');
        $table = trim((string)($query['table'] ?? ''));
        $limit = max(1, min(500, (int)($query['limit'] ?? 200)));

        try {
            $columns = self::service()->describe($table);
            $rows = self::service()->listRows($table, $limit);
            self::success(['columns' => $columns, 'rows' => $rows]);
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function create(array $payload): void
    {
        Auth::requireRole('admin');
        $table = trim((string)($payload['table'] ?? ''));
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        try {
            $id = self::service()->createRow($table, $data);
            self::success(['id' => $id]);
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function update(array $payload): void
    {
        Auth::requireRole('admin');
        $table = trim((string)($payload['table'] ?? ''));
        $id = (int)($payload['id'] ?? 0);
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        if ($id <= 0) {
            self::fail('Missing id');
        }

        try {
            self::service()->updateRow($table, $id, $data);
            self::success();
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function delete(array $payload): void
    {
        Auth::requireRole('admin');
        $table = trim((string)($payload['table'] ?? ''));
        $id = (int)($payload['id'] ?? 0);

        if ($id <= 0) {
            self::fail('Missing id');
        }

        try {
            self::service()->softDelete($table, $id);
            self::success();
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }
}

