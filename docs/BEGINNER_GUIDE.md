# Beginner Guide

This guide is for developers new to this project.

## 1) Understand the Big Picture

The app follows a layered flow:

1. `public/*.php` page entrypoint
2. `public/api/*.php` API wrapper
3. `app/Controllers/Api/*Controller.php` request handling
4. `app/Services/*Service.php` business rules
5. `app/Repositories/*Repository.php` SQL and database access

If you only remember one thing: keep page and API files thin, and put logic in services/repositories.

## 2) First 30 Minutes

1. Start the app:
   - PowerShell: `./run.ps1`
   - CMD: `run.cmd`
2. Open `http://localhost:8000/setup.php` and initialize the database.
3. Open one feature page (example: patients).
4. Find the matching page and API files.
5. Follow the controller -> service -> repository path.

## 3) Where to Make Changes

- UI behavior (frontend JS): `public/assets/js/`
- API URL file: `public/api/`
- Input validation and response shaping: `app/Controllers/Api/`
- Business logic: `app/Services/`
- SQL queries: `app/Repositories/`
- Shared UI pieces: `app/Views/Shared/`, `templates/`

## 4) Safe Change Checklist

Before submitting changes, verify:

1. You did not put SQL in page/API wrapper files.
2. You reused `App\Core\Auth` for authorization checks.
3. API responses stay consistent using `App\Core\ApiResponse`.
4. New behavior is in service/repository when possible.
5. Setup page still works in a clean local DB.

## 5) Example: Add One Field to a Form

1. Add field in HTML/partial.
2. Include field in frontend request payload.
3. Validate field in controller.
4. Apply business rules in service (if needed).
5. Persist field in repository SQL.
6. Return the new field in API response.

## 6) Common Mistakes

- Adding heavy logic directly in `public/*.php`.
- Duplicating validation in multiple places instead of centralizing it.
- Returning inconsistent JSON shapes across API actions.
- Mixing data-access logic into controllers.

## 7) Next Reading

- Architecture details: `docs/ARCHITECTURE.md`
- Root onboarding: `README.md`
