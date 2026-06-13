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
            ]);
            self::applySchemaPatches(self::$pdo);
        } catch (PDOException $e) {
            $message = 'DB Connection failed: ' . $e->getMessage();
            if (stripos($e->getMessage(), 'could not find driver') !== false) {
                $message .= "\n" . self::buildPdoMysqlMissingMessage();
            }
            die($message);
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
            'DB_HOST' => getenv('DB_HOST') ?: '192.168.1.204', //dev ip 192.168.1.204 wsl mysql
            'DB_NAME' => getenv('DB_NAME') ?: 'hospital',   //DB
            'DB_USER' => getenv('DB_USER') ?: 'Marianorori',   //dev user
            'DB_PORT' => getenv('DB_PORT') ?: '3307',   //dev port
            'DB_PASS' => getenv('DB_PASS') ?: 'SuperNoror!26*', //dev pass SuperNoror!26*
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

    private static function applySchemaPatches(PDO $pdo): void
    {
        $patches = [
            'tests' => [
                'result' => 'TEXT NULL',
            ],
            'exam_requests' => [
                'result' => 'TEXT NULL',
            ],
        ];

        foreach ($patches as $table => $columns) {
            foreach ($columns as $column => $definition) {
                self::ensureColumnExists($pdo, $table, $column, $definition);
            }
        }

        self::ensureTableExists($pdo, 'seguimiento_integral_ninez_adolescencia', <<<'SQL'
CREATE TABLE IF NOT EXISTS seguimiento_integral_ninez_adolescencia (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  visit_date DATE NOT NULL,
  respira_rapida TINYINT(1) NOT NULL DEFAULT 0,
  dificultad_alimentarse TINYINT(1) NOT NULL DEFAULT 0,
  dificultad_respirar TINYINT(1) NOT NULL DEFAULT 0,
  convulsiones TINYINT(1) NOT NULL DEFAULT 0,
  letargia TINYINT(1) NOT NULL DEFAULT 0,
  inconciencia TINYINT(1) NOT NULL DEFAULT 0,
  flacidez TINYINT(1) NOT NULL DEFAULT 0,
  vomitos TINYINT(1) NOT NULL DEFAULT 0,
  diarrea TINYINT(1) NOT NULL DEFAULT 0,
  dias_diarrea SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  fiebre TINYINT(1) NOT NULL DEFAULT 0,
  fiebre_mas_7_dias TINYINT(1) NOT NULL DEFAULT 0,
  cianosis_central TINYINT(1) NOT NULL DEFAULT 0,
  ombligo_rojizo TINYINT(1) NOT NULL DEFAULT 0,
  ombligo_supurando TINYINT(1) NOT NULL DEFAULT 0,
  pustulas_extensas TINYINT(1) NOT NULL DEFAULT 0,
  pustulas_escasas TINYINT(1) NOT NULL DEFAULT 0,
  tiraje_subcostal TINYINT(1) NOT NULL DEFAULT 0,
  placas_blancas_bucales TINYINT(1) NOT NULL DEFAULT 0,
  hipotermia TINYINT(1) NOT NULL DEFAULT 0,
  se_ve_mal TINYINT(1) NOT NULL DEFAULT 0,
  supuracion_oido TINYINT(1) NOT NULL DEFAULT 0,
  supuracion_ojos TINYINT(1) NOT NULL DEFAULT 0,
  manifestacion_sangrado TINYINT(1) NOT NULL DEFAULT 0,
  distension_abdominal TINYINT(1) NOT NULL DEFAULT 0,
  apnea TINYINT(1) NOT NULL DEFAULT 0,
  quejido TINYINT(1) NOT NULL DEFAULT 0,
  aleteo_nasal TINYINT(1) NOT NULL DEFAULT 0,
  palidez_intensa TINYINT(1) NOT NULL DEFAULT 0,
  llenado_capilar_lento TINYINT(1) NOT NULL DEFAULT 0,
  fontanela_abombada TINYINT(1) NOT NULL DEFAULT 0,
  sangrado_heces TINYINT(1) NOT NULL DEFAULT 0,
  anormalmente_somnoliento TINYINT(1) NOT NULL DEFAULT 0,
  ojos_hundidos TINYINT(1) NOT NULL DEFAULT 0,
  inquieto_irritable TINYINT(1) NOT NULL DEFAULT 0,
  peso_g INT UNSIGNED DEFAULT NULL,
  talla_cm DECIMAL(5,2) DEFAULT NULL,
  perimetro_cefalico_cm DECIMAL(5,2) DEFAULT NULL,
  imc DECIMAL(5,2) DEFAULT NULL,
  peso_edad ENUM('normal','bajo','alto') DEFAULT NULL,
  talla_edad ENUM('normal','bajo','alto') DEFAULT NULL,
  peso_talla ENUM('normal','bajo','alto') DEFAULT NULL,
  edema_pies TINYINT(1) NOT NULL DEFAULT 0,
  emaciacion TINYINT(1) NOT NULL DEFAULT 0,
  malnutricion TINYINT(1) NOT NULL DEFAULT 0,
  lactancia_materna TINYINT(1) NOT NULL DEFAULT 0,
  lactancia_nocturna TINYINT(1) NOT NULL DEFAULT 0,
  lactancia_mas_8_veces TINYINT(1) NOT NULL DEFAULT 0,
  otros_liquidos TINYINT(1) NOT NULL DEFAULT 0,
  uso_biberon TINYINT(1) NOT NULL DEFAULT 0,
  problemas_posicion TINYINT(1) NOT NULL DEFAULT 0,
  problemas_agarre TINYINT(1) NOT NULL DEFAULT 0,
  problemas_succion TINYINT(1) NOT NULL DEFAULT 0,
  vacuna TINYINT(1) NOT NULL DEFAULT 0,
  vacuna_edad TINYINT(1) NOT NULL DEFAULT 0,
  vitamina_a TINYINT(1) NOT NULL DEFAULT 0,
  hierro TINYINT(1) NOT NULL DEFAULT 0,
  zinc TINYINT(1) NOT NULL DEFAULT 0,
  antiparasitario TINYINT(1) NOT NULL DEFAULT 0,
  buen_trato TINYINT(1) NOT NULL DEFAULT 0,
  relacion_afectivo ENUM('Madre','Padre','Cuidador') DEFAULT NULL,
  lesiones_fisicas TINYINT(1) NOT NULL DEFAULT 0,
  lesiones_genitales TINYINT(1) NOT NULL DEFAULT 0,
  lesiones_ano TINYINT(1) NOT NULL DEFAULT 0,
  comportamiento_alterado TINYINT(1) NOT NULL DEFAULT 0,
  comportamiento_cuidador_alterado TINYINT(1) NOT NULL DEFAULT 0,
  notas TEXT DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_seg_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_seg_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_seg_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_patient_date (patient_id, visit_date),
  INDEX idx_encounter (encounter_id),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        self::ensureTableExists($pdo, 'seguimiento_notas', <<<'SQL'
CREATE TABLE IF NOT EXISTS seguimiento_notas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  seguimiento_id INT UNSIGNED NOT NULL,
  tipo VARCHAR(50),
  contenido TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (seguimiento_id) REFERENCES seguimiento_integral_ninez_adolescencia(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    private static function ensureTableExists(PDO $pdo, string $table, string $createSql): void
    {
        try {
            $stmt = $pdo->prepare(
                'SELECT 1 FROM information_schema.tables WHERE table_schema = :schema AND table_name = :table LIMIT 1'
            );
            $stmt->execute([
                ':schema' => self::config()['DB_NAME'],
                ':table' => $table,
            ]);
            if ($stmt->fetch()) {
                return;
            }

            $pdo->exec($createSql);
        } catch (PDOException $e) {
            // Leave existing schema unchanged if we cannot patch it.
        }
    }

    private static function ensureColumnExists(PDO $pdo, string $table, string $column, string $definition): void
    {
        try {
            $stmt = $pdo->prepare(
                'SELECT 1 FROM information_schema.columns WHERE table_schema = :schema AND table_name = :table AND column_name = :column LIMIT 1'
            );
            $stmt->execute([
                ':schema' => self::config()['DB_NAME'],
                ':table' => $table,
                ':column' => $column,
            ]);
            if ($stmt->fetch()) {
                return;
            }

            $pdo->exec(sprintf('ALTER TABLE `%s` ADD COLUMN `%s` %s', $table, $column, $definition));
        } catch (PDOException $e) {
            // Leave existing schema unchanged if we cannot patch it.
        }
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
