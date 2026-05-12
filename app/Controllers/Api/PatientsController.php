<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Repositories\PatientRepository;
use App\Services\PatientService;
use Exception;

class PatientsController extends BaseApiController
{
    private static function service(): PatientService
    {
        return new PatientService(new PatientRepository());
    }

    public static function index(): void
    {
        Auth::requireLogin();
        $service = self::service();
        $filters = [];

        if (isset($_GET['encountered'])) {
            $filters['encountered'] = (int)$_GET['encountered'];
        }

        if (isset($_GET['emergency_available'])) {
            $filters['emergency_available'] = (int)$_GET['emergency_available'];
        }

        self::success(['data' => $service->getAllPatients($filters)]);
    }

    public static function create(array $payload): void
    {
        Auth::requireLogin();
        try {
            $service = self::service();
            $id = $service->createPatient($payload);
            self::success(['id' => $id]);
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }

    public static function update(array $payload): void
    {
        Auth::requireLogin();
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        try {
            $service = self::service();
            $service->updatePatient($id, $payload);
            self::success();
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }

    public static function show(array $query): void
    {
        Auth::requireLogin();
        $id = (int)($query['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        $patient = self::service()->getPatient($id);
        if ($patient === null) {
            self::fail('Patient not found');
        }

        self::success(['patient' => $patient]);
    }

    public static function delete(array $payload): void
    {
        Auth::requireLogin();
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        try {
            $service = self::service();
            $service->deletePatient($id);
            self::success();
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }
}

