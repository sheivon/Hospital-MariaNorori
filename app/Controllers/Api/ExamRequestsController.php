<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Repositories\ExamRequestRepository;
use App\Repositories\ExamTypeRepository;
use App\Services\ExamRequestService;
use Exception;

class ExamRequestsController extends BaseApiController
{
    private static function service(): ExamRequestService
    {
        return new ExamRequestService(new ExamRequestRepository(), new ExamTypeRepository());
    }

    public static function index(array $query = []): void
    {
        Auth::requireLogin();
        $filters = [];

        if (!empty($query['patient_id'])) {
            $filters['patient_id'] = (int)$query['patient_id'];
        }

        if (!empty($query['status'])) {
            $filters['status'] = (string)$query['status'];
        }

        self::success(['data' => self::service()->all($filters)]);
    }

    public static function types(): void
    {
        Auth::requireLogin();
        self::success(['data' => (new ExamTypeRepository())->all(['active_only' => true])]);
    }

    public static function show(array $query): void
    {
        Auth::requireLogin();
        $id = (int)($query['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        $request = self::service()->find($id);
        if ($request === null) {
            self::fail('Exam request not found');
        }

        self::success(['exam_request' => $request]);
    }

    public static function create(array $payload): void
    {
        Auth::requireLogin();
        try {
            $user = Auth::currentUser();
            $payload['created_by'] = $payload['created_by'] ?? (is_array($user) ? ($user['id'] ?? null) : null);
            $id = self::service()->create($payload);
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
            unset($payload['id']);
            self::service()->update($id, $payload);
            self::success();
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }

    public static function delete(array $payload): void
    {
        Auth::requireLogin();
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            self::fail('Missing id');
        }

        try {
            self::service()->delete($id);
            self::success();
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }
}
