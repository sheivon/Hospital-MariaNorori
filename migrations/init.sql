CREATE DATABASE IF NOT EXISTS hospital
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE hospital;

SET FOREIGN_KEY_CHECKS = 0;

-- =========================================================
-- ELIMINAR TABLAS EXISTENTES
-- =========================================================

DROP TABLE IF EXISTS seguimiento_notas;
DROP TABLE IF EXISTS seguimiento_integral_ninez_adolescencia;
DROP TABLE IF EXISTS encounter_doctors;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS bed_movements;
DROP TABLE IF EXISTS admissions;
DROP TABLE IF EXISTS patient_contacts;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS emergency_encounters;
DROP TABLE IF EXISTS immunizations;
DROP TABLE IF EXISTS treatment_administration;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS medications_catalog;
DROP TABLE IF EXISTS clinical_procedures;
DROP TABLE IF EXISTS treatment_plans;
DROP TABLE IF EXISTS clinical_notes;
DROP TABLE IF EXISTS pediatric_clinical_history;
DROP TABLE IF EXISTS exam_requests;
DROP TABLE IF EXISTS exam_types;
DROP TABLE IF EXISTS tests;
DROP TABLE IF EXISTS vitals;
DROP TABLE IF EXISTS diagnostics;
DROP TABLE IF EXISTS patient_allergies;
DROP TABLE IF EXISTS patient_conditions;
DROP TABLE IF EXISTS encounters;
DROP TABLE IF EXISTS chat_messages;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;


-- =========================================================
-- USUARIOS
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_users_role (role),
  INDEX idx_users_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- ROLES DE USUARIO
-- Esta tabla es de solo lectura desde la aplicación.
-- =========================================================

