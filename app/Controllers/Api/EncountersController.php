<?php

namespace App\Controllers\Api;

use App\Core\ApiResponse;
use App\Models\EncounterModel;
use Exception;

class EncountersController
{
    public static function index(): void
    {
        $model = new EncounterModel();
        ApiResponse::success(['data' => $model->all()]);
    }

    public static function create(array $payload): void
    {
        try {
            $model = new EncounterModel();
            $id = $model->create($payload);
            ApiResponse::success(['id' => $id]);
        } catch (Exception $e) {
            ApiResponse::fail($e->getMessage());
        }
    }

    public static function update(array $payload): void
    {
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            ApiResponse::fail('Missing id');
        }

        try {
            $model = new EncounterModel();
            $model->update($id, $payload);
            ApiResponse::success();
        } catch (Exception $e) {
            ApiResponse::fail($e->getMessage());
        }
    }

    public static function delete(array $payload): void
    {
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            ApiResponse::fail('Missing id');
        }

        try {
            $model = new EncounterModel();
            $model->delete($id);
            ApiResponse::success();
        } catch (Exception $e) {
            ApiResponse::fail($e->getMessage());
        }
    }
}
