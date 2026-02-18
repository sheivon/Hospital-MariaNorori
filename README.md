# Hospital Patient Records (PHP + MySQL)

A minimal patient records web app using PHP, PDO, Bootstrap and AJAX.

Prerequisites
- XAMPP (or PHP + MySQL) installed on Windows
- PHP 7.4+ recommended

Quick start (using XAMPP)
1. Copy this project folder into `C:/xampp/htdocs/hospital` (or your www folder).
2. Start Apache and MySQL via XAMPP Control Panel.
3. Create the database and tables:
   - Open phpMyAdmin (http://localhost/phpmyadmin) and import `migrations/init.sql`, or run from command line:

```powershell
# create the database/tables from the migration SQL
mysql -u root -p < migrations/init.sql

# (optional) run the built-in PHP server for development (PowerShell):
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8000 -t public
```

4. Create an admin user (script will prompt for username/password):

```powershell
php scripts/create_admin.php
```

5. Open the app in your browser:
   - If you put the project under Apache's htdocs and mapped `/hospital` to the project root: http://localhost/hospital/
   - If you run the built-in PHP server (development): http://localhost:8000/

Alternatively, run a built-in PHP server (for dev):

```powershell
cd C:/00-IN-DEV/Health
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8000 -t public
# then open http://127.0.0.1:8000 or http://localhost:8000
```

Default DB settings
- See `.env.example` for example values. You can create a `.env` file in the project root to override them.

Files of interest
- `migrations/init.sql` — DB schema
- `scripts/create_admin.php` — creates an admin user using password_hash
- `public/` — web root: `index.php`, `login.php`, `api/` endpoints
- `src/` — auth and patient helper functions
- `templates/` — header/footer with Bootstrap

Security notes
- This is a minimal starter. Do not run in production without adding proper CSRF protections, input validation and HTTPS.

If you want, I can now implement additional features (search, pagination, export) or tighten security (CSRF tokens, input validation).



----

admin
admin123


cliadmin
cliPass2025

