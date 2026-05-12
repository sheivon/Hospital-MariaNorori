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
DROP TABLE IF EXISTS adolescent_clinical_histories;
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
  specialty VARCHAR(120) DEFAULT NULL,
  department VARCHAR(120) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  deleted_at DATETIME NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role (role),
  INDEX idx_users_deleted_at (deleted_at)
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
  father_name VARCHAR(150) DEFAULT NULL,
  mother_name VARCHAR(150) DEFAULT NULL,
  expediente_no VARCHAR(100) DEFAULT NULL,
  procedencia VARCHAR(255) DEFAULT NULL,
  education_level VARCHAR(100) DEFAULT NULL,
  employer VARCHAR(255) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  is_deceased TINYINT(1) NOT NULL DEFAULT 0,
  deleted_at DATETIME NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  encountered TINYINT(1) NOT NULL DEFAULT 0,
  INDEX idx_patients_name (last_name, first_name),
  INDEX idx_patients_dob (dob),
  INDEX idx_patients_deleted_at (deleted_at),
  INDEX idx_patients_encountered (encountered)
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
  unit VARCHAR(120) DEFAULT NULL,
  room VARCHAR(80) DEFAULT NULL,
  icd10_code VARCHAR(20) DEFAULT NULL,
  description TEXT,
  status VARCHAR(30) NOT NULL DEFAULT 'active',
  severity VARCHAR(30) DEFAULT NULL,
  date DATE DEFAULT NULL,
  time TIME DEFAULT NULL,
  plan TEXT DEFAULT NULL,
  weight DECIMAL(6,2) DEFAULT NULL,
  height DECIMAL(6,2) DEFAULT NULL,
  age INT DEFAULT NULL,
  sex ENUM('M','F','O') DEFAULT NULL,
  expediente_no VARCHAR(100) DEFAULT NULL,
  cedula VARCHAR(50) DEFAULT NULL,
  inss_no VARCHAR(100) DEFAULT NULL,
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
  location VARCHAR(255) DEFAULT NULL,
  code VARCHAR(100) DEFAULT NULL,
  bed VARCHAR(50) DEFAULT NULL,
  service VARCHAR(120) DEFAULT NULL,
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

CREATE TABLE exam_requests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  patient_id INT UNSIGNED NOT NULL,
  request_date DATE NOT NULL,
  exam_type VARCHAR(255) NOT NULL,
  notes TEXT DEFAULT NULL,
  result TEXT DEFAULT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'pending',
  deleted_at DATETIME NULL DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_exam_requests_patient (patient_id),
  INDEX idx_exam_requests_created_by (created_by),
  CONSTRAINT fk_exam_requests_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_exam_requests_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE radiology_requests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  patient_id INT UNSIGNED NOT NULL,
  unit VARCHAR(120) DEFAULT NULL,
  first_last_name VARCHAR(100) DEFAULT NULL,
  second_last_name VARCHAR(100) DEFAULT NULL,
  names VARCHAR(200) DEFAULT NULL,
  insured VARCHAR(10) DEFAULT NULL,
  gender VARCHAR(10) DEFAULT NULL,
  age VARCHAR(20) DEFAULT NULL,
  request_date DATE DEFAULT NULL,
  clinic_bed VARCHAR(100) DEFAULT NULL,
  service VARCHAR(100) DEFAULT NULL,
  code VARCHAR(50) DEFAULT NULL,
  prior_radiograph VARCHAR(50) DEFAULT NULL,
  prior_radiograph_code VARCHAR(50) DEFAULT NULL,
  exam_requested TEXT DEFAULT NULL,
  clinical_data TEXT DEFAULT NULL,
  evolution_time VARCHAR(100) DEFAULT NULL,
  presumptive_diagnosis TEXT DEFAULT NULL,
  observations TEXT DEFAULT NULL,
  doctor_code VARCHAR(100) DEFAULT NULL,
  technician VARCHAR(100) DEFAULT NULL,
  plates_used VARCHAR(50) DEFAULT NULL,
  findings TEXT DEFAULT NULL,
  conclusions TEXT DEFAULT NULL,
  radiology_date DATE DEFAULT NULL,
  radiographs_archived VARCHAR(100) DEFAULT NULL,
  radiograph_count VARCHAR(50) DEFAULT NULL,
  dictating_doctor_code VARCHAR(100) DEFAULT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'pending',
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_radiology_requests_patient (patient_id),
  INDEX idx_radiology_requests_created_by (created_by),
  CONSTRAINT fk_radiology_requests_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_radiology_requests_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
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

