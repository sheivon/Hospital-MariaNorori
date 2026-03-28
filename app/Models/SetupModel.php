<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;
use RuntimeException;

class SetupModel
{
    public function loadConfig(): array
    {
        return Database::config();
    }

    public function testConnection(array $config): string
    {
        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $config['DB_HOST'], $config['DB_PORT']),
                $config['DB_USER'],
                $config['DB_PASS'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $pdo->query('SELECT 1');
            return 'Connection successful.';
        } catch (PDOException $e) {
            throw new RuntimeException('Connection failed: ' . $e->getMessage());
        }
    }

    public function saveConfig(array $config): string
    {
        $envFile = APP_ROOT . '/.env';
        $lines = [];

        if (is_file($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        }

        $keys = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS'];
        $newLines = [];

        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '' || strpos($trim, '#') === 0 || strpos($trim, '=') === false) {
                $newLines[] = $line;
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if (in_array($key, $keys, true)) {
                continue; // we'll add updated values below
            }
            $newLines[] = $line;
        }

        foreach ($keys as $key) {
            if (isset($config[$key])) {
                $newLines[] = sprintf('%s=%s', $key, $config[$key]);
            }
        }

        if (false === @file_put_contents($envFile, implode(PHP_EOL, $newLines) . PHP_EOL)) {
            throw new RuntimeException('Unable to write .env file. Check permissions.');
        }

        return 'Configuration saved to .env.';
    }

    public function createDatabase(array $config): string
    {
        $this->validateDbIdentifier($config['DB_NAME']);

        try {
            $pdo = $this->connectServer($config);
            $pdo->exec(sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                $config['DB_NAME']
            ));

            return sprintf("Database '%s' created or already exists.", $config['DB_NAME']);
        } catch (PDOException $e) {
            if ($this->canUseExistingDatabase($config)) {
                return sprintf(
                    "No CREATE DATABASE permission for user '%s'. Using existing database '%s'.",
                    $config['DB_USER'],
                    $config['DB_NAME']
                );
            }
            throw $e;
        }
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
            $messages[] = "Created user: {$user['username']} (password hashed)";
        }

        return $messages;
    }

    public function createSampleData(array $config): array
    {
        $pdo = $this->connectDatabase($config);
        $messages = [];

        $patientCheck = $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
        if ($patientCheck > 0) {
            $messages[] = 'Sample data not inserted: patients already exist.';
            return $messages;
        }

        $patients = [
            ['first_name'=>'Carlos','last_name'=>'Mendez','cedula'=>'080119990001','dob'=>'1999-01-08','gender'=>'M','marital_status'=>'single','phone'=>'555-0101','email'=>'carlos.mendez@example.com','insurance_provider'=>'INSURSA','insurance_policy_no'=>'INS-1001','expediente_no'=>'EXP-0001','procedencia'=>'Local','father_name'=>'Jose Mendez','mother_name'=>'Ana Lopez','education_level'=>'College','employer'=>'Clinica A','address'=>'123 Principal St','notes'=>'Allergic to penicillin'],
            ['first_name'=>'Maria','last_name'=>'Gonzalez','cedula'=>'080219985002','dob'=>'1985-02-08','gender'=>'F','marital_status'=>'married','phone'=>'555-0202','email'=>'maria.gonzalez@example.com','insurance_provider'=>'SEGUROS','insurance_policy_no'=>'INS-1002','expediente_no'=>'EXP-0002','procedencia'=>'Regional','father_name'=>'Miguel Gonzalez','mother_name'=>'Lucia Torres','education_level'=>'High School','employer'=>'Hospital General','address'=>'45 Avenida Central','notes'=>'Diabetic type 2'],
        ];

        $insertPatient = $pdo->prepare('INSERT INTO patients (first_name, last_name, cedula, dob, gender, marital_status, phone, email, insurance_provider, insurance_policy_no, expediente_no, procedencia, father_name, mother_name, education_level, employer, address, notes, created_at, updated_at) VALUES (:first_name, :last_name, :cedula, :dob, :gender, :marital_status, :phone, :email, :insurance_provider, :insurance_policy_no, :expediente_no, :procedencia, :father_name, :mother_name, :education_level, :employer, :address, :notes, NOW(), NOW())');

        foreach ($patients as $patient) {
            $insertPatient->execute($patient);
            $patientId = (int)$pdo->lastInsertId();
            $messages[] = "Inserted patient #{$patientId}: {$patient['first_name']} {$patient['last_name']}";

            $insertDiag = $pdo->prepare('INSERT INTO diagnostics (patient_id, encounter_id, type, unit, room, icd10_code, description, status, severity, date, time, plan, weight, height, age, sex, expediente_no, cedula, inss_no, created_by, created_at, updated_at) VALUES (:patient_id, NULL, :type, :unit, :room, :icd10, :description, :status, :severity, :date, :time, :plan, :weight, :height, :age, :sex, :expediente_no, :cedula, :inss_no, NULL, NOW(), NOW())');
            $insertDiag->execute([
                ':patient_id' => $patientId,
                ':type' => 'General Checkup',
                ':unit' => 'General',
                ':room' => '101',
                ':icd10' => 'Z00.00',
                ':description' => "Routine clinical assessment for {$patient['first_name']}",
                ':status' => 'completed',
                ':severity' => 'low',
                ':date' => date('Y-m-d'),
                ':time' => date('H:i:s'),
                ':plan' => 'Follow standard monitoring',
                ':weight' => 70.0,
                ':height' => 170.0,
                ':age' => 25,
                ':sex' => $patient['gender'],
                ':expediente_no' => $patient['expediente_no'],
                ':cedula' => $patient['cedula'],
                ':inss_no' => $patient['insurance_policy_no'],
            ]);
            $messages[] = "Inserted diagnostics for patient #{$patientId}";
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

    private function canUseExistingDatabase(array $config): bool
    {
        try {
            $pdo = $this->connectDatabase($config);
            $pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function validateDbIdentifier(string $name): void
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new RuntimeException("Invalid database name: {$name}");
        }
    }
}
