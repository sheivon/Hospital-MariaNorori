# Architecture (MVC)

This project now follows an incremental MVC structure while keeping existing URL routes stable.

## Folder Map

- `public/`
  - Web entrypoints and API routes (`/api/*.php`).
  - Keep these files thin: parse request and call a controller.
- `app/Core/`
  - Cross-cutting infrastructure: DB connection, auth/session, API response helpers.
  - Current technical baseline while migration into `app/Infrastructure/` progresses.
- `app/Infrastructure/`
  - ASP.NET-style infrastructure layer for technical services and adapters.
- `app/Controllers/`
  - Request orchestration logic (HTTP input/output).
  - `app/Controllers/Api/` for API controllers.
  - `app/Controllers/SetupController.php` for setup workflow.
- `app/Views/`
  - ASP.NET-style view layer.
  - `app/Views/Shared/` contains shared layout fragments and modal partials.
- `app/ViewModels/`
  - View-specific DTOs prepared by controllers.
- `app/Services/`
  - Business logic services and orchestration beyond simple CRUD.
  - Includes `app/Services/Admin/` and `app/Services/PrintService.php`.
- `app/Interfaces/`
  - Repository and service contracts for decoupling implementations.
- `app/Entities/`
  - Domain entities and value object definitions.
- `app/Domain/`
  - ASP.NET-style target folder for domain-centric classes.
- `app/Models/`
  - Repository-style database access and data persistence logic.
- `app/Repositories/`
  - ASP.NET-style target folder for repository classes.
- `src/`
  - Legacy compatibility wrappers used by older pages/scripts.
  - These wrappers are deprecated: public pages now use direct OOP classes under `app/`.
- `app/Helpers/`
  - Reusable helper classes for auth, patient, and chat access.
- `config/`
  - Compatibility config bootstrap (`db.php` now delegates to `app/Core/Database.php`).
- `templates/`
  - Compatibility wrappers that forward to `app/Views/Shared`.
- `public/modal/`
  - Compatibility wrappers that forward to `app/Views/Shared/Modals`.
- `public/wwwroot/`
  - ASP.NET-style target static root.
  - Existing static assets remain in `public/assets` during migration.

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
- Keep front-end modules clean: API client layer (`UsersApi`), UI modal state (`UserModal`), and page renderer/controller (`UserView`).

## Migrated Areas

- Patients API (`patients_list/create/update/delete.php`)
- Chat API (`chat_list.php`, `chat_send.php`)
- Users API (`users_list.php` and admin users CRUD endpoints)
- Legacy wrappers (`src/auth.php`, `src/patient.php`, `src/chat.php`) are now deprecated and no longer used by current public pages.
- Setup flow moved to MVC (`app/Controllers/SetupController.php`, `app/Models/SetupRepository.php`) while keeping `public/setup.php` UI.

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
