-- ============================================================
-- Migración: foto de paciente
-- Fecha: 2026-11-09
-- Descripción: agrega la columna photo_path a la tabla patients
--              para almacenar la ruta de la imagen del paciente
--              (p. ej. /uploads/patients/20261109_ab12cd34.jpg).
--
-- Aplicar con:  php scripts/apply_sql.php migrations/2026-11-09_add_patients_photo_path.sql
--
-- Nota: la aplicación también aplica este cambio de forma
--       idempotente al arrancar (Database::applySchemaPatches),
--       por lo que ejecutar este script es opcional pero recomendado.
--
-- Requisito del servidor: upload_max_filesize y post_max_size en
--       php.ini deben ser >= 5M para permitir subir fotos.
--
-- Rollback:
--   1. Borrar los archivos de /public/uploads/patients/ (opcional).
--   2. Ejecutar:
--        ALTER TABLE patients DROP COLUMN photo_path;
-- ============================================================

ALTER TABLE patients
  ADD COLUMN photo_path VARCHAR(255) DEFAULT NULL AFTER notes;
