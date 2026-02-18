-- Add email column to patients table
ALTER TABLE patients
  ADD COLUMN email VARCHAR(255) NULL AFTER last_name;
