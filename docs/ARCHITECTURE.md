# Architecture (MVC)

This project now follows an incremental MVC structure while keeping existing URL routes stable.

## Folder Map

- `public/`
  - Web entrypoints and API routes (`/api/*.php`)
  - Keep these files thin: parse request and call a controller.
- `app/Core/`
  - Cross-cutting infrastructure: DB connection, auth/session, API response helpers.
- `app/Controllers/`
  - Request orchestration logic (HTTP input/output).
  - `app/Controllers/Api/` for API controllers.
- `app/Models/`
  - Database access and data rules for each domain entity.
- `src/`
  - Legacy compatibility wrappers used by older pages/scripts.
  - New business logic should go in `app/`.
- `config/`
  - Compatibility config bootstrap (`db.php` now delegates to `app/Core/Database.php`).
- `templates/`
  - Shared HTML layout fragments.

## Request Flow

1. Browser calls endpoint in `public/api/...`.
2. Endpoint loads `app/bootstrap.php` and calls controller method.
3. Controller uses model(s) for data access.
4. Controller returns JSON via `App\Core\ApiResponse`.

## Conventions

- Keep SQL in Models, not in endpoint files.
- Keep endpoint files under 10 lines when possible.
- Use `App\Core\Auth` for all auth/role checks.
- Use `App\Core\ApiResponse` for consistent JSON responses.

## Migrated Areas

- Patients API (`patients_list/create/update/delete.php`)
- Chat API (`chat_list.php`, `chat_send.php`)
- Users API (`users_list.php` and admin users CRUD endpoints)
- Legacy wrappers (`src/auth.php`, `src/patient.php`, `src/chat.php`) now delegate to MVC classes.
- Setup flow moved to MVC (`app/Controllers/SetupController.php`, `app/Models/SetupModel.php`) while keeping `public/setup.php` UI.

## Database Baseline

- Migrations are consolidated into a single baseline file: `migrations/init.sql`.
- The baseline script recreates schema from scratch and includes both:
  - Compatibility tables used by the current UI/API (`users`, `patients`, `diagnostics`, `tests`, `chat_messages`), and
  - Extended clinical entities for longitudinal hospital history and reporting.

## Core Entities and Relationships

- `patients` 1-* `encounters`
- `patients` 1-* `diagnostics`, `tests`, `vitals`, `clinical_notes`
- `patients` 1-* `patient_conditions`, `patient_allergies`, `immunizations`
- `patients` 1-* `appointments`, `admissions`
- `admissions` 1-* `bed_movements`
- `encounters` 1-* `diagnostics`, `tests`, `clinical_notes`, `treatment_plans`, `clinical_procedures`
- `diagnostics` 1-* `tests` and optional link to `treatment_plans`
- `medications_catalog` 1-* `prescriptions` 1-* `treatment_administration`
- `users` acts as clinical/administrative actor across creation, update, assignment, and audit relations

## Statistical/History Coverage

- Longitudinal patient history: conditions, allergies, diagnostics, tests, vitals, notes, treatments.
- Operational hospital controls: encounters, appointments, admissions, room/bed movements.
- Medication lifecycle: prescriptions and administered doses.
- Governance: `audit_logs` for traceability of actions over entities.

## Next Recommended Migration

- Move page-level logic from `public/*.php` into page controllers gradually.
- Introduce a simple router (optional) to reduce the number of endpoint files.
- Add services in `app/Services/` if business logic grows beyond controllers/models.
