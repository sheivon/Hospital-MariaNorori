-- Migration: create tests (patient test results) table
CREATE TABLE IF NOT EXISTS `tests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `patient_id` INT UNSIGNED NOT NULL,
  `test_type` VARCHAR(191) NOT NULL,
  `result` TEXT,
  `test_date` DATETIME DEFAULT NULL,
  `notes` TEXT,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`patient_id`),
  INDEX (`created_by`),
  CONSTRAINT `tests_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
