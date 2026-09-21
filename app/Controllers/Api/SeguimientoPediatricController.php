<?php

namespace App\Controllers\Api;

use App\Core\ApiResponse;
use App\Repositories\SeguimientoPediatricRepository;

class SeguimientoPediatricController
{
    private SeguimientoPediatricRepository $repo;

    public function __construct(SeguimientoPediatricRepository $repo)
    {
        $this->repo = $repo;
    }

    public function list()
    {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 200;
            $rows = $this->repo->getList($limit);
            ApiResponse::json(['success' => true, 'rows' => $rows]);
        } catch (\Exception $e) {
            ApiResponse::error('Error loading pediatric visits: ' . $e->getMessage());
        }
    }

    public function get()
    {
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (!$id) {
                ApiResponse::error('Missing ID');
                return;
            }
            $row = $this->repo->getById($id);
            if (!$row) {
                ApiResponse::error('Record not found');
                return;
            }
            ApiResponse::json(['success' => true, 'row' => $row]);
        } catch (\Exception $e) {
            ApiResponse::error('Error fetching record: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || empty($data['patient_id']) || empty($data['visit_date'])) {
                ApiResponse::error('Invalid data. Patient ID and Visit Date are required.');
                return;
            }
            
            $id = $this->repo->create($data);
            ApiResponse::json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            ApiResponse::error('Failed to create record: ' . $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || empty($data['id'])) {
                ApiResponse::error('Invalid data. ID is required.');
                return;
            }

            $id = (int)$data['id'];
            unset($data['id']);

            $this->repo->update($id, $data);
            ApiResponse::json(['success' => true]);
        } catch (\Exception $e) {
            ApiResponse::error('Failed to update record: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || empty($data['id'])) {
                ApiResponse::error('Invalid data. ID is required.');
                return;
            }

            $id = (int)$data['id'];
            $this->repo->delete($id);
            ApiResponse::json(['success' => true]);
        } catch (\Exception $e) {
            ApiResponse::error('Failed to delete record: ' . $e->getMessage());
        }
    }
}