CREATE DATABASE IF NOT EXISTS hospital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hospital;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS bed_movements;
DROP TABLE IF EXISTS admissions;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS immunizations;
DROP TABLE IF EXISTS treatment_administration;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS medications_catalog;
DROP TABLE IF EXISTS clinical_procedures;
DROP TABLE IF EXISTS treatment_plans;
DROP TABLE IF EXISTS clinical_notes;
DROP TABLE IF EXISTS vitals;
DROP TABLE IF EXISTS tests;
DROP TABLE IF EXISTS diagnostics;
DROP TABLE IF EXISTS patient_allergies;
DROP TABLE IF EXISTS patient_conditions;
DROP TABLE IF EXISTS encounters;
DROP TABLE IF EXISTS patient_contacts;
DROP TABLE IF EXISTS chat_messages;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  fullname VARCHAR(255) DEFAULT NULL,
  cedula VARCHAR(50) DEFAULT NULL UNIQUE,
  role VARCHAR(50) NOT NULL DEFAULT 'user',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_roles (
  role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role VARCHAR(50) NOT NULL UNIQUE,
  accesstype VARCHAR(50) NOT NULL,
  INDEX idx_user_roles_access (accesstype)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO user_roles (role, accesstype) VALUES
  ('admin', 'full'),
  ('doctor', 'clinical'),
  ('user', 'basic');

DROP TRIGGER IF EXISTS trg_user_roles_no_insert;
DROP TRIGGER IF EXISTS trg_user_roles_no_update;
DROP TRIGGER IF EXISTS trg_user_roles_no_delete;

CREATE TRIGGER trg_user_roles_no_insert
BEFORE INSERT ON user_roles
FOR EACH ROW
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'user_roles is read-only';

CREATE TRIGGER trg_user_roles_no_update
BEFORE UPDATE ON user_roles
FOR EACH ROW
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'user_roles is read-only';

CREATE TRIGGER trg_user_roles_no_delete
BEFORE DELETE ON user_roles
FOR EACH ROW
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'user_roles is read-only';

CREATE TABLE patients (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  cedula VARCHAR(50) DEFAULT NULL UNIQUE,
  email VARCHAR(255) DEFAULT NULL,
  dob DATE DEFAULT NULL,
  gender ENUM('M','F','O') DEFAULT 'O',
  marital_status VARCHAR(50) DEFAULT NULL,
  blood_type VARCHAR(5) DEFAULT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  emergency_phone VARCHAR(50) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  occupation VARCHAR(120) DEFAULT NULL,
  insurance_provider VARCHAR(120) DEFAULT NULL,
  insurance_policy_no VARCHAR(120) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  is_deceased TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_patients_name (last_name, first_name),
  INDEX idx_patients_dob (dob)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE patient_contacts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  contact_name VARCHAR(150) NOT NULL,
  relationship VARCHAR(80) DEFAULT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  email VARCHAR(255) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_patient_contacts_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  INDEX idx_patient_contacts_patient (patient_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE encounters (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  encounter_type VARCHAR(50) NOT NULL DEFAULT 'outpatient',
  reason_for_visit VARCHAR(255) DEFAULT NULL,
  triage_level VARCHAR(20) DEFAULT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'open',
  attending_user_id INT UNSIGNED DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_encounters_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_encounters_attending FOREIGN KEY (attending_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_encounters_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_encounters_patient (patient_id),
  INDEX idx_encounters_date (encounter_date),
  INDEX idx_encounters_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE patient_conditions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  icd10_code VARCHAR(20) DEFAULT NULL,
  condition_name VARCHAR(200) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'active',
  onset_date DATE DEFAULT NULL,
  resolved_date DATE DEFAULT NULL,
  severity VARCHAR(30) DEFAULT NULL,
  clinical_notes TEXT DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_patient_conditions_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_patient_conditions_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_patient_conditions_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_patient_conditions_patient (patient_id),
  INDEX idx_patient_conditions_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE patient_allergies (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  allergen VARCHAR(200) NOT NULL,
  reaction VARCHAR(200) DEFAULT NULL,
  severity VARCHAR(30) DEFAULT NULL,
  noted_date DATE DEFAULT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'active',
  notes TEXT DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_patient_allergies_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_patient_allergies_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_patient_allergies_patient (patient_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE diagnostics (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  type VARCHAR(100) NOT NULL,
  icd10_code VARCHAR(20) DEFAULT NULL,
  description TEXT,
  status VARCHAR(30) NOT NULL DEFAULT 'active',
  severity VARCHAR(30) DEFAULT NULL,
  date DATE DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_diagnostics_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_diagnostics_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_diagnostics_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_diagnostics_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_diagnostics_patient (patient_id),
  INDEX idx_diagnostics_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tests (
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
  PRIMARY KEY (id),
  INDEX idx_tests_patient (patient_id),
  INDEX idx_tests_encounter (encounter_id),
  INDEX idx_tests_created_by (created_by),
  CONSTRAINT fk_tests_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_tests_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_tests_diagnostic FOREIGN KEY (diagnostic_id) REFERENCES diagnostics(id) ON DELETE SET NULL,
  CONSTRAINT fk_tests_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vitals (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  measured_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  temperature_c DECIMAL(4,1) DEFAULT NULL,
  systolic_bp SMALLINT UNSIGNED DEFAULT NULL,
  diastolic_bp SMALLINT UNSIGNED DEFAULT NULL,
  heart_rate SMALLINT UNSIGNED DEFAULT NULL,
  respiratory_rate SMALLINT UNSIGNED DEFAULT NULL,
  oxygen_saturation DECIMAL(5,2) DEFAULT NULL,
  weight_kg DECIMAL(6,2) DEFAULT NULL,
  height_cm DECIMAL(6,2) DEFAULT NULL,
  bmi DECIMAL(5,2) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_vitals_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_vitals_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_vitals_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_vitals_patient (patient_id),
  INDEX idx_vitals_measured_at (measured_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE clinical_notes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  note_type VARCHAR(50) NOT NULL DEFAULT 'progress',
  note_text TEXT NOT NULL,
  is_confidential TINYINT(1) NOT NULL DEFAULT 0,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_clinical_notes_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_clinical_notes_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_clinical_notes_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_clinical_notes_patient (patient_id),
  INDEX idx_clinical_notes_encounter (encounter_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE treatment_plans (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  diagnostic_id INT UNSIGNED DEFAULT NULL,
  goal VARCHAR(255) DEFAULT NULL,
  treatment_description TEXT NOT NULL,
  start_date DATE DEFAULT NULL,
  end_date DATE DEFAULT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'active',
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_treatment_plans_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_treatment_plans_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_treatment_plans_diagnostic FOREIGN KEY (diagnostic_id) REFERENCES diagnostics(id) ON DELETE SET NULL,
  CONSTRAINT fk_treatment_plans_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_treatment_plans_patient (patient_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE clinical_procedures (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  procedure_name VARCHAR(200) NOT NULL,
  procedure_code VARCHAR(40) DEFAULT NULL,
  procedure_date DATETIME DEFAULT NULL,
  outcome TEXT DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  performed_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_clinical_procedures_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_clinical_procedures_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_clinical_procedures_user FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_clinical_procedures_patient (patient_id),
  INDEX idx_clinical_procedures_date (procedure_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE medications_catalog (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  medication_name VARCHAR(200) NOT NULL,
  generic_name VARCHAR(200) DEFAULT NULL,
  form VARCHAR(100) DEFAULT NULL,
  strength VARCHAR(100) DEFAULT NULL,
  UNIQUE KEY uq_medications_catalog_name (medication_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE prescriptions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  medication_id INT UNSIGNED DEFAULT NULL,
  medication_name VARCHAR(200) NOT NULL,
  dose VARCHAR(100) DEFAULT NULL,
  frequency VARCHAR(100) DEFAULT NULL,
  route VARCHAR(100) DEFAULT NULL,
  duration_days INT DEFAULT NULL,
  start_date DATE DEFAULT NULL,
  end_date DATE DEFAULT NULL,
  instructions TEXT DEFAULT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'active',
  prescribed_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_prescriptions_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_prescriptions_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_prescriptions_med FOREIGN KEY (medication_id) REFERENCES medications_catalog(id) ON DELETE SET NULL,
  CONSTRAINT fk_prescriptions_user FOREIGN KEY (prescribed_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_prescriptions_patient (patient_id),
  INDEX idx_prescriptions_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE treatment_administration (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  prescription_id INT UNSIGNED NOT NULL,
  administered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  administered_dose VARCHAR(100) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  administered_by INT UNSIGNED DEFAULT NULL,
  CONSTRAINT fk_treatment_admin_prescription FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE,
  CONSTRAINT fk_treatment_admin_user FOREIGN KEY (administered_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_treatment_admin_prescription (prescription_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE immunizations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  vaccine_name VARCHAR(200) NOT NULL,
  dose_number VARCHAR(30) DEFAULT NULL,
  administered_date DATE DEFAULT NULL,
  next_due_date DATE DEFAULT NULL,
  lot_number VARCHAR(80) DEFAULT NULL,
  administered_by INT UNSIGNED DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_immunizations_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_immunizations_user FOREIGN KEY (administered_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_immunizations_patient (patient_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE appointments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  provider_user_id INT UNSIGNED DEFAULT NULL,
  appointment_at DATETIME NOT NULL,
  reason VARCHAR(255) DEFAULT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'scheduled',
  notes TEXT DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_appointments_provider FOREIGN KEY (provider_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_appointments_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_appointments_patient (patient_id),
  INDEX idx_appointments_at (appointment_at),
  INDEX idx_appointments_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE admissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  admitted_at DATETIME NOT NULL,
  discharged_at DATETIME DEFAULT NULL,
  department VARCHAR(120) DEFAULT NULL,
  room VARCHAR(50) DEFAULT NULL,
  bed VARCHAR(50) DEFAULT NULL,
  admission_reason VARCHAR(255) DEFAULT NULL,
  discharge_summary TEXT DEFAULT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'admitted',
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_admissions_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_admissions_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_admissions_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_admissions_patient (patient_id),
  INDEX idx_admissions_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bed_movements (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  admission_id INT UNSIGNED NOT NULL,
  moved_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  from_room VARCHAR(50) DEFAULT NULL,
  from_bed VARCHAR(50) DEFAULT NULL,
  to_room VARCHAR(50) DEFAULT NULL,
  to_bed VARCHAR(50) DEFAULT NULL,
  reason VARCHAR(255) DEFAULT NULL,
  moved_by INT UNSIGNED DEFAULT NULL,
  CONSTRAINT fk_bed_movements_admission FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE CASCADE,
  CONSTRAINT fk_bed_movements_user FOREIGN KEY (moved_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_bed_movements_admission (admission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chat_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  username VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  recipient_id INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_chat_messages_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_chat_messages_recipient FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_chat_messages_created_at (created_at),
  INDEX idx_chat_messages_recipient (recipient_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  actor_user_id INT UNSIGNED DEFAULT NULL,
  entity_name VARCHAR(100) NOT NULL,
  entity_id VARCHAR(100) DEFAULT NULL,
  action VARCHAR(40) NOT NULL,
  details JSON DEFAULT NULL,
  ip_address VARCHAR(64) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_logs_actor FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_audit_logs_entity (entity_name, entity_id),
  INDEX idx_audit_logs_action (action),
  INDEX idx_audit_logs_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE users ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_users_deleted_at (deleted_at);
ALTER TABLE patients ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_patients_deleted_at (deleted_at);
ALTER TABLE patient_contacts ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_patient_contacts_deleted_at (deleted_at);
ALTER TABLE encounters ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_encounters_deleted_at (deleted_at);
ALTER TABLE patient_conditions ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_patient_conditions_deleted_at (deleted_at);
ALTER TABLE patient_allergies ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_patient_allergies_deleted_at (deleted_at);
ALTER TABLE diagnostics ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_diagnostics_deleted_at (deleted_at);
ALTER TABLE tests ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_tests_deleted_at (deleted_at);
ALTER TABLE vitals ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_vitals_deleted_at (deleted_at);
ALTER TABLE clinical_notes ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_clinical_notes_deleted_at (deleted_at);
ALTER TABLE treatment_plans ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_treatment_plans_deleted_at (deleted_at);
ALTER TABLE clinical_procedures ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_clinical_procedures_deleted_at (deleted_at);
ALTER TABLE medications_catalog ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_medications_catalog_deleted_at (deleted_at);
ALTER TABLE prescriptions ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_prescriptions_deleted_at (deleted_at);
ALTER TABLE treatment_administration ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_treatment_administration_deleted_at (deleted_at);
ALTER TABLE immunizations ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_immunizations_deleted_at (deleted_at);
ALTER TABLE appointments ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_appointments_deleted_at (deleted_at);
ALTER TABLE admissions ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_admissions_deleted_at (deleted_at);
ALTER TABLE bed_movements ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_bed_movements_deleted_at (deleted_at);
ALTER TABLE chat_messages ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_chat_messages_deleted_at (deleted_at);
ALTER TABLE audit_logs ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL, ADD INDEX idx_audit_logs_deleted_at (deleted_at);
