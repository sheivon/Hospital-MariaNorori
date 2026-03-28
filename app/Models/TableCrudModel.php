<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use RuntimeException;

class TableCrudModel
{
    private PDO $pdo;

    private array $tables = [
        'users' => ['pk' => 'id', 'label' => 'Users'],
        'patients' => ['pk' => 'id', 'label' => 'Patients'],
        'patient_contacts' => ['pk' => 'id', 'label' => 'Patient Contacts'],
        'encounters' => ['pk' => 'id', 'label' => 'Encounters'],
        'patient_conditions' => ['pk' => 'id', 'label' => 'Patient Conditions'],
        'patient_allergies' => ['pk' => 'id', 'label' => 'Patient Allergies'],
        'diagnostics' => ['pk' => 'id', 'label' => 'Diagnostics'],
        'tests' => ['pk' => 'id', 'label' => 'Tests'],
        'vitals' => ['pk' => 'id', 'label' => 'Vitals'],
        'clinical_notes' => ['pk' => 'id', 'label' => 'Clinical Notes'],
        'treatment_plans' => ['pk' => 'id', 'label' => 'Treatment Plans'],
        'clinical_procedures' => ['pk' => 'id', 'label' => 'Clinical Procedures'],
        'medications_catalog' => ['pk' => 'id', 'label' => 'Medication Catalog'],
        'prescriptions' => ['pk' => 'id', 'label' => 'Prescriptions'],
        'treatment_administration' => ['pk' => 'id', 'label' => 'Treatment Administration'],
        'immunizations' => ['pk' => 'id', 'label' => 'Immunizations'],
        'appointments' => ['pk' => 'id', 'label' => 'Appointments'],
        'admissions' => ['pk' => 'id', 'label' => 'Admissions'],
        'bed_movements' => ['pk' => 'id', 'label' => 'Bed Movements'],
        'chat_messages' => ['pk' => 'id', 'label' => 'Chat Messages'],
        'audit_logs' => ['pk' => 'id', 'label' => 'Audit Logs'],
    ];

    private array $deletedAtCache = [];

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function listTables(): array
    {
        return $this->tables;
    }

    private function hasDeletedAt(string $table): bool
    {
        if (array_key_exists($table, $this->deletedAtCache)) {
            return $this->deletedAtCache[$table];
        }

        $stmt = $this->pdo->query(sprintf("SHOW COLUMNS FROM `%s` LIKE 'deleted_at'", $table));
        $this->deletedAtCache[$table] = (bool) $stmt->fetch();
        return $this->deletedAtCache[$table];
    }

    public function listRows(string $table, int $limit = 200): array
    {
        $meta = $this->tableMeta($table);
        $pk = $meta['pk'];
        $limit = max(1, min(500, $limit));
        $whereDeleted = $this->hasDeletedAt($table) ? ' WHERE deleted_at IS NULL' : '';
        $sql = sprintf('SELECT * FROM `%s`%s ORDER BY `%s` DESC LIMIT %d', $table, $whereDeleted, $pk, $limit);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function describe(string $table): array
    {
        $this->tableMeta($table);
        $stmt = $this->pdo->query(sprintf('SHOW COLUMNS FROM `%s`', $table));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRow(string $table, array $payload): int
    {
        $meta = $this->tableMeta($table);
        $columns = $this->editableColumns($table, false);
        $data = $this->sanitizeData($table, $payload, $columns);

        if (empty($data)) {
            throw new RuntimeException('No valid data provided.');
        }

        $fields = array_keys($data);
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', array_map(fn($c) => "`{$c}`", $fields)),
            implode(', ', array_map(fn($c) => ":{$c}", $fields))
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateRow(string $table, int $id, array $payload): void
    {
        $meta = $this->tableMeta($table);
        $pk = $meta['pk'];
        $columns = $this->editableColumns($table, true);
        $data = $this->sanitizeData($table, $payload, $columns);

        if (empty($data)) {
            throw new RuntimeException('No updatable fields provided.');
        }

        $sets = [];
        foreach (array_keys($data) as $field) {
            $sets[] = sprintf('`%s` = :%s', $field, $field);
        }
        $data['__id'] = $id;

        $whereDeleted = $this->hasDeletedAt($table) ? ' AND deleted_at IS NULL' : '';
        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE `%s` = :__id%s',
            $table,
            implode(', ', $sets),
            $pk,
            $whereDeleted
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function softDelete(string $table, int $id): void
    {
        $meta = $this->tableMeta($table);
        $pk = $meta['pk'];
        if ($this->hasDeletedAt($table)) {
            $sql = sprintf('UPDATE `%s` SET deleted_at = NOW() WHERE `%s` = :id AND deleted_at IS NULL', $table, $pk);
        } else {
            $sql = sprintf('DELETE FROM `%s` WHERE `%s` = :id', $table, $pk);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    private function tableMeta(string $table): array
    {
        if (!isset($this->tables[$table])) {
            throw new RuntimeException('Table not allowed.');
        }
        return $this->tables[$table];
    }

    private function editableColumns(string $table, bool $forUpdate): array
    {
        $meta = $this->tableMeta($table);
        $pk = $meta['pk'];
        $skip = [$pk, 'created_at', 'updated_at', 'deleted_at'];
        $columns = $this->describe($table);
        $editable = [];

        foreach ($columns as $column) {
            $name = $column['Field'] ?? '';
            if ($name === '' || in_array($name, $skip, true)) {
                continue;
            }
            if ($forUpdate && ($column['Extra'] ?? '') === 'auto_increment') {
                continue;
            }
            $editable[] = $name;
        }

        return $editable;
    }

    private function sanitizeData(string $table, array $payload, array $allowed): array
    {
        $clean = [];
        foreach ($allowed as $field) {
            if (!array_key_exists($field, $payload)) {
                continue;
            }
            $value = $payload[$field];
            if ($value === '') {
                $value = null;
            }

            if ($table === 'users' && $field === 'password' && $value !== null) {
                $value = password_hash((string)$value, PASSWORD_DEFAULT);
            }

            $clean[$field] = $value;
        }
        return $clean;
    }
}
