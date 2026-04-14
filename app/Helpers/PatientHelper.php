<?php

namespace App\Helpers;

use App\Models\PatientModel;

class PatientHelper
{
    private static ?PatientModel $model = null;

    private static function model(): PatientModel
    {
        if (self::$model === null) {
            self::$model = new PatientModel();
        }
        return self::$model;
    }

    public static function getPatients(): array
    {
        return self::model()->all();
    }

    public static function getPatient(int $id): ?array
    {
        return self::model()->find($id);
    }

    public static function createPatient(array $data): int
    {
        return self::model()->create($data);
    }

    public static function updatePatient(int $id, array $data): bool
    {
        return self::model()->update($id, $data);
    }

    public static function deletePatient(int $id): bool
    {
        return self::model()->delete($id);
    }
}
