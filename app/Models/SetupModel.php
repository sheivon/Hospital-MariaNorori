<?php

namespace App\Models;

use PDO;
use RuntimeException;

class SetupModel
{
    public function loadConfig(): array
    {
        $config = [
            'DB_HOST' => getenv('DB_HOST') ?: '127.0.0.1',
            'DB_NAME' => getenv('DB_NAME') ?: 'hospital',
            'DB_USER' => getenv('DB_USER') ?: 'root',
            'DB_PORT' => getenv('DB_PORT') ?: '3306',
            'DB_PASS' => getenv('DB_PASS') ?: '',
        ];

        $envFile = APP_ROOT . '/.env';
        if (!is_file($envFile)) {
            return $config;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if ($key !== '') {
                $config[$key] = trim($value, "\"'");
            }
        }

        return $config;
    }

    public function createDatabase(array $config): string
    {
        $this->validateDbIdentifier($config['DB_NAME']);

        $pdo = $this->connectServer($config);
        $pdo->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $config['DB_NAME']
        ));

        return sprintf("Database '%s' created or already exists.", $config['DB_NAME']);
    }

    public function dropAllTables(array $config): array
    {
        $pdo = $this->connectDatabase($config);
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        $tables = $pdo->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"')->fetchAll(PDO::FETCH_NUM) ?: [];
        $messages = [];

        foreach ($tables as $row) {
            $tableName = $row[0] ?? null;
            if (!$tableName) {
                continue;
            }
            $pdo->exec(sprintf('DROP TABLE IF EXISTS `%s`', $tableName));
            $messages[] = "Dropped table: {$tableName}";
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        if (empty($messages)) {
            $messages[] = 'No tables found to drop.';
        }

        return $messages;
    }

    public function runSchemaFromFile(array $config, string $schemaFile): string
    {
        if (!is_file($schemaFile)) {
            throw new RuntimeException("Schema file not found: {$schemaFile}");
        }

        $sql = file_get_contents($schemaFile);
        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException('Schema file is empty or unreadable.');
        }

        $pdo = $this->connectDatabase($config);
        $pdo->exec($sql);

        return 'Schema executed successfully.';
    }

    public function createDefaultUsers(array $config): array
    {
        $pdo = $this->connectDatabase($config);

        $defaultUsers = [
            ['username' => 'admin', 'password' => 'admin123', 'fullname' => 'Administrator', 'role' => 'admin'],
            ['username' => 'doctor', 'password' => 'doctor123', 'fullname' => 'General Doctor', 'role' => 'doctor'],
        ];

        $messages = [];
        $check = $pdo->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
        $insert = $pdo->prepare('INSERT INTO users (username, password, fullname, cedula, role) VALUES (:u, :p, :f, :c, :r)');

        foreach ($defaultUsers as $user) {
            $check->execute([':u' => $user['username']]);
            if ($check->fetch()) {
                $messages[] = "User '{$user['username']}' already exists.";
                continue;
            }

            $insert->execute([
                ':u' => $user['username'],
                ':p' => password_hash($user['password'], PASSWORD_DEFAULT),
                ':f' => $user['fullname'],
                ':c' => null,
                ':r' => $user['role'],
            ]);
            $messages[] = "Created user: {$user['username']} (password: {$user['password']})";
        }

        return $messages;
    }

    private function connectServer(array $config): PDO
    {
        $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $config['DB_HOST'], $config['DB_PORT']);
        return new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private function connectDatabase(array $config): PDO
    {
        $this->validateDbIdentifier($config['DB_NAME']);
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['DB_HOST'],
            $config['DB_PORT'],
            $config['DB_NAME']
        );

        return new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private function validateDbIdentifier(string $name): void
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new RuntimeException("Invalid database name: {$name}");
        }
    }
}
