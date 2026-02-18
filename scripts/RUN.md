# Quick run/build commands

This project includes small convenience scripts for Windows (PowerShell and CMD) to install frontend dependencies and run a local PHP server for development.

Files:

- `build.ps1` - Installs npm packages (runs `npm install`).
- `run.ps1` - PowerShell helper to start PHP built-in server. It will try `php` on PATH, then `C:\xampp\php\php.exe`.
- `run.cmd` - CMD version that behaves similarly and runs in the console.

Usage (PowerShell):

1. Open PowerShell in the project root (`c:\xampp\htdocs\hospital`).
2. Run the build step (first time or when package.json changes):

   .\build.ps1

3. Start the PHP dev server and open the site in your browser:

   .\run.ps1

   Optional flags:
   - `-Port <port>`  e.g. `.-\run.ps1 -Port 8080`
   - `-NoOpen` to avoid opening the browser automatically.
   - `-UseXamppPhp` to prefer XAMPP's `C:\xampp\php\php.exe`.

Usage (cmd.exe):

1. Open cmd.exe in project root.
2. Run:

   run.cmd

This will attempt to start PHP built-in server on `http://localhost:8000` and serve the `public/` folder.

Notes:
- These scripts are intentionally minimal and safe. They do not alter your database or start/stop XAMPP services.
- If you prefer Apache via XAMPP, start the XAMPP Control Panel and open the site at `http://localhost/hospital/public` (or move the project under `htdocs`).
