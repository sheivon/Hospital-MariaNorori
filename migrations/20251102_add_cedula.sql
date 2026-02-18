-- Add cedula column to patients and users
USE hospital;
ALTER TABLE patients ADD COLUMN IF NOT EXISTS cedula VARCHAR(50) DEFAULT NULL AFTER last_name;
ALTER TABLE users ADD COLUMN IF NOT EXISTS cedula VARCHAR(50) DEFAULT NULL AFTER fullname;
