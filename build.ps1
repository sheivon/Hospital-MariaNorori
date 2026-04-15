# build.ps1 - install frontend dependencies and prepare offline DataTables vendor assets
# Usage: Run this from the project root: .\build.ps1

$ErrorActionPreference = 'Stop'

Write-Host "Running build: installing npm packages..." -ForegroundColor Cyan

if (-not (Get-Command npm -ErrorAction SilentlyContinue)) {
    Write-Warning "npm is not in PATH. Please install Node.js/npm or run this script from an environment where npm is available."
    exit 1
}

npm install

# Copy required DataTables vendor assets for offline use
$dest = Join-Path -Path (Get-Location) -ChildPath 'public/assets/vendor/datatables'
if (-not (Test-Path $dest)) {
    New-Item -ItemType Directory -Path $dest -Force | Out-Null
}

Write-Host "Copying DataTables vendor assets to $dest" -ForegroundColor Cyan

$copyPairs = @(
    @{ src = 'node_modules\datatables.net-buttons-dt\css\buttons.dataTables.min.css'; dst = 'buttons.dataTables.min.css' },
    @{ src = 'node_modules\datatables.net-buttons\js\dataTables.buttons.min.js'; dst = 'dataTables.buttons.min.js' },
    @{ src = 'node_modules\datatables.net-buttons\js\buttons.html5.min.js'; dst = 'buttons.html5.min.js' },
    @{ src = 'node_modules\datatables.net-buttons\js\buttons.print.min.js'; dst = 'buttons.print.min.js' },
    @{ src = 'node_modules\jszip\dist\jszip.min.js'; dst = 'jszip.min.js' },
    @{ src = 'node_modules\pdfmake\build\pdfmake.min.js'; dst = 'pdfmake.min.js' },
    @{ src = 'node_modules\pdfmake\build\vfs_fonts.js'; dst = 'vfs_fonts.js' }
)

$cdnMap = {
    'buttons.dataTables.min.css' = 'https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css'
    'dataTables.buttons.min.js'      = 'https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js'
    'buttons.html5.min.js'          = 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js'
    'buttons.print.min.js'          = 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js'
    'jszip.min.js'                  = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js'
    'pdfmake.min.js'                = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js'
    'vfs_fonts.js'                  = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js'
}

foreach ($p in $copyPairs) {
    $srcPath = Join-Path -Path (Get-Location) -ChildPath $p.src
    $destPath = Join-Path -Path $dest -ChildPath $p.dst

    if (Test-Path $srcPath) {
        Copy-Item -Path $srcPath -Destination $destPath -Force -ErrorAction Stop
        Write-Host "Copied: $($p.src) -> $destPath"
    } elseif ($cdnMap.ContainsKey($p.dst)) {
        Write-Host "Downloading missing asset $($p.dst) from CDN..." -ForegroundColor Cyan
        try {
            Invoke-WebRequest -Uri $cdnMap[$p.dst] -OutFile $destPath -UseBasicParsing -ErrorAction Stop
            Write-Host "Downloaded: $($p.dst) -> $destPath"
        } catch {
            Write-Warning "Failed to download $($p.dst): $($_.Exception.Message)"
        }
    } else {
        Write-Warning "No source or CDN mapping available for $($p.dst)."
    }
}

Write-Host "Build step completed." -ForegroundColor Green
