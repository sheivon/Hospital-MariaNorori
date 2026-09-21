<?php

namespace App\Services;

use App\Repositories\ExamRequestRepository;
use App\Repositories\ExamTypeRepository;
use InvalidArgumentException;

class ExamRequestService extends BaseService
{
    private const STATUSES = ['pending', 'completed', 'cancelled'];

    public function __construct(ExamRequestRepository $repository, private ExamTypeRepository $typeRepository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): int
    {
        $data = $this->normalise($data);
        $this->validateCreate($data);
        return parent::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $this->ensureValidId($id);
        $data = $this->normalise($data);
        if (isset($data['exam_type_id']) && !$this->typeRepository->isActive((int) $data['exam_type_id'])) {
            throw new InvalidArgumentException('Invalid or inactive exam type');
        }
        if (isset($data['status']) && !in_array($data['status'], self::STATUSES, true)) {
            throw new InvalidArgumentException('Invalid status');
        }
        return $this->repository->update($id, $data);
    }

    private function validateCreate(array &$data): void
    {
        if (empty($data['patient_id']) || (int) $data['patient_id'] <= 0) {
            throw new InvalidArgumentException('A valid patient is required');
        }
        if (empty($data['exam_type_id']) || !$this->typeRepository->isActive((int) $data['exam_type_id'])) {
            throw new InvalidArgumentException('A valid active exam type is required');
        }
        if (empty($data['request_date'])) {
            $data['request_date'] = date('Y-m-d');
        }
        if (!empty($data['status']) && !in_array($data['status'], self::STATUSES, true)) {
            throw new InvalidArgumentException('Invalid status');
        }
        $data['status'] = $data['status'] ?? 'pending';
    }

    private function normalise(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value) === '' ? null : trim($value);
            }
        }
        foreach (['patient_id', 'exam_type_id'] as $field) {
            if (isset($data[$field])) $data[$field] = (int) $data[$field];
        }
        return $data;
    }
}
