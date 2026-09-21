<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\ApiResponse;
use App\Core\Auth;
use App\Repositories\PatientAllergyRepository;
use App\Services\PatientAllergyService;

class PatientAllergiesController
{
    private PatientAllergyService $service;

    public function __construct(PatientAllergyService $service)
    {
        $this->service = $service;
    }

    public static function index(array $params): void
    {
        Auth::requireLogin();
        try {
            $patientId = isset($params['patient_id']) ? (int)$params['patient_id'] : null;
            $service = new PatientAllergyService(new PatientAllergyRepository());
            $rows = $service->list($patientId ?: null);
            ApiResponse::success(['data' => $rows]);
        } catch (\Exception $e) {
            ApiResponse::fail('Error loading allergies: ' . $e->getMessage(), 500);
        }
    }

    public static function save(array $post): void
    {
        Auth::requireLogin();
        try {
            $service = new PatientAllergyService(new PatientAllergyRepository());
            $id = isset($post['id']) ? (int)$post['id'] : 0;
            if (empty($post['patient_id']) || trim((string)($post['allergen'] ?? '')) === '') {
                ApiResponse::fail('Invalid data. Patient ID and allergen are required.');
                return;
            }
            if ($id > 0) {
                $service->update($id, $post);
                ApiResponse::success();
            } else {
                $id = $service->create($post);
                ApiResponse::success(['id' => $id], 201);
            }
        } catch (\Exception $e) {
            ApiResponse::fail('Failed to save allergy: ' . $e->getMessage(), 500);
        }
    }

    public function list(): void
    {
        try {
            $patientId = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : null;
            $rows = $this->service->list($patientId ?: null);
            ApiResponse::success(['rows' => $rows]);
        } catch (\Exception $e) {
            ApiResponse::fail('Error loading allergies: ' . $e->getMessage(), 500);
        }
    }

    public static function get(array $params): void
    {
        Auth::requireLogin();
        try {
            $service = new PatientAllergyService(new PatientAllergyRepository());
            $id = isset($params['id']) ? (int)$params['id'] : 0;
            $row = $service->get($id);
            if (!$row) {
                ApiResponse::fail('Allergy not found', 404);
                return;
            }
            ApiResponse::success(['data' => $row]);
        } catch (\Exception $e) {
            ApiResponse::fail('Error fetching allergy: ' . $e->getMessage(), 500);
        }
    }

    public static function delete(array $post): void
    {
        Auth::requireLogin();
        try {
            $service = new PatientAllergyService(new PatientAllergyRepository());
            $id = isset($post['id']) ? (int)$post['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
            if (!$id) {
                ApiResponse::fail('Invalid data. ID is required.');
                return;
            }
            $service->delete($id);
            ApiResponse::success();
        } catch (\Exception $e) {
            ApiResponse::fail('Failed to delete allergy: ' . $e->getMessage(), 500);
        }
    }

    public function create(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: [];
            if (empty($data['patient_id']) || trim((string)($data['allergen'] ?? '')) === '') {
                ApiResponse::fail('Invalid data. Patient ID and allergen are required.');
                return;
            }
            $id = $this->service->create($data);
            ApiResponse::success(['id' => $id], 201);
        } catch (\Exception $e) {
            ApiResponse::fail('Failed to create allergy: ' . $e->getMessage(), 500);
        }
    }

    public function update(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: [];
            if (empty($data['id'])) {
                ApiResponse::fail('Invalid data. ID is required.');
                return;
            }
            $id = (int)$data['id'];
            unset($data['id']);
            $this->service->update($id, $data);
            ApiResponse::success();
        } catch (\Exception $e) {
            ApiResponse::fail('Failed to update allergy: ' . $e->getMessage(), 500);
        }
    }
}