# build.ps1 - install frontend dependencies for the project
# Usage: Open PowerShell in the project root and run: .\build.ps1

$ErrorActionPreference = 'Stop'

Write-Host "Running build: installing npm packages..." -ForegroundColor Cyan

if (-not (Get-Command npm -ErrorAction SilentlyContinue)) {
    Write-Warning "npm is not in PATH. Please install Node.js/npm or run this script from an environment where npm is available."
} else {
    npm install

    # Copy DataTables Buttons and export assets from node_modules to public assets for offline use
    $dest = Join-Path -Path (Get-Location) -ChildPath 'public/assets/vendor/datatables'
    if (-not (Test-Path $dest)) { New-Item -ItemType Directory -Path $dest -Force | Out-Null }

    Write-Host "Copying DataTables Buttons and export assets to $dest" -ForegroundColor Cyan
    try {
        $copyPairs = @(
            @{ src = 'node_modules\datatables.net-buttons-dt\css\buttons.dataTables.min.css'; dst = 'buttons.dataTables.min.css' },
            @{ src = 'node_modules\datatables.net-buttons\js\dataTables.buttons.min.js'; dst = 'dataTables.buttons.min.js' },
            @{ src = 'node_modules\datatables.net-buttons\js\buttons.html5.min.js'; dst = 'buttons.html5.min.js' },
            @{ src = 'node_modules\datatables.net-buttons\js\buttons.print.min.js'; dst = 'buttons.print.min.js' },
            @{ src = 'node_modules\jszip\dist\jszip.min.js'; dst = 'jszip.min.js' },
            @{ src = 'node_modules\pdfmake\build\pdfmake.min.js'; dst = 'pdfmake.min.js' },
            @{ src = 'node_modules\pdfmake\build\vfs_fonts.js'; dst = 'vfs_fonts.js' }
        )

        foreach ($p in $copyPairs) {
            $s = Join-Path (Get-Location) $p.src
            $d = Join-Path $dest $p.dst
            if (Test-Path $s) {
                Copy-Item -Path $s -Destination $d -Force -ErrorAction Stop
                Write-Host "Copied: $p.src -> $d"
            } else {
                Write-Warning "Missing upstream asset: $s (was not copied)"
                # Mark as missing so we can attempt to download from CDN later
                $p.missing = $true
            }
        }
    } catch {
        Write-Warning "Failed copying some assets: $_"
    }
    
    # If any asset was missing from node_modules, attempt to download from known CDN URLs
    $cdnMap = @{
        'buttons.dataTables.min.css' = 'https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css';
        'dataTables.buttons.min.js' = 'https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js';
        'buttons.html5.min.js' = 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js';
        'buttons.print.min.js' = 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js';
        'jszip.min.js' = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
        'pdfmake.min.js' = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js';
        'vfs_fonts.js' = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js'
    }

    foreach ($p in $copyPairs) {
        $d = Join-Path $dest $p.dst
        if (-not (Test-Path $d)) {
            $filename = $p.dst
            if ($cdnMap.ContainsKey($filename)) {
                $url = $cdnMap[$filename]
                Write-Host "Downloading $filename from CDN: $url" -ForegroundColor Cyan
                try {
                    Invoke-WebRequest -Uri $url -OutFile $d -UseBasicParsing -ErrorAction Stop
                    Write-Host "Downloaded: $filename -> $d"
                } catch {
                    $msg = $_.Exception.Message
                    Write-Warning ("Failed to download {0} from {1}: {2}" -f $filename, $url, $msg)
                }
            } else {
                Write-Warning "No CDN mapping for $filename; cannot download automatically."
            }
        }
    }
}

    # --- Scan project files for other https:// CDN assets (js/css/fonts/images) and download them locally ---
    $cdnDestAll = Join-Path (Get-Location) 'public/assets/vendor/cdn'
    if (-not (Test-Path $cdnDestAll)) { New-Item -ItemType Directory -Path $cdnDestAll -Force | Out-Null }

    Write-Host "Scanning project files for https:// URLs to cache locally..." -ForegroundColor Cyan
    $scanPatterns = '*.php','*.html','*.htm','*.js','*.css'
    $files = Get-ChildItem -Recurse -Include $scanPatterns -File | Where-Object { $_.FullName -notmatch '\\node_modules\\' -and $_.FullName -notmatch '\\public\\assets\\vendor\\' -and $_.Name -ne 'package-lock.json' }
    $urlRegex = 'https://[^"''\s>]+?\.(?:js|css|woff2?|ttf|svg|png|jpg|jpeg)'
    $found = @{}
    foreach ($f in $files) {
        try {
            $content = Get-Content -Raw -Encoding UTF8 -Path $f.FullName
            $mcoll = [regex]::Matches($content, $urlRegex)
            foreach ($m in $mcoll) {
                $url = $m.Value.Trim()
                if (-not $found.ContainsKey($url)) { $found[$url] = $true }
            }
        } catch { }
    }

    if ($found.Count -eq 0) { Write-Host "No external CDN assets detected." -ForegroundColor Yellow }
    else {
        Write-Host "Found $($found.Count) external assets. Downloading into $cdnDestAll" -ForegroundColor Cyan
        foreach ($url in $found.Keys) {
            try {
                $uri = [uri]$url
                $rawname = [System.IO.Path]::GetFileName($uri.LocalPath)
                if ([string]::IsNullOrEmpty($rawname)) { $rawname = [System.IO.Path]::GetFileName($uri.AbsolutePath) }
                # strip query strings already removed by LocalPath/AbsolutePath
                $target = Join-Path $cdnDestAll $rawname
                if (Test-Path $target) { Write-Host "Already have: $rawname (skipping)"; continue }
                Write-Host "Downloading $url -> $rawname" -ForegroundColor Cyan
                Invoke-WebRequest -Uri $url -OutFile $target -UseBasicParsing -ErrorAction Stop
            } catch {
                $msg = $_.Exception.Message
                Write-Warning ("Failed to download {0}: {1}" -f $url, $msg)
            }
        }
    }

Write-Host "Build step completed." -ForegroundColor Green
