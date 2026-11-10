<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\PatientRepositoryInterface;
use Exception;

class PatientService extends BaseService
{
    /** Maximum allowed photo size in bytes (5 MB). */
    private const MAX_PHOTO_BYTES = 5242880;

    /** MIME types accepted for patient photos. */
    private const ALLOWED_PHOTO_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    public function __construct(PatientRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getAllPatients(array $filters = []): array
    {
        return $this->repository->all($filters);
    }

    public function getPatient(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function createPatient(array $data, ?array $photoFile = null): int
    {
        $this->validatePayload($data);

        $photoPath = $this->storePhoto($photoFile);
        $data['photo_path'] = $photoPath;

        try {
            return $this->repository->create($data);
        } catch (\Throwable $e) {
            // Never leave an orphaned file behind if the insert fails.
            $this->deletePhotoFile($photoPath);
            throw $e;
        }
    }

    public function updatePatient(int $id, array $data, ?array $photoFile = null): bool
    {
        $this->ensureValidId($id, 'patient id');
        $this->validatePayload($data, $id);

        $existing = $this->repository->find($id);
        if ($existing === null) {
            throw new Exception('Patient not found');
        }

        $oldPhotoPath = $existing['photo_path'] ?? null;
        $removePhoto = !empty($data['remove_photo']);

        // A newly uploaded photo wins; otherwise keep or clear the existing one.
        $newPhotoPath = $this->storePhoto($photoFile);
        if ($newPhotoPath === null && $removePhoto) {
            $finalPhotoPath = null;
        } elseif ($newPhotoPath !== null) {
            $finalPhotoPath = $newPhotoPath;
        } else {
            $finalPhotoPath = $oldPhotoPath;
        }

        $data['photo_path'] = $finalPhotoPath;
        unset($data['remove_photo']);

        try {
            $updated = $this->repository->update($id, $data);
        } catch (\Throwable $e) {
            $this->deletePhotoFile($newPhotoPath);
            throw $e;
        }

        if ($updated && $finalPhotoPath !== $oldPhotoPath) {
            $this->deletePhotoFile($oldPhotoPath);
        }

        return $updated;
    }

    public function deletePatient(int $id): bool
    {
        $this->ensureValidId($id, 'patient id');

        $existing = $this->repository->find($id);
        $result = $this->repository->delete($id);

        if ($result && $existing !== null) {
            $this->deletePhotoFile($existing['photo_path'] ?? null);
        }

        return $result;
    }

    /**
     * Validate and store an uploaded patient photo.
     *
     * @param array|null $file One entry of $_FILES (e.g. $_FILES['photo']).
     * @return string|null Web path of the stored photo (e.g. /uploads/patients/xxx.jpg),
     *                     or null when no file was submitted.
     * @throws Exception When the upload is invalid or could not be stored.
     */
    private function storePhoto(?array $file): ?string
    {
        if ($file === null || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ((int)($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new Exception('Photo upload failed (PHP error ' . (int)$file['error'] . ')');
        }

        $tmpName = (string)($file['tmp_name'] ?? '');
        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            throw new Exception('Invalid photo upload');
        }

        if ((int)($file['size'] ?? 0) > self::MAX_PHOTO_BYTES) {
            throw new Exception('Photo must be 5 MB or smaller');
        }

        $info = @getimagesize($tmpName);
        if ($info === false) {
            throw new Exception('Photo must be a valid image');
        }

        $mime = (string)($info['mime'] ?? '');
        if (!isset(self::ALLOWED_PHOTO_MIME_TYPES[$mime])) {
            throw new Exception('Photo must be a JPG, PNG, WebP or GIF image');
        }

        $dir = $this->photoDirectory();
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new Exception('Could not create photo upload directory');
        }

        $extension = self::ALLOWED_PHOTO_MIME_TYPES[$mime];
        $filename = date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

        if (!@move_uploaded_file($tmpName, $dir . DIRECTORY_SEPARATOR . $filename)) {
            throw new Exception('Could not store the uploaded photo');
        }

        return '/uploads/patients/' . $filename;
    }

    /**
     * Delete a previously stored photo file. Only files inside the dedicated
     * upload directory are removed (guards against path traversal).
     */
    private function deletePhotoFile(?string $photoPath): void
    {
        if ($photoPath === null || $photoPath === '') {
            return;
        }

        // Only accept paths inside our upload directory.
        if (!str_starts_with($photoPath, '/uploads/patients/')) {
            return;
        }

        $basename = basename($photoPath);
        if ($basename === '' || $basename === '.' || $basename === '..') {
            return;
        }

        $base = realpath($this->photoDirectory());
        if ($base === false) {
            return;
        }

        $full = realpath($base . DIRECTORY_SEPARATOR . $basename);
        if ($full !== false && is_file($full) && str_starts_with($full, $base . DIRECTORY_SEPARATOR)) {
            @unlink($full);
        }
    }

    private function photoDirectory(): string
    {
        return APP_ROOT . '/public/uploads/patients';
    }

    private function validatePayload(array $data, ?int $exceptId = null): void
    {
        $cedula = trim((string)($data['cedula'] ?? ''));
        if ($cedula !== '') {
            $existing = $this->repository->findByCedula($cedula, $exceptId);
            if ($existing !== null) {
                throw new Exception('Cédula already in use');
            }
        }

        $email = trim((string)($data['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email');
        }
    }
}
