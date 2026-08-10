<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Repositories\TableCrudRepository;
use Throwable;

class VitalsController extends BaseApiController
{
    private const TABLE = 'vitals';
    private const WRITER_ROLES = ['admin', 'doctor', 'nurse'];

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

        $patientId = (int)($payload['patient_id'] ?? 0);
        if ($patientId <= 0) {
            self::fail('patient_id is required');
        }

        $data = [
            'patient_id' => $patientId,
            'encounter_id' => self::nullableInt($payload['encounter_id'] ?? null),
            'measured_at' => self::normalizeDateTime($payload['measured_at'] ?? null) ?? date('Y-m-d H:i:s'),
            'temperature_c' => self::nullableFloat($payload['temperature_c'] ?? null),
            'systolic_bp' => self::nullableInt($payload['systolic_bp'] ?? null),
            'diastolic_bp' => self::nullableInt($payload['diastolic_bp'] ?? null),
            'heart_rate' => self::nullableInt($payload['heart_rate'] ?? null),
            'respiratory_rate' => self::nullableInt($payload['respiratory_rate'] ?? null),
            'oxygen_saturation' => self::nullableFloat($payload['oxygen_saturation'] ?? null),
            'weight_kg' => self::nullableFloat($payload['weight_kg'] ?? null),
            'height_cm' => self::nullableFloat($payload['height_cm'] ?? null),
            'bmi' => self::nullableFloat($payload['bmi'] ?? null),
            'notes' => self::nullableString($payload['notes'] ?? null),
        ];

        // Stamp creator from session if available.
        $user = Auth::currentUser();
        if (!empty($user['id'])) {
            $data['created_by'] = (int)$user['id'];
        }

        try {
            $repo = new TableCrudRepository();
            $id = $repo->createRow(self::TABLE, $data);
            self::success(['id' => $id]);
        } catch (Throwable $e) {
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

        $patientId = (int)($payload['patient_id'] ?? 0);
        if ($patientId <= 0) {
            self::fail('patient_id is required');
        }

        $data = [
            'patient_id' => $patientId,
            'encounter_id' => self::nullableInt($payload['encounter_id'] ?? null),
            'measured_at' => self::normalizeDateTime($payload['measured_at'] ?? null) ?? date('Y-m-d H:i:s'),
            'temperature_c' => self::nullableFloat($payload['temperature_c'] ?? null),
            'systolic_bp' => self::nullableInt($payload['systolic_bp'] ?? null),
            'diastolic_bp' => self::nullableInt($payload['diastolic_bp'] ?? null),
            'heart_rate' => self::nullableInt($payload['heart_rate'] ?? null),
            'respiratory_rate' => self::nullableInt($payload['respiratory_rate'] ?? null),
            'oxygen_saturation' => self::nullableFloat($payload['oxygen_saturation'] ?? null),
            'weight_kg' => self::nullableFloat($payload['weight_kg'] ?? null),
            'height_cm' => self::nullableFloat($payload['height_cm'] ?? null),
            'bmi' => self::nullableFloat($payload['bmi'] ?? null),
            'notes' => self::nullableString($payload['notes'] ?? null),
        ];

        try {
            $repo = new TableCrudRepository();
            $repo->updateRow(self::TABLE, $id, $data);
            self::success();
        } catch (Throwable $e) {
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

    /** Convert empty / 'null' / non-numeric to NULL; otherwise return (int). */
    private static function nullableInt($value): ?int
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }
        if (is_numeric($value)) {
            return (int)$value;
        }
        return null;
    }

    /** Convert empty / 'null' / non-numeric to NULL; otherwise return (float). */
    private static function nullableFloat($value): ?float
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }
        if (is_numeric($value)) {
            return (float)$value;
        }
        return null;
    }

    private static function nullableString($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim((string)$value);
        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * Accept either "Y-m-d H:i:s", "Y-m-d\TH:i", or "Y-m-d H:i" and return
     * "Y-m-d H:i:s", or NULL when input is empty.
     */
    private static function normalizeDateTime($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim((string)$value);
        if ($trimmed === '') {
            return null;
        }
        $trimmed = str_replace('T', ' ', $trimmed);
        $ts = strtotime($trimmed);
        if ($ts === false) {
            return null;
        }
        return date('Y-m-d H:i:s', $ts);
    }
}
