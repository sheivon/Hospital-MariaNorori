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

## Next Recommended Migration

- Move page-level logic from `public/*.php` into page controllers gradually.
- Introduce a simple router (optional) to reduce the number of endpoint files.
- Add services in `app/Services/` if business logic grows beyond controllers/models.
