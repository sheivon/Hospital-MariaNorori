# Hospital Management System (PHP + MySQL)

Hospital web application built with PHP, PDO, Bootstrap, and a modular controller/service/repository architecture.

## Start Here (Beginner Friendly)

If this is your first time in this codebase, follow this exact order:

1. Run the app locally (see Quick Start below).
2. Open one page in the browser, for example `/pacientes.php`.
3. Find its page entrypoint in `public/` and read it top-to-bottom.
4. Find the API endpoint used by that page in `public/api/`.
5. Follow the call chain into:
   - `app/Controllers/Api/`
   - `app/Services/` (if used)
   - `app/Repositories/`

This is the fastest way to understand "how things work" in this project.

## Quick Start

### Requirements

- PHP 8.0+
- MySQL / MariaDB
- PHP extensions: `pdo`, `pdo_mysql`

### Run

```powershell
.\run.ps1
```

Alternative:

```cmd
run.cmd
```

Then open:

- `http://localhost:8000/setup.php`

Use setup to initialize schema and seed data.

## What To Edit (Rule of Thumb)

- New page UI behavior: `public/assets/js/`
- New API route wrapper: `public/api/`
- Request validation and orchestration: `app/Controllers/Api/`
- Business rules: `app/Services/`
- SQL/data access: `app/Repositories/`
- Shared HTML partials/layout: `app/Views/Shared/` and `templates/`

Keep `public/*.php` and `public/api/*.php` thin. Put real logic in controllers, services, and repositories.

## Project Map

```text
hospital/
|- app/
|  |- bootstrap.php            (autoload and app bootstrap)
|  |- Core/                    (Database, Auth, Router, ApiResponse)
|  |- Controllers/             (HTTP orchestration)
|  |  `- Api/
|  |- Services/                (business workflows)
|  |- Repositories/            (SQL and persistence)
|  |- Interfaces/              (contracts)
|  |- Helpers/                 (shared helpers)
|  |- Modules/                 (module registration/navigation)
|  |- Views/                   (templates)
|  |  |- Pages/
|  |  `- Shared/
|  |- ViewModels/              (view-specific shaping)
|  |- Domain/                  (domain-centric classes)
|  `- Infrastructure/          (technical adapters)
|- public/
|  |- *.php                    (web entrypoints)
|  |- api/                     (API wrappers)
|  |- admin/
|  |- assets/
|  |- modal/
|  `- wwwroot/
|- migrations/
|  `- init.sql                 (baseline schema)
|- scripts/                    (maintenance scripts)
|- templates/                  (compatibility wrappers)
|- docs/
|  `- ARCHITECTURE.md
|- config/
|  `- db.php
|- run.ps1 / run.cmd
|- build.ps1
`- push.ps1
```

## Request Flow (Simple Version)

For API calls:

1. Browser calls `/api/<feature>_<action>.php`.
2. Endpoint loads `app/bootstrap.php`.
3. Endpoint calls controller in `app/Controllers/Api/`.
4. Controller validates/authenticates and calls service/repository.
5. Controller returns JSON using `App\Core\ApiResponse`.

For page loads:

1. Browser requests `public/<page>.php`.
2. Page checks auth with `App\Core\Auth`.
3. Page includes shared layout fragments.
4. JavaScript calls API endpoints for dynamic data.

## Naming Conventions

- API endpoint: `<feature>_<action>.php`
  - Example: `encounters_list.php`
- API controller: `<FeaturePlural>Controller`
  - Example: `PatientsController`
- Repository: `<Feature>Repository`
  - Example: `EncounterRepository`
- Service: `<Feature>Service`
  - Example: `PatientService`

## Common Beginner Tasks

### Add a New API Action

1. Create wrapper file in `public/api/`.
2. Add method to the correct controller in `app/Controllers/Api/`.
3. Add/update service logic in `app/Services/` (if needed).
4. Add/update SQL access in `app/Repositories/`.
5. Return consistent JSON (`success`, `data`, `message`) via `ApiResponse`.

### Add a New Page Field

1. Update HTML in page entry or shared partial.
2. Update JS request payload in `public/assets/js/`.
3. Update controller validation.
4. Update repository SQL and schema if required.

## Security Notes

- Do not expose setup tooling in production.
- Enforce HTTPS and strong credentials.
- Add CSRF and stricter validation before production deployment.

## Additional Documentation

- Beginner onboarding: `docs/BEGINNER_GUIDE.md`
- Detailed architecture: `docs/ARCHITECTURE.md`


