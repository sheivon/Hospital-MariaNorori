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
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            self::applySchemaPatches(self::$pdo); // not needed if you are sure the schema is already set up
        } catch (PDOException $e) {
            $message = 'DB Connection failed: ' . $e->getMessage();
            if (stripos($e->getMessage(), 'could not find driver') !== false) {
                $message .= "\n" . self::buildPdoMysqlMissingMessage();
            }

            // Any connection failure means the database needs attention. Send the
            // user to setup.php (create DB / tables / users) instead of showing a
            // raw error on every page. Never redirect from the setup flow itself.
            if (self::setupRedirectPath() !== null && !headers_sent()) {
                http_response_code(302);
                header('Location: ' . self::setupRedirectPath());
                exit;
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

    /**
     * Absolute path to setup.php relative to the app's web root.
     * Returns null when the current request is part of the setup flow itself
     * (setup.php or api/setup_action.php) so the user can never be redirected
     * into a loop.
     */
    private static function setupRedirectPath(): ?string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $page = basename($script);
        if ($page === 'setup.php' || $page === 'setup_action.php') {
            return null;
        }

        // setup.php lives at the project base. If the failing page sits in a
        // subdirectory (e.g. /admin/x.php or /api/x.php), walk up one level so
        // the redirect also works when the app is served from a subfolder.
        $dir = rtrim((string)dirname($script), '/\\');
        $base = ($dir !== '' && $dir !== '/' && $dir !== '.') ? dirname($dir) : '';

        return ($base === '' || $base === '/' || $base === '.') ? '/setup.php' : $base . '/setup.php';
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
        // ------------------------------------------------------------------
        // appointments table
        // ------------------------------------------------------------------
        // Source of truth: database/hospital_schema.sql (and migrations/init.sql).
        // This patcher is only a safety net for fresh installs and for databases
        // that were created against an earlier, narrower version of this table
        // (provider_id, appointment_datetime, ENUM status). It must stay in
        // sync with AppointmentRepository.php, which reads/writes:
        //   patient_id, encounter_id, provider_user_id, appointment_at,
        //   reason, status, notes, created_by, deleted_at, created_at, updated_at.
        $tableExists = $pdo->query("SHOW TABLES LIKE 'appointments'")->rowCount() > 0;

        if (!$tableExists) {
            $pdo->exec("
                CREATE TABLE appointments (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    patient_id INT UNSIGNED NOT NULL,
                    encounter_id INT UNSIGNED DEFAULT NULL,
                    provider_user_id INT UNSIGNED DEFAULT NULL,
                    appointment_at DATETIME NOT NULL,
                    reason VARCHAR(255) DEFAULT NULL,
                    status VARCHAR(30) NOT NULL DEFAULT 'scheduled',
                    notes TEXT DEFAULT NULL,
                    created_by INT UNSIGNED DEFAULT NULL,
                    deleted_at DATETIME DEFAULT NULL,
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_appointments_patient (patient_id),
                    INDEX idx_appointments_provider (provider_user_id),
                    INDEX idx_appointments_at (appointment_at),
                    INDEX idx_appointments_status (status),
                    INDEX idx_appointments_deleted_at (deleted_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            return;
        }

        // Table already exists — upgrade old schemas in place. Every step is
        // gated on a SHOW COLUMNS check so it is safe to re-run.
        $columns = [];
        foreach ($pdo->query("SHOW COLUMNS FROM appointments") as $row) {
            $columns[$row['Field']] = $row;
        }

        // Rename legacy provider_id → provider_user_id (if old column is present
        // and the new one is not). Done as a copy + drop so we don't lose data.
        if (isset($columns['provider_id']) && !isset($columns['provider_user_id'])) {
            $pdo->exec("ALTER TABLE appointments CHANGE COLUMN provider_id provider_user_id INT UNSIGNED DEFAULT NULL");
            unset($columns['provider_id']);
            $columns['provider_user_id'] = true;
        }
        if (isset($columns['provider_user_id']) && !isset($columns['provider_id'])) {
            // already modern
        }

        // Rename appointment_datetime → appointment_at
        if (isset($columns['appointment_datetime']) && !isset($columns['appointment_at'])) {
            $pdo->exec("ALTER TABLE appointments CHANGE COLUMN appointment_datetime appointment_at DATETIME NOT NULL");
            unset($columns['appointment_datetime']);
            $columns['appointment_at'] = true;
        }

        // Migrate ENUM status → VARCHAR(30) so new statuses (confirmed, no_show) work.
        if (isset($columns['status'])) {
            $type = strtolower($columns['status']['Type'] ?? '');
            if (strpos($type, 'enum(') === 0) {
                // Map legacy values to modern ones, then widen the column type.
                // Old: scheduled/completed/canceled  →  New: scheduled/confirmed/completed/cancelled/no_show
                $pdo->exec("UPDATE appointments SET status = 'cancelled' WHERE status = 'canceled'");
                $pdo->exec("ALTER TABLE appointments MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'scheduled'");
                $columns['status'] = true;
            }
        }

        // Add every modern column that is still missing. nullableString / nullableInt
        // in the repository treats NULL as 'no value', so DEFAULT NULL is safe.
        $addColumn = function (string $name, string $definition) use (&$columns, $pdo) {
            if (!isset($columns[$name])) {
                $pdo->exec("ALTER TABLE appointments ADD COLUMN {$name} {$definition}");
                $columns[$name] = true;
            }
        };
        $addColumn('encounter_id',     "INT UNSIGNED DEFAULT NULL AFTER patient_id");
        $addColumn('notes',            "TEXT DEFAULT NULL AFTER reason");
        $addColumn('created_by',       "INT UNSIGNED DEFAULT NULL AFTER notes");
        $addColumn('deleted_at',       "DATETIME DEFAULT NULL AFTER created_by");
        $addColumn('updated_at',       "TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at");

        // Loosen NOT NULL on reason — repository treats '' as NULL, and the form
        // allows blank reasons.
        if (isset($columns['reason']) && stripos((string)($columns['reason']['Null'] ?? ''), 'no') === 0) {
            $pdo->exec("ALTER TABLE appointments MODIFY COLUMN reason VARCHAR(255) DEFAULT NULL");
            $columns['reason'] = true;
        }

        // Useful indexes for the list query (idempotent: ignore duplicate-key errors).
        $indexes = [];
        foreach ($pdo->query("SHOW INDEX FROM appointments") as $row) {
            $indexes[$row['Key_name']] = true;
        }
        $addIndex = function (string $name, string $cols) use (&$indexes, $pdo) {
            if (isset($indexes[$name])) return;
            try {
                $pdo->exec("ALTER TABLE appointments ADD INDEX {$name} ({$cols})");
            } catch (PDOException $e) {
                // Duplicate key name or column already covered — safe to ignore.
            }
        };
        $addIndex('idx_appointments_patient',   'patient_id');
        $addIndex('idx_appointments_provider',  'provider_user_id');
        $addIndex('idx_appointments_at',        'appointment_at');
        $addIndex('idx_appointments_status',    'status');
        $addIndex('idx_appointments_deleted_at','deleted_at');

        // ------------------------------------------------------------------
        // users.role -> user_roles.role foreign key
        // ------------------------------------------------------------------
        // Source of truth: migrations/init.sql (and database/hospital_schema.sql).
        // Ensures the read-only role catalog exists and that every value in
        // users.role references it, matching the validation already done by
        // UserService/UserRoleRepository. Idempotent: safe to re-run.
        $rolesExists = $pdo->query("SHOW TABLES LIKE 'user_roles'")->rowCount() > 0;

        if (!$rolesExists) {
            $pdo->exec("
                CREATE TABLE user_roles (
                    role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    role VARCHAR(50) NOT NULL UNIQUE,
                    accesstype VARCHAR(50) NOT NULL,
                    INDEX idx_user_roles_access (accesstype)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            try {
                $pdo->exec("
                    INSERT INTO user_roles (role, accesstype) VALUES
                        ('admin', 'full'),
                        ('doctor', 'clinical'),
                        ('user', 'basic');
                ");
            } catch (PDOException $e) {
                // Read-only trigger or concurrent seed — roles already present.
            }
        }

        // Adopt any orphaned role values so the constraint can be applied.
        try {
            $pdo->exec("
                INSERT INTO user_roles (role, accesstype)
                SELECT DISTINCT u.role, 'custom'
                FROM users u
                LEFT JOIN user_roles r ON r.role = u.role
                WHERE r.role_id IS NULL;
            ");
        } catch (PDOException $e) {
            // Read-only trigger blocks the insert; service-level validation
            // already keeps roles valid in catalogs managed by init.sql.
        }

        $fkExists = false;
        foreach ($pdo->query("SHOW CREATE TABLE users") as $row) {
            if (stripos((string)$row['Create Table'], 'fk_users_role') !== false
                && stripos((string)$row['Create Table'], 'user_roles') !== false) {
                $fkExists = true;
            }
        }

        if (!$fkExists) {
            try {
                $pdo->exec("
                    ALTER TABLE users
                    ADD CONSTRAINT fk_users_role
                    FOREIGN KEY (role) REFERENCES user_roles (role)
                    ON UPDATE CASCADE ON DELETE RESTRICT;
                ");
            } catch (PDOException $e) {
                // Constraint already exists under another name, or legacy data
                // could not be repaired — never block app boot over this.
            }
        }
    }
    
}