CREATE TABLE user_roles (
  role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role VARCHAR(50) NOT NULL UNIQUE,
  accesstype VARCHAR(50) NOT NULL,

  INDEX idx_user_roles_access (accesstype)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


INSERT INTO user_roles (role, accesstype) VALUES
  ('admin', 'full'),
  ('doctor', 'clinical'),
  ('user', 'basic');


-- Every user role must exist in the read-only role catalog.
ALTER TABLE users
  ADD CONSTRAINT fk_users_role
  FOREIGN KEY (role) REFERENCES user_roles (role)
  ON UPDATE CASCADE
  ON DELETE RESTRICT;


DROP TRIGGER IF EXISTS trg_user_roles_no_insert;
DROP TRIGGER IF EXISTS trg_user_roles_no_update;
DROP TRIGGER IF EXISTS trg_user_roles_no_delete;


CREATE TRIGGER trg_user_roles_no_insert
BEFORE INSERT ON user_roles
FOR EACH ROW
SIGNAL SQLSTATE '45000'
SET MESSAGE_TEXT = 'user_roles is read-only';


CREATE TRIGGER trg_user_roles_no_update
BEFORE UPDATE ON user_roles
FOR EACH ROW
SIGNAL SQLSTATE '45000'
SET MESSAGE_TEXT = 'user_roles is read-only';


CREATE TRIGGER trg_user_roles_no_delete
BEFORE DELETE ON user_roles
FOR EACH ROW
SIGNAL SQLSTATE '45000'
SET MESSAGE_TEXT = 'user_roles is read-only';


-- =========================================================
-- PACIENTES
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_patients_name (last_name, first_name),
  INDEX idx_patients_dob (dob),
  INDEX idx_patients_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- ENCUENTROS / CONSULTAS
-- =========================================================

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

  -- Campos utilizados principalmente para emergencias
  admission_date DATE DEFAULT NULL,
  discharge_date DATE DEFAULT NULL,
  emergency_status VARCHAR(50) DEFAULT NULL,
  form_data TEXT DEFAULT NULL,

  created_by INT UNSIGNED DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_encounters_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_encounters_attending
    FOREIGN KEY (attending_user_id)
    REFERENCES users(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_encounters_created_by
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_encounters_patient (patient_id),
  INDEX idx_encounters_date (encounter_date),
  INDEX idx_encounters_status (status),
  INDEX idx_encounters_type (encounter_type),
  INDEX idx_encounters_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- CONDICIONES DEL PACIENTE
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_patient_conditions_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_patient_conditions_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_patient_conditions_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_patient_conditions_patient (patient_id),
  INDEX idx_patient_conditions_status (status),
  INDEX idx_patient_conditions_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- ALERGIAS DEL PACIENTE
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_patient_allergies_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_patient_allergies_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_patient_allergies_patient (patient_id),
  INDEX idx_patient_allergies_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- DIAGNÓSTICOS
-- =========================================================

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
  inss_no VARCHAR(100) DEFAULT NULL,

  created_by INT UNSIGNED DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_diagnostics_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_diagnostics_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_diagnostics_created_by
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_diagnostics_updated_by
    FOREIGN KEY (updated_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_diagnostics_patient (patient_id),
  INDEX idx_diagnostics_date (date),
  INDEX idx_diagnostics_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- EXÁMENES / PRUEBAS
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),

  INDEX idx_tests_patient (patient_id),
  INDEX idx_tests_encounter (encounter_id),
  INDEX idx_tests_created_by (created_by),
  INDEX idx_tests_deleted_at (deleted_at),

  CONSTRAINT fk_tests_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_tests_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_tests_diagnostic
    FOREIGN KEY (diagnostic_id)
    REFERENCES diagnostics(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_tests_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- TIPOS DE EXÁMENES
-- =========================================================

CREATE TABLE exam_types (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,

  code VARCHAR(50) NOT NULL,
  name VARCHAR(100) NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,

  PRIMARY KEY (id),

  UNIQUE KEY uq_exam_types_code (code)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- SOLICITUDES DE EXÁMENES
-- =========================================================
-- Esta tabla reemplaza la antigua tabla radiology_requests.
-- Permite manejar radiología, laboratorio y otros tipos de examen.

CREATE TABLE exam_requests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,

  patient_id INT UNSIGNED NOT NULL,
  exam_type_id INT UNSIGNED NOT NULL,

  -- Información general de la solicitud
  request_date DATE NOT NULL DEFAULT (CURRENT_DATE),
  unit VARCHAR(120) DEFAULT NULL,
  insured VARCHAR(10) DEFAULT NULL,
  clinic_bed VARCHAR(100) DEFAULT NULL,
  service VARCHAR(100) DEFAULT NULL,
  code VARCHAR(50) DEFAULT NULL,

  -- Información sobre exámenes anteriores
  prior_radiograph VARCHAR(50) DEFAULT NULL,
  prior_radiograph_code VARCHAR(50) DEFAULT NULL,

  -- Solicitud del examen
  exam_requested TEXT DEFAULT NULL,
  clinical_data TEXT DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  evolution_time VARCHAR(100) DEFAULT NULL,
  presumptive_diagnosis TEXT DEFAULT NULL,
  observations TEXT DEFAULT NULL,

  -- Información de radiología
  doctor_code VARCHAR(100) DEFAULT NULL,
  technician VARCHAR(100) DEFAULT NULL,
  plates_used VARCHAR(50) DEFAULT NULL,
  findings TEXT DEFAULT NULL,
  conclusions TEXT DEFAULT NULL,
  radiology_date DATE DEFAULT NULL,
  radiographs_archived VARCHAR(100) DEFAULT NULL,
  radiograph_count VARCHAR(50) DEFAULT NULL,
  dictating_doctor_code VARCHAR(100) DEFAULT NULL,

  -- Resultado general
  result TEXT DEFAULT NULL,

  -- Estado y auditoría
  status VARCHAR(50) NOT NULL DEFAULT 'pending',

  deleted_at DATETIME DEFAULT NULL,

  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),

  INDEX idx_exam_requests_patient (patient_id),
  INDEX idx_exam_requests_type (exam_type_id),
  INDEX idx_exam_requests_status (status),
  INDEX idx_exam_requests_created_by (created_by),
  INDEX idx_exam_requests_deleted_at (deleted_at),

  CONSTRAINT fk_exam_requests_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_exam_requests_type
    FOREIGN KEY (exam_type_id)
    REFERENCES exam_types(id),

  CONSTRAINT fk_exam_requests_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- TIPOS DE EXÁMENES POR DEFECTO
-- =========================================================

INSERT INTO exam_types (code, name, active) VALUES
  ('GENERAL', 'Examen general', 1),
  ('LAB', 'Laboratorio', 1),
  ('RAD', 'Radiología', 1);


-- =========================================================
-- SIGNOS VITALES
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_vitals_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_vitals_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_vitals_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_vitals_patient (patient_id),
  INDEX idx_vitals_measured_at (measured_at),
  INDEX idx_vitals_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- NOTAS CLÍNICAS
-- =========================================================

CREATE TABLE clinical_notes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  note_type VARCHAR(50) NOT NULL DEFAULT 'progress',
  note_text TEXT NOT NULL,
  is_confidential TINYINT(1) NOT NULL DEFAULT 0,

  created_by INT UNSIGNED DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_clinical_notes_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_clinical_notes_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_clinical_notes_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_clinical_notes_patient (patient_id),
  INDEX idx_clinical_notes_encounter (encounter_id),
  INDEX idx_clinical_notes_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- HISTORIA CLÍNICA PEDIÁTRICA
-- =========================================================

CREATE TABLE pediatric_clinical_history (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  visit_date DATE NOT NULL,

  -- Anamnesis
  reason_for_consultation TEXT DEFAULT NULL,
  personal_pathological_history TEXT DEFAULT NULL,
  risk_factors TEXT DEFAULT NULL,
  family_pathological_history TEXT DEFAULT NULL,
  family_environment TEXT DEFAULT NULL,
  education_work_living TEXT DEFAULT NULL,
  activities_social TEXT DEFAULT NULL,
  physical_activity TEXT DEFAULT NULL,

  -- Signos principales
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

  -- Nutrición y crecimiento
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

  -- Alimentación
  lactancia_materna TINYINT(1) NOT NULL DEFAULT 0,
  lactancia_nocturna TINYINT(1) NOT NULL DEFAULT 0,
  lactancia_mas_8_veces TINYINT(1) NOT NULL DEFAULT 0,
  otros_liquidos TINYINT(1) NOT NULL DEFAULT 0,
  uso_biberon TINYINT(1) NOT NULL DEFAULT 0,

  problemas_posicion TINYINT(1) NOT NULL DEFAULT 0,
  problemas_agarre TINYINT(1) NOT NULL DEFAULT 0,
  problemas_succion TINYINT(1) NOT NULL DEFAULT 0,

  -- Vacunas y suplementos
  vacuna TINYINT(1) NOT NULL DEFAULT 0,
  vacuna_edad TINYINT(1) NOT NULL DEFAULT 0,
  vitamina_a TINYINT(1) NOT NULL DEFAULT 0,
  hierro TINYINT(1) NOT NULL DEFAULT 0,
  zinc TINYINT(1) NOT NULL DEFAULT 0,
  antiparasitario TINYINT(1) NOT NULL DEFAULT 0,

  -- Entorno familiar
  buen_trato TINYINT(1) NOT NULL DEFAULT 0,
  relacion_afectivo ENUM('Madre','Padre','Cuidador') DEFAULT NULL,

  lesiones_fisicas TINYINT(1) NOT NULL DEFAULT 0,
  lesiones_genitales TINYINT(1) NOT NULL DEFAULT 0,
  lesiones_ano TINYINT(1) NOT NULL DEFAULT 0,

  comportamiento_alterado TINYINT(1) NOT NULL DEFAULT 0,
  comportamiento_cuidador_alterado TINYINT(1) NOT NULL DEFAULT 0,

  -- Notas y datos adicionales
  notes TEXT DEFAULT NULL,
  form_data TEXT DEFAULT NULL,

  created_by INT UNSIGNED DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_pch_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_pch_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_pch_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_pch_patient_date (patient_id, visit_date),
  INDEX idx_pch_encounter (encounter_id),
  INDEX idx_pch_created_at (created_at),
  INDEX idx_pch_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- PLANES DE TRATAMIENTO
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_treatment_plans_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_treatment_plans_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_treatment_plans_diagnostic
    FOREIGN KEY (diagnostic_id)
    REFERENCES diagnostics(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_treatment_plans_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_treatment_plans_patient (patient_id),
  INDEX idx_treatment_plans_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- PROCEDIMIENTOS CLÍNICOS
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_clinical_procedures_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_clinical_procedures_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_clinical_procedures_user
    FOREIGN KEY (performed_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_clinical_procedures_patient (patient_id),
  INDEX idx_clinical_procedures_date (procedure_date),
  INDEX idx_clinical_procedures_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- CATÁLOGO DE MEDICAMENTOS
-- =========================================================

CREATE TABLE medications_catalog (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  medication_name VARCHAR(200) NOT NULL,
  generic_name VARCHAR(200) DEFAULT NULL,
  form VARCHAR(100) DEFAULT NULL,
  strength VARCHAR(100) DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  UNIQUE KEY uq_medications_catalog_name (medication_name),
  INDEX idx_medications_catalog_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- RECETAS
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_prescriptions_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_prescriptions_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_prescriptions_med
    FOREIGN KEY (medication_id)
    REFERENCES medications_catalog(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_prescriptions_user
    FOREIGN KEY (prescribed_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_prescriptions_patient (patient_id),
  INDEX idx_prescriptions_status (status),
  INDEX idx_prescriptions_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- ADMINISTRACIÓN DE TRATAMIENTOS
-- =========================================================

CREATE TABLE treatment_administration (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  prescription_id INT UNSIGNED NOT NULL,
  administered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  administered_dose VARCHAR(100) DEFAULT NULL,
  notes TEXT DEFAULT NULL,

  administered_by INT UNSIGNED DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  CONSTRAINT fk_treatment_admin_prescription
    FOREIGN KEY (prescription_id)
    REFERENCES prescriptions(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_treatment_admin_user
    FOREIGN KEY (administered_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_treatment_admin_prescription (prescription_id),
  INDEX idx_treatment_admin_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- INMUNIZACIONES
-- =========================================================

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

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_immunizations_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_immunizations_user
    FOREIGN KEY (administered_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_immunizations_patient (patient_id),
  INDEX idx_immunizations_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- CITAS
-- =========================================================

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

  CONSTRAINT fk_appointments_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_appointments_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_appointments_provider
    FOREIGN KEY (provider_user_id)
    REFERENCES users(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_appointments_created_by
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_appointments_patient (patient_id),
  INDEX idx_appointments_encounter (encounter_id),
  INDEX idx_appointments_at (appointment_at),
  INDEX idx_appointments_status (status),
  INDEX idx_appointments_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- MENSAJES DEL CHAT
-- =========================================================

CREATE TABLE chat_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  user_id INT UNSIGNED DEFAULT NULL,
  username VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  recipient_id INT UNSIGNED DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_chat_messages_user
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_chat_messages_recipient
    FOREIGN KEY (recipient_id)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_chat_messages_created_at (created_at),
  INDEX idx_chat_messages_recipient (recipient_id),
  INDEX idx_chat_messages_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- REGISTRO DE AUDITORÍA
-- =========================================================

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  actor_user_id INT UNSIGNED DEFAULT NULL,
  entity_name VARCHAR(100) NOT NULL,
  entity_id VARCHAR(100) DEFAULT NULL,
  action VARCHAR(40) NOT NULL,
  details JSON DEFAULT NULL,
  ip_address VARCHAR(64) DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_audit_logs_actor
    FOREIGN KEY (actor_user_id)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_audit_logs_entity (entity_name, entity_id),
  INDEX idx_audit_logs_action (action),
  INDEX idx_audit_logs_created_at (created_at),
  INDEX idx_audit_logs_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- MÉDICOS ASOCIADOS A UN ENCUENTRO
-- =========================================================

CREATE TABLE encounter_doctors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  encounter_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  role VARCHAR(80) DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_encounter_doctors_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_encounter_doctors_user
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

  INDEX idx_encounter_doctors_encounter (encounter_id),
  INDEX idx_encounter_doctors_user (user_id),
  INDEX idx_encounter_doctors_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- SEGUIMIENTO INTEGRAL DE NIÑEZ Y ADOLESCENCIA
-- =========================================================

CREATE TABLE seguimiento_integral_ninez_adolescencia (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  patient_id INT UNSIGNED NOT NULL,
  encounter_id INT UNSIGNED DEFAULT NULL,
  visit_date DATE NOT NULL,

  -- Signos principales
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

  -- Nutrición y crecimiento
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

  -- Alimentación
  lactancia_materna TINYINT(1) NOT NULL DEFAULT 0,
  lactancia_nocturna TINYINT(1) NOT NULL DEFAULT 0,
  lactancia_mas_8_veces TINYINT(1) NOT NULL DEFAULT 0,
  otros_liquidos TINYINT(1) NOT NULL DEFAULT 0,
  uso_biberon TINYINT(1) NOT NULL DEFAULT 0,

  problemas_posicion TINYINT(1) NOT NULL DEFAULT 0,
  problemas_agarre TINYINT(1) NOT NULL DEFAULT 0,
  problemas_succion TINYINT(1) NOT NULL DEFAULT 0,

  -- Vacunas y suplementos
  vacuna TINYINT(1) NOT NULL DEFAULT 0,
  vacuna_edad TINYINT(1) NOT NULL DEFAULT 0,
  vitamina_a TINYINT(1) NOT NULL DEFAULT 0,
  hierro TINYINT(1) NOT NULL DEFAULT 0,
  zinc TINYINT(1) NOT NULL DEFAULT 0,
  antiparasitario TINYINT(1) NOT NULL DEFAULT 0,

  -- Entorno familiar
  buen_trato TINYINT(1) NOT NULL DEFAULT 0,
  relacion_afectivo ENUM('Madre','Padre','Cuidador') DEFAULT NULL,

  lesiones_fisicas TINYINT(1) NOT NULL DEFAULT 0,
  lesiones_genitales TINYINT(1) NOT NULL DEFAULT 0,
  lesiones_ano TINYINT(1) NOT NULL DEFAULT 0,

  comportamiento_alterado TINYINT(1) NOT NULL DEFAULT 0,
  comportamiento_cuidador_alterado TINYINT(1) NOT NULL DEFAULT 0,

  -- Notas
  notas TEXT DEFAULT NULL,

  created_by INT UNSIGNED DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_seg_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_seg_encounter
    FOREIGN KEY (encounter_id)
    REFERENCES encounters(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_seg_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_seg_patient_date (patient_id, visit_date),
  INDEX idx_seg_encounter (encounter_id),
  INDEX idx_seg_created_at (created_at),
  INDEX idx_seg_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- NOTAS DEL SEGUIMIENTO
-- =========================================================

CREATE TABLE seguimiento_notas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  pediatric_history_id INT UNSIGNED NOT NULL,

  tipo VARCHAR(50) DEFAULT NULL,
  contenido TEXT DEFAULT NULL,

  created_by INT UNSIGNED DEFAULT NULL,

  deleted_at DATETIME DEFAULT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_seguimiento_notas_history
    FOREIGN KEY (pediatric_history_id)
    REFERENCES pediatric_clinical_history(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_seguimiento_notas_user
    FOREIGN KEY (created_by)
    REFERENCES users(id)
    ON DELETE SET NULL,

  INDEX idx_seguimiento_notas_history (pediatric_history_id),
  INDEX idx_seguimiento_notas_created_at (created_at),
  INDEX idx_seguimiento_notas_deleted_at (deleted_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- VISTA DE HISTORIA CLÍNICA PEDIÁTRICA
-- =========================================================

DROP VIEW IF EXISTS pediatric_clinical_history_view;

CREATE VIEW pediatric_clinical_history_view AS
SELECT
    pch.id,
    pch.patient_id,
    pch.encounter_id,
    pch.visit_date,
    pch.notes,
    pch.created_at,
    pch.created_by,

    CASE
        WHEN TIMESTAMPDIFF(YEAR, p.dob, pch.visit_date) < 12
            THEN 'ninez'
        ELSE 'adolescencia'
    END AS age_band,

    'pediatric_clinical_history' AS source_table

FROM pediatric_clinical_history pch

INNER JOIN patients p
    ON p.id = pch.patient_id

WHERE p.dob IS NOT NULL
  AND pch.deleted_at IS NULL
  AND p.deleted_at IS NULL;


-- =========================================================
-- FIN DEL ESQUEMA
-- =========================================================