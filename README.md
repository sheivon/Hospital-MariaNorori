# Hospital Patient Records (PHP + MySQL)

Hospital management starter application built with PHP, PDO, Bootstrap, DataTables, and AJAX.

## Features

- Authentication and role-based access.
- Patient management (create, update, soft delete).
- Diagnostics module.
- Admin user management.
- Generic admin data manager for CRUD across hospital tables.
- Bilingual UI (English / Spanish).

## Requirements

- PHP 8.0+ (recommended)
- MySQL / MariaDB
- `pdo_mysql` PHP extension enabled
- Windows + PowerShell scripts are included, but app runs on any OS with PHP/MySQL

## Quick Start

1. Configure DB credentials in environment variables or `.env` file (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`).
2. Start the local server:

```powershell
.\run.ps1
```

3. Open setup page and initialize schema/data:

- http://localhost:8000/setup.php

Use **Initialize Database** to create DB, run schema, and seed default users.

## Database

- Baseline schema: `migrations/init.sql`
- Setup MVC flow:
  - `app/Controllers/SetupController.php`
  - `app/Models/SetupModel.php`
  - `public/setup.php`

## Project Structure

```text
hospital/
├─ app/
│  ├─ Core/                 # DB, auth, API response helpers
│  ├─ Controllers/          # MVC controllers (API + setup)
│  ├─ Models/               # DB models and business rules
│  └─ bootstrap.php         # Autoload/bootstrap
├─ config/
│  └─ db.php                # PDO bootstrap (uses app/Core/Database)
├─ docs/
│  └─ ARCHITECTURE.md       # Architecture and entity relationships
├─ migrations/
│  └─ init.sql              # Single baseline schema
├─ public/
│  ├─ index.php             # Landing/dashboard summary
│  ├─ patients.php          # Patients view
│  ├─ diagnostics.php       # Diagnostics view
│  ├─ tests.php             # Tests view
│  ├─ setup.php             # DB setup UI
│  ├─ admin/
│  │  ├─ users.php          # User administration
│  │  └─ data_manager.php   # Generic CRUD manager
│  ├─ api/                  # API endpoints
│  └─ assets/               # JS, CSS, i18n, vendor assets
├─ scripts/                 # Utility scripts (admin creation, checks, etc.)
├─ src/                     # Legacy compatibility wrappers
├─ templates/               # Shared header/footer/modals
├─ run.ps1                  # Start development server
└─ push.ps1                 # Stage/commit/push helper
```

## Notes

- Soft delete is enforced in key modules using `deleted_at`.
- Role values are sourced from `user_roles`.
- For architecture details, see `docs/ARCHITECTURE.md`.

## Theme / Branding

The UI uses an application color palette that matches the hospital logo and navbar:

- Primary: `#0b5ed7` (blue)
- Secondary: `#2d9f6c` (green)
- Accent: `#f7b500` (yellow)
- Dark text: `#1f2937`
- Border: `#cbd5e1`
- Background hue: `#f1f5f9`

DataTables components are themed with the same palette:

- Headers: gradient from primary to secondary
- Action buttons (pagination): primary background when active
- Row hover: subtle primary tint
- Controls (filter + length selector): brand border and rounded corners

## Security

- Do not expose `setup.php` in production.
- Use strong credentials and HTTPS.
- Add CSRF protection and stricter validation before production use.


