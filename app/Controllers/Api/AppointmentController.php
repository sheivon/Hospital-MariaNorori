<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Services\AppointmentService;
use App\Repositories\AppointmentRepository;
use Exception;

class AppointmentController extends BaseApiController
{
    private static function service(): AppointmentService
    {
        return new AppointmentService(new AppointmentRepository());
    }

    public static function list(array $query = []): void
    {
        try {
            Auth::requireLogin();
            self::success([
                'data' => self::service()->all($query),
            ]);
        } catch (Exception $e) {
            self::fail($e->getMessage(), 500);
        }
    }

    public static function get(array $query): void
    {
        try {
            Auth::requireLogin();

            $id = (int)($query['id'] ?? 0);
            if ($id <= 0) {
                self::fail('Invalid appointment id');
                return;
            }

            $appointment = self::service()->find($id);
            if (!$appointment) {
                self::fail('Appointment not found', 404);
                return;
            }

            self::success([
                'appointment' => $appointment,
            ]);
        } catch (Exception $e) {
            self::fail($e->getMessage(), 500);
        }
    }

    public static function create(array $post): void
    {
        try {
            Auth::requireLogin();

            $user = Auth::currentUser();
            if (!isset($post['created_by']) && !empty($user['id'])) {
                $post['created_by'] = (int)$user['id'];
            }

            $id = self::service()->create($post);

            self::success([
                'id' => $id,
            ]);
        } catch (Exception $e) {
            self::fail($e->getMessage(), 500);
        }
    }

    public static function update(array $post): void
    {
        try {
            Auth::requireLogin();

            $id = (int)($post['id'] ?? 0);
            if ($id <= 0) {
                self::fail('Missing id');
                return;
            }
            unset($post['id']);

            self::service()->update($id, $post);

            self::success();
        } catch (Exception $e) {
            self::fail($e->getMessage(), 500);
        }
    }

    public static function delete(array $post): void
    {
        try {
            Auth::requireLogin();

            $id = (int)($post['id'] ?? 0);
            if ($id <= 0) {
                self::fail('Invalid appointment id');
                return;
            }

            self::service()->delete($id);

            self::success();
        } catch (Exception $e) {
            self::fail($e->getMessage(), 500);
        }
    }
}