CREATE TABLE adolescent_clinical_histories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  visit_date DATE DEFAULT NULL,
  reason_for_consultation TEXT DEFAULT NULL,
  personal_pathological_history TEXT DEFAULT NULL,
  risk_factors TEXT DEFAULT NULL,
  family_pathological_history TEXT DEFAULT NULL,
  family_environment TEXT DEFAULT NULL,
  education_work_living TEXT DEFAULT NULL,
  activities_social TEXT DEFAULT NULL,
  physical_activity TEXT DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  form_data TEXT DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_adolescent_history_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_adolescent_history_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_adolescent_history_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_adolescent_history_patient (patient_id),
  INDEX idx_adolescent_history_visit_date (visit_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE treatment_plans (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  diagnostic_id INT UNSIGNED DEFAULT NULL,
  goal VARCHAR(255) DEFAULT NULL,
  treatment_description TEXT NOT NULL,
  medications TEXT DEFAULT NULL,
  exams TEXT DEFAULT NULL,
  diet TEXT DEFAULT NULL,
  rules TEXT DEFAULT NULL,
  follow_up TEXT DEFAULT NULL,
  transfer TEXT DEFAULT NULL,
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
  encounter_id INT UNSIGNED DEFAULT NULL,
  provider_user_id INT UNSIGNED DEFAULT NULL,
  appointment_at DATETIME NOT NULL,
  reason VARCHAR(255) DEFAULT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'scheduled',
  notes TEXT DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_appointments_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_appointments_provider FOREIGN KEY (provider_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_appointments_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_appointments_patient (patient_id),
  INDEX idx_appointments_encounter (encounter_id),
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

CREATE TABLE emergency_encounters (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  admission_date DATE NOT NULL,
  discharge_date DATE DEFAULT NULL,
  status VARCHAR(50) DEFAULT 'Activo',
  form_data TEXT DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_emergency_encounters_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_emergency_encounters_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE SET NULL,
  CONSTRAINT fk_emergency_encounters_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_emergency_encounters_patient (patient_id),
  INDEX idx_emergency_encounters_status (status)
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

CREATE TABLE encounter_doctors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  encounter_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  role VARCHAR(80) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_encounter_doctors_encounter FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE CASCADE,
  CONSTRAINT fk_encounter_doctors_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_encounter_doctors_encounter (encounter_id),
  INDEX idx_encounter_doctors_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE users ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'idx_users_deleted_at') = 0,
    'CREATE INDEX idx_users_deleted_at ON users (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patients' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE patients ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patients' AND COLUMN_NAME = 'encountered') = 0,
    'ALTER TABLE patients ADD COLUMN encountered TINYINT(1) NOT NULL DEFAULT 0',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patients' AND INDEX_NAME = 'idx_patients_deleted_at') = 0,
    'CREATE INDEX idx_patients_deleted_at ON patients (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patients' AND INDEX_NAME = 'idx_patients_encountered') = 0,
    'CREATE INDEX idx_patients_encountered ON patients (encountered)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patient_contacts' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE patient_contacts ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patient_contacts' AND INDEX_NAME = 'idx_patient_contacts_deleted_at') = 0,
    'CREATE INDEX idx_patient_contacts_deleted_at ON patient_contacts (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'encounters' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE encounters ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'encounters' AND INDEX_NAME = 'idx_encounters_deleted_at') = 0,
    'CREATE INDEX idx_encounters_deleted_at ON encounters (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patient_conditions' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE patient_conditions ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patient_conditions' AND INDEX_NAME = 'idx_patient_conditions_deleted_at') = 0,
    'CREATE INDEX idx_patient_conditions_deleted_at ON patient_conditions (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patient_allergies' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE patient_allergies ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'patient_allergies' AND INDEX_NAME = 'idx_patient_allergies_deleted_at') = 0,
    'CREATE INDEX idx_patient_allergies_deleted_at ON patient_allergies (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'diagnostics' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE diagnostics ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'diagnostics' AND INDEX_NAME = 'idx_diagnostics_deleted_at') = 0,
    'CREATE INDEX idx_diagnostics_deleted_at ON diagnostics (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tests' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE tests ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tests' AND INDEX_NAME = 'idx_tests_deleted_at') = 0,
    'CREATE INDEX idx_tests_deleted_at ON tests (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vitals' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE vitals ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vitals' AND INDEX_NAME = 'idx_vitals_deleted_at') = 0,
    'CREATE INDEX idx_vitals_deleted_at ON vitals (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clinical_notes' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE clinical_notes ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clinical_notes' AND INDEX_NAME = 'idx_clinical_notes_deleted_at') = 0,
    'CREATE INDEX idx_clinical_notes_deleted_at ON clinical_notes (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'treatment_plans' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE treatment_plans ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'treatment_plans' AND INDEX_NAME = 'idx_treatment_plans_deleted_at') = 0,
    'CREATE INDEX idx_treatment_plans_deleted_at ON treatment_plans (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clinical_procedures' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE clinical_procedures ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clinical_procedures' AND INDEX_NAME = 'idx_clinical_procedures_deleted_at') = 0,
    'CREATE INDEX idx_clinical_procedures_deleted_at ON clinical_procedures (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'medications_catalog' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE medications_catalog ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'medications_catalog' AND INDEX_NAME = 'idx_medications_catalog_deleted_at') = 0,
    'CREATE INDEX idx_medications_catalog_deleted_at ON medications_catalog (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'prescriptions' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE prescriptions ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'prescriptions' AND INDEX_NAME = 'idx_prescriptions_deleted_at') = 0,
    'CREATE INDEX idx_prescriptions_deleted_at ON prescriptions (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'treatment_administration' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE treatment_administration ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'treatment_administration' AND INDEX_NAME = 'idx_treatment_administration_deleted_at') = 0,
    'CREATE INDEX idx_treatment_administration_deleted_at ON treatment_administration (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'immunizations' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE immunizations ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'immunizations' AND INDEX_NAME = 'idx_immunizations_deleted_at') = 0,
    'CREATE INDEX idx_immunizations_deleted_at ON immunizations (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'appointments' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE appointments ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'appointments' AND INDEX_NAME = 'idx_appointments_deleted_at') = 0,
    'CREATE INDEX idx_appointments_deleted_at ON appointments (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admissions' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE admissions ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admissions' AND INDEX_NAME = 'idx_admissions_deleted_at') = 0,
    'CREATE INDEX idx_admissions_deleted_at ON admissions (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bed_movements' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE bed_movements ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bed_movements' AND INDEX_NAME = 'idx_bed_movements_deleted_at') = 0,
    'CREATE INDEX idx_bed_movements_deleted_at ON bed_movements (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chat_messages' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE chat_messages ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chat_messages' AND INDEX_NAME = 'idx_chat_messages_deleted_at') = 0,
    'CREATE INDEX idx_chat_messages_deleted_at ON chat_messages (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_logs' AND COLUMN_NAME = 'deleted_at') = 0,
    'ALTER TABLE audit_logs ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_logs' AND INDEX_NAME = 'idx_audit_logs_deleted_at') = 0,
    'CREATE INDEX idx_audit_logs_deleted_at ON audit_logs (deleted_at)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;




