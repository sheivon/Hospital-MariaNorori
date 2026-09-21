# build.ps1 - install frontend dependencies and prepare offline DataTables vendor assets
# Usage: Run this from the project root: .\build.ps1

$ErrorActionPreference = 'Stop'

Write-Host "Running build: installing npm packages..." -ForegroundColor Cyan

if (-not (Get-Command npm -ErrorAction SilentlyContinue)) {
    Write-Warning "npm is not in PATH. Please install Node.js/npm or run this script from an environment where npm is available."
    exit 1
}

npm install

# Assets are served from the web root (public/), so drop the /public prefix in URLs.
$webRoot = Join-Path -Path (Get-Location) -ChildPath 'public'
$vendorDest = Join-Path -Path $webRoot -ChildPath 'assets\vendor\datatables'
if (-not (Test-Path $vendorDest)) {
    New-Item -ItemType Directory -Path $vendorDest -Force | Out-Null
}

Write-Host "Copying DataTables vendor assets (offline use)..." -ForegroundColor Cyan

# dst is relative to public/ (the web root). node_modules source is used first,
# with a CDN fallback so the offline bundle can always be recreated.
$assets = @(
    # Core DataTables 2.x (loaded from /assets/js and /assets/css)
    @{ dst = 'assets\js\jquery.dataTables.min.js'; src = 'node_modules\datatables.net\js\dataTables.min.js'; cdn = 'https://cdn.datatables.net/2.3.4/js/dataTables.min.js' },
    @{ dst = 'assets\css\jquery.dataTables.min.css'; src = 'node_modules\datatables.net-dt\css\dataTables.dataTables.min.css'; cdn = 'https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css' },

    # Buttons + export helpers (loaded from /assets/vendor/datatables)
    @{ dst = 'buttons.dataTables.min.css'; src = 'node_modules\datatables.net-buttons-dt\css\buttons.dataTables.min.css'; cdn = 'https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css' },
    @{ dst = 'dataTables.buttons.min.js'; src = 'node_modules\datatables.net-buttons\js\dataTables.buttons.min.js'; cdn = 'https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js' },
    @{ dst = 'buttons.html5.min.js'; src = 'node_modules\datatables.net-buttons\js\buttons.html5.min.js'; cdn = 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js' },
    @{ dst = 'buttons.print.min.js'; src = 'node_modules\datatables.net-buttons\js\buttons.print.min.js'; cdn = 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js' },
    @{ dst = 'jszip.min.js'; src = 'node_modules\jszip\dist\jszip.min.js'; cdn = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js' },
    @{ dst = 'pdfmake.min.js'; src = 'node_modules\pdfmake\build\pdfmake.min.js'; cdn = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js' },
    @{ dst = 'vfs_fonts.js'; src = 'node_modules\pdfmake\build\vfs_fonts.js'; cdn = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js' },
    # DataTables Spanish language pack (no npm equivalent)
    @{ dst = 'Spanish.json'; src = ''; cdn = 'https://raw.githubusercontent.com/DataTables/Plugins/master/i18n/es-ES.json' }
)

foreach ($a in $assets) {
    # Files without an npm source (or those not under vendor/) go to an absolute path
    $destBase = if ($a.dst -like 'assets\*') { $webRoot } else { $vendorDest }
    $destPath = Join-Path -Path $destBase -ChildPath $a.dst

    if ($a.src -and (Test-Path (Join-Path -Path (Get-Location) -ChildPath $a.src))) {
        Copy-Item -Path (Join-Path -Path (Get-Location) -ChildPath $a.src) -Destination $destPath -Force -ErrorAction Stop
        Write-Host "Copied: $($a.src) -> $destPath"
    } elseif ($a.cdn) {
        Write-Host "Downloading missing asset $($a.dst) from CDN..." -ForegroundColor Cyan
        try {
            Invoke-WebRequest -Uri $a.cdn -OutFile $destPath -UseBasicParsing -ErrorAction Stop
            Write-Host "Downloaded: $($a.dst) -> $destPath"
        } catch {
            Write-Warning "Failed to download $($a.dst): $($_.Exception.Message)"
        }
    } else {
        Write-Warning "No source or CDN mapping available for $($a.dst)."
    }
}

Write-Host "Build step completed." -ForegroundColor Green