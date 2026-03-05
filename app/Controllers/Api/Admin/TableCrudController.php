<?php

namespace App\Controllers\Api\Admin;

use App\Core\ApiResponse;
use App\Core\Auth;
use App\Models\TableCrudModel;
use Throwable;

class TableCrudController
{
    public static function meta(): void
    {
        Auth::requireRole('admin');
        try {
            $model = new TableCrudModel();
            ApiResponse::success(['tables' => $model->listTables()]);
        } catch (Throwable $e) {
            ApiResponse::fail($e->getMessage());
        }
    }

    public static function rows(array $query): void
    {
        Auth::requireRole('admin');
        $table = (string)($query['table'] ?? '');
        $limit = (int)($query['limit'] ?? 200);

        try {
            $model = new TableCrudModel();
            $columns = $model->describe($table);
            $rows = $model->listRows($table, $limit);
            ApiResponse::success(['columns' => $columns, 'rows' => $rows]);
        } catch (Throwable $e) {
            ApiResponse::fail($e->getMessage());
        }
    }

    public static function create(array $payload): void
    {
        Auth::requireRole('admin');
        $table = (string)($payload['table'] ?? '');
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        try {
            $model = new TableCrudModel();
            $id = $model->createRow($table, $data);
            ApiResponse::success(['id' => $id]);
        } catch (Throwable $e) {
            ApiResponse::fail($e->getMessage());
        }
    }

    public static function update(array $payload): void
    {
        Auth::requireRole('admin');
        $table = (string)($payload['table'] ?? '');
        $id = (int)($payload['id'] ?? 0);
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        if ($id <= 0) {
            ApiResponse::fail('Missing id');
        }

        try {
            $model = new TableCrudModel();
            $model->updateRow($table, $id, $data);
            ApiResponse::success();
        } catch (Throwable $e) {
            ApiResponse::fail($e->getMessage());
        }
    }

    public static function delete(array $payload): void
    {
        Auth::requireRole('admin');
        $table = (string)($payload['table'] ?? '');
        $id = (int)($payload['id'] ?? 0);

        if ($id <= 0) {
            ApiResponse::fail('Missing id');
        }

        try {
            $model = new TableCrudModel();
            $model->softDelete($table, $id);
            ApiResponse::success();
        } catch (Throwable $e) {
            ApiResponse::fail($e->getMessage());
        }
    }
}
