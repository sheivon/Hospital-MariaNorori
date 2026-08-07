<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Services\AppointmentService;

class AppointmentController extends BaseApiController
{
    private static function service(): AppointmentService
    {
        return new AppointmentService();
    }

    public static function list(): void
    {
        Auth::requireLogin();

        self::success(
            self::service()->list()
        );
    }

    public static function get(array $query): void
    {
        Auth::requireLogin();

        $id = (int)($query['id'] ?? 0);

        if ($id <= 0) {
            self::fail('Invalid appointment id');
        }

        $appointment = self::service()->get($id);

        if (!$appointment) {
            self::fail('Appointment not found');
        }

        self::success([
            'appointment' => $appointment
        ]);
    }

    public static function create(array $post): void
    {
        Auth::requireLogin();

        $id = self::service()->create($post);

        self::success([
            'id' => $id
        ]);
    }

    public static function update(array $post): void
    {
        Auth::requireLogin();

        self::service()->update($post);

        self::success();
    }

    public static function delete(array $post): void
    {
        Auth::requireLogin();

        $id = (int)($post['id'] ?? 0);

        if ($id <= 0) {
            self::fail('Invalid appointment id');
        }

        self::service()->delete($id);

        self::success();
    }
}