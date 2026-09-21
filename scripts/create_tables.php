<?php
/**
 * scripts/create_tables.php
 *
 * Initializes the Hospital database using the canonical SQL schema.
 *
 * IMPORTANT:
 * - This script does NOT contain a second copy of the database schema.
 * - The source of truth is: database/hospital_schema.sql
 * - Use migrations separately for changes to an existing production database.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

try {
    /*
     * ---------------------------------------------------------
     * DATABASE CONNECTION
     * ---------------------------------------------------------
     */

    $pdo = new PDO(
        $dsn,
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    echo "Connected to MySQL successfully.\n";


    /*
     * ---------------------------------------------------------
     * LOAD CANONICAL SCHEMA
     * ---------------------------------------------------------
     */

    $schemaFile = __DIR__ . '/../database/hospital_schema.sql';

    if (!file_exists($schemaFile)) {
        throw new RuntimeException(
            "Schema file not found: {$schemaFile}"
        );
    }

    if (!is_readable($schemaFile)) {
        throw new RuntimeException(
            "Schema file is not readable: {$schemaFile}"
        );
    }

    $sql = file_get_contents($schemaFile);

    if ($sql === false || trim($sql) === '') {
        throw new RuntimeException(
            "Schema file is empty or could not be read."
        );
    }


    /*
     * ---------------------------------------------------------
     * EXECUTE SCHEMA
     * ---------------------------------------------------------
     *
     * PDO::exec() sends the SQL to MySQL.
     *
     * Your schema contains multiple statements, so this works
     * with MySQL when multi-statements are enabled by the driver.
     */

    echo "Executing hospital database schema...\n";

    $pdo->exec($sql);


    /*
     * ---------------------------------------------------------
     * VERIFY DATABASE
     * ---------------------------------------------------------
     */

    $databaseName = $pdo->query("SELECT DATABASE()")->fetchColumn();

    echo "Database: {$databaseName}\n";


    /*
     * ---------------------------------------------------------
     * CHECK IMPORTANT TABLES
     * ---------------------------------------------------------
     */

    $requiredTables = [
        'users',
        'user_roles',
        'patients',
        'encounters',
        'patient_conditions',
        'patient_allergies',
        'diagnostics',
        'exam_types',
        'exam_requests',
        'vitals',
        'clinical_notes',
        'treatment_plans',
        'clinical_procedures',
        'medications_catalog',
        'prescriptions',
        'treatment_administration',
        'immunizations',
        'appointments',
        'chat_messages',
        'audit_logs',
        'encounter_doctors',
        'seguimiento_notas',
    ];

    $missingTables = [];

    $checkTable = $pdo->prepare(
        "SELECT COUNT(*)
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = ?"
    );

    foreach ($requiredTables as $table) {
        $checkTable->execute([$table]);

        if ((int) $checkTable->fetchColumn() === 0) {
            $missingTables[] = $table;
        }
    }


    /*
     * ---------------------------------------------------------
     * RESULT
     * ---------------------------------------------------------
     */

    if (!empty($missingTables)) {
        echo "\nWARNING: Some expected tables are missing:\n";

        foreach ($missingTables as $table) {
            echo "  - {$table}\n";
        }

        echo "\nDatabase initialization finished with warnings.\n";
        exit(1);
    }

    echo "\nAll required tables are present.\n";
    echo "Hospital database initialized successfully.\n";

} catch (PDOException $e) {

    echo "\nDATABASE ERROR:\n";
    echo $e->getMessage() . "\n";

    exit(1);

} catch (Throwable $e) {

    echo "\nERROR:\n";
    echo $e->getMessage() . "\n";

    exit(1);
}