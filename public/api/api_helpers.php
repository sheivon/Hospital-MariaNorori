<?php

function hasColumn(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :column");
    $stmt->execute([':column' => $column]);
    return (bool) $stmt->fetch();
}

function softDeleteCondition(PDO $pdo, string $table, string $alias = ''): string
{
    if (!hasColumn($pdo, $table, 'deleted_at')) {
        return '';
    }
    $prefix = $alias !== '' ? $alias . '.' : '';
    return " AND {$prefix}deleted_at IS NULL";
}
