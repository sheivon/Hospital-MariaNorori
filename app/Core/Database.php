<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function config(): array
    {
        return self::loadConfig();
    }

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        self::ensurePdoMysqlAvailable();

        $config = self::config();
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['DB_HOST'],
            $config['DB_PORT'],
            $config['DB_NAME']
        );

        try {
            self::$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            self::applySchemaPatches(self::$pdo); // not needed if you are sure the schema is already set up
        } catch (PDOException $e) {
            $message = 'DB Connection failed: ' . $e->getMessage();
            if (stripos($e->getMessage(), 'could not find driver') !== false) {
                $message .= "\n" . self::buildPdoMysqlMissingMessage();
            }
            die($message);
            // In a production environment, log the error and show a generic error page.
            // For development, it's okay to show the detailed error.
            error_log('DB Connection failed: ' . $e->getMessage());
            http_response_code(500);
            die('Database connection failed. Please check the logs.');
        }

        return self::$pdo;
    }

    public static function getInstance(): PDO
    {
        return self::pdo();
    }

    private static function loadConfig(): array
    {
        $config = [
           /* 'DB_HOST' => getenv('DB_HOST') ?: '192.168.1.204', //dev ip 192.168.1.204 wsl mysql
            'DB_NAME' => getenv('DB_NAME') ?: 'hospital',   //DB
            'DB_USER' => getenv('DB_USER') ?: 'Marianorori',   //dev user
            'DB_PORT' => getenv('DB_PORT') ?: '3307',   //dev port
            'DB_PASS' => getenv('DB_PASS') ?: 'SuperNoror!26*', //dev pass SuperNoror!26**/
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
                $config[$key] = $value;
            }
        }

        return $config;
    }

    private static function ensurePdoMysqlAvailable(): void
    {
        if (extension_loaded('pdo_mysql')) {
            return;
        }

        die(self::buildPdoMysqlMissingMessage()); // Or throw an exception
    }

    private static function buildPdoMysqlMissingMessage(): string
    {
        $iniFile = php_ini_loaded_file() ?: 'not loaded';

        return sprintf(
            'MySQL driver not found. The PHP extension "pdo_mysql" is not enabled.%sPHP binary: %s%sLoaded php.ini: %s%sOn Windows, enable "extension=pdo_mysql" in php.ini and restart Apache/PHP server.',
            PHP_EOL,
            PHP_BINARY,
            PHP_EOL,
            $iniFile,
            PHP_EOL
        );
    }


    private static function applySchemaPatches(PDO $pdo): void
    {
        // Example: Check if a specific table exists, and if not, create it.
        $stmt = $pdo->query("SHOW TABLES LIKE 'appointments'");
        if ($stmt->rowCount() === 0) {
            $createTableSQL = "
                CREATE TABLE appointments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    patient_id INT NOT NULL,
                    provider_id INT NOT NULL,
                    appointment_datetime DATETIME NOT NULL,
                    reason VARCHAR(255) NOT NULL,
                    status ENUM('scheduled', 'completed', 'canceled') DEFAULT 'scheduled',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB;
            ";
            $pdo->exec($createTableSQL);
        }

        // Add more schema checks and patches as needed.
    }
    
}
