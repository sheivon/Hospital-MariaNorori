<#
run.ps1 - start the application quickly for local dev

Usage examples:
  .\run.ps1                # start PHP builtin server on localhost:8000 and open browser
  .\run.ps1 -Port 8080     # start on port 8080
  .\run.ps1 -NoOpen        # don't open the browser automatically
  .\run.ps1 -UseXamppPhp   # prefer XAMPP php at C:\xampp\php\php.exe (if detected)
#>
param(
    [int]$Port = 8000,
    [switch]$NoOpen,
    [switch]$UseXamppPhp
)

$ErrorActionPreference = 'Stop'

$root = (Get-Location).ProviderPath
Write-Host "Project root: $root"

# Try to find php.exe: prefer system php, else XAMPP copy
$phpCmd = $null
# Find php on system PATH (PowerShell 5 compatible)
$cmd = Get-Command php -ErrorAction SilentlyContinue
if ($cmd -and -not $UseXamppPhp) { $phpCmd = $cmd.Source }

if (-not $phpCmd) {
    $xamppPhp = 'C:\xampp\php\php.exe'
    if (Test-Path $xamppPhp) { $phpCmd = $xamppPhp }
}

if (-not $phpCmd) {
    Write-Error "php executable not found. Install PHP or ensure C:\xampp\php\php.exe exists, then re-run with -UseXamppPhp if needed."
    exit 1
}

# Build arguments for PHP built-in server
$phpHost = 'localhost'
$phpArgs = "-S $phpHost`:$Port -t public"
Write-Host "Starting PHP built-in server: $phpCmd $phpArgs" -ForegroundColor Cyan

# Start server in a new window so this script returns immediately
Start-Process -FilePath $phpCmd -ArgumentList $phpArgs -WorkingDirectory $root -WindowStyle Normal

$uri = "http://localhost:$Port/"
if (-not $NoOpen) {
    Write-Host "Opening $uri" -ForegroundColor Cyan
    Start-Process $uri
}

Write-Host "Server started. To stop it, find the php process (Task Manager) and terminate it." -ForegroundColor Green
