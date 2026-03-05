<?php
require_once __DIR__ . '/../config/db.php';

echo "Creating tests table...\n";
$sql = <<<SQL
CREATE TABLE IF NOT EXISTS tests (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    patient_id INT UNSIGNED NOT NULL,
    encounter_id INT UNSIGNED DEFAULT NULL,
    diagnostic_id INT UNSIGNED DEFAULT NULL,
    test_type VARCHAR(191) NOT NULL,
    result TEXT,
    test_date DATETIME DEFAULT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    reference_range VARCHAR(120) DEFAULT NULL,
    notes TEXT,
    created_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_tests_patient (patient_id),
    INDEX idx_tests_encounter (encounter_id),
    INDEX idx_tests_created_by (created_by),
    INDEX idx_tests_deleted_at (deleted_at),
    CONSTRAINT fk_tests_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    CONSTRAINT fk_tests_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
    CONSTRAINT fk_tests_diagnostic FOREIGN KEY (diagnostic_id) REFERENCES diagnostics(id) ON DELETE SET NULL,
    CONSTRAINT fk_tests_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

try{
    $pdo->exec($sql);
    echo "Done.\n";
}catch(PDOException $e){
    echo "Failed: " . $e->getMessage() . "\n";
}