// Add more tables and indexes as needed for future features
// preliminary structure for future modules like billing, inventory, staff scheduling, etc.

//Table for Seguimiento_Integral_ala_niñez_y_adolescencia
CREATE TABLE seguimiento_integral_ninez_adolescencia (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  visit_date DATE NOT NULL,

  -- signos principales
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

  -- nutricion y crecimiento
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

  -- alimentacion
  lactancia_materna TINYINT(1) NOT NULL DEFAULT 0,
  lactancia_nocturna TINYINT(1) NOT NULL DEFAULT 0,
  lactancia_mas_8_veces TINYINT(1) NOT NULL DEFAULT 0,
  otros_liquidos TINYINT(1) NOT NULL DEFAULT 0,
  uso_biberon TINYINT(1) NOT NULL DEFAULT 0,

  problemas_posicion TINYINT(1) NOT NULL DEFAULT 0,
  problemas_agarre TINYINT(1) NOT NULL DEFAULT 0,
  problemas_succion TINYINT(1) NOT NULL DEFAULT 0,

  -- vacunas y suplementos
  vacuna TINYINT(1) NOT NULL DEFAULT 0,
  vacuna_edad TINYINT(1) NOT NULL DEFAULT 0,
  vitamina_a TINYINT(1) NOT NULL DEFAULT 0,
  hierro TINYINT(1) NOT NULL DEFAULT 0,
  zinc TINYINT(1) NOT NULL DEFAULT 0,
  antiparasitario TINYINT(1) NOT NULL DEFAULT 0,

  -- entorno familiar
  buen_trato TINYINT(1) NOT NULL DEFAULT 0,
  relacion_afectivo ENUM('Madre','Padre','Cuidador') DEFAULT NULL,

  lesiones_fisicas TINYINT(1) NOT NULL DEFAULT 0,
  lesiones_genitales TINYINT(1) NOT NULL DEFAULT 0,
  lesiones_ano TINYINT(1) NOT NULL DEFAULT 0,

  comportamiento_alterado TINYINT(1) NOT NULL DEFAULT 0,
  comportamiento_cuidador_alterado TINYINT(1) NOT NULL DEFAULT 0,

  -- notas (texto separado conceptualmente)
  notas TEXT DEFAULT NULL,

  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  -- foreign keys
  CONSTRAINT fk_seg_patient FOREIGN KEY (patient_id)
    REFERENCES patients(id) ON DELETE CASCADE,

  CONSTRAINT fk_seg_encounter FOREIGN KEY (encounter_id)
    REFERENCES encounters(id) ON DELETE SET NULL,

  CONSTRAINT fk_seg_user FOREIGN KEY (created_by)
    REFERENCES users(id) ON DELETE SET NULL,

  -- indexes (IMPORTANT for performance)
  INDEX idx_patient_date (patient_id, visit_date),
  INDEX idx_encounter (encounter_id),
  INDEX idx_created_at (created_at)

) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

CREATE TABLE seguimiento_notas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  seguimiento_id INT UNSIGNED NOT NULL,
  tipo VARCHAR(50),
  contenido TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (seguimiento_id) REFERENCES seguimiento_integral_ninez_adolescencia(id) ON DELETE CASCADE
);










