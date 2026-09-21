<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Repositories\TableCrudRepository;
use Throwable;

class MedicationsCatalogController extends BaseApiController
{
    private const TABLE = 'medications_catalog';
    private const WRITER_ROLES = ['admin', 'doctor'];

    private static function requireWriter(): void
    {
        Auth::requireLogin();
        $role = strtolower((string)(Auth::currentUser()['role'] ?? ''));
        if (!in_array($role, self::WRITER_ROLES, true)) {
            self::fail('Forbidden', 403);
        }
    }

    public static function index(array $query): void
    {
        Auth::requireLogin();
        try {
            $repo = new TableCrudRepository();
            self::success([
                'columns' => $repo->describe(self::TABLE),
                'rows' => $repo->listRows(self::TABLE, 500),
            ]);
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }

    public static function create(array $payload): void
    {
        self::requireWriter();

        $name = trim((string)($payload['medication_name'] ?? ''));
        if ($name === '') {
            self::fail('medication_name is required');
        }

        $data = [
            'medication_name' => $name,
            'generic_name' => $payload['generic_name'] ?? null,
            'form' => $payload['form'] ?? null,
            'strength' => $payload['strength'] ?? null,
        ];

        try {
            $repo = new TableCrudRepository();
            $id = $repo->createRow(self::TABLE, $data);
            self::success(['id' => $id]);
        } catch (Throwable $e) {
            // MySQL duplicate-key (1062) → user-facing unique violation
            if (strpos($e->getMessage(), '1062') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
                self::fail('A medication with this name already exists', 409);
                return;
            }
            self::fail($e->getMessage());
        }
    }

    public static function update(array $payload): void
    {
        self::requireWriter();

        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        $name = trim((string)($payload['medication_name'] ?? ''));
        if ($name === '') {
            self::fail('medication_name is required');
        }

        $data = [
            'medication_name' => $name,
            'generic_name' => $payload['generic_name'] ?? null,
            'form' => $payload['form'] ?? null,
            'strength' => $payload['strength'] ?? null,
        ];

        try {
            $repo = new TableCrudRepository();
            $repo->updateRow(self::TABLE, $id, $data);
            self::success();
        } catch (Throwable $e) {
            if (strpos($e->getMessage(), '1062') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
                self::fail('A medication with this name already exists', 409);
                return;
            }
            self::fail($e->getMessage());
        }
    }

    public static function delete(array $payload): void
    {
        self::requireWriter();

        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        try {
            $repo = new TableCrudRepository();
            $repo->softDelete(self::TABLE, $id);
            self::success();
        } catch (Throwable $e) {
            self::fail($e->getMessage());
        }
    }
}
