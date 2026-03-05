<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        self::ensurePdoMysqlAvailable();

        $config = self::loadConfig();
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
            ]);
        } catch (PDOException $e) {
            $message = 'DB Connection failed: ' . $e->getMessage();
            if (stripos($e->getMessage(), 'could not find driver') !== false) {
                $message .= "\n" . self::buildPdoMysqlMissingMessage();
            }
            die($message);
        }

        return self::$pdo;
    }

    private static function loadConfig(): array
    {
        $config = [
            'DB_HOST' => getenv('DB_HOST') ?: '192.168.1.204', //dev ip 192.168.1.204 wsl mysql
            'DB_NAME' => getenv('DB_NAME') ?: 'hospital',   //DB
            'DB_USER' => getenv('DB_USER') ?: 'root',   //dev user
            'DB_PORT' => getenv('DB_PORT') ?: '3307',   //dev port
            'DB_PASS' => getenv('DB_PASS') ?: 'Kilabone15*', //dev pass
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

        die(self::buildPdoMysqlMissingMessage());
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
}
