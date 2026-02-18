# Scans the project for https:// asset URLs and downloads them into public/assets/vendor/cdn/
# Usage: .\scripts\download_cdn_assets.ps1

$ErrorActionPreference = 'Stop'

$RepoRoot = Resolve-Path (Join-Path $PSScriptRoot '..')
$outDir = Join-Path $RepoRoot 'public\assets\vendor\cdn'
if (-not (Test-Path $outDir)) { New-Item -ItemType Directory -Path $outDir -Force | Out-Null }

Write-Host "Repo root: $RepoRoot" -ForegroundColor Cyan
Write-Host "Output directory: $outDir" -ForegroundColor Cyan

# File globs to scan
$globs = @('*.php','*.html','*.js','*.css','*.json','*.twig','*.htm')

# Regex to capture https URLs ending with common asset extensions or with a querystring
$urlRegex = '(https?://[^"'""\s\)>]+?(?:\.(?:js|css|woff2?|woff|ttf|svg|png|jpg|jpeg|map)|(?:\?[^\s"'"">]+)))'

$urls = [System.Collections.Generic.HashSet[string]]::new()

foreach ($g in $globs) {
    Get-ChildItem -Path $RepoRoot -Recurse -Include $g -File -ErrorAction SilentlyContinue | ForEach-Object {
        try{
            $content = Get-Content -Raw -ErrorAction Stop -Path $_.FullName
            foreach ($m in [regex]::Matches($content, $urlRegex)){
                $u = $m.Groups[1].Value.Trim()
                if ($u -and -not $u.StartsWith('data:')){ $null = $urls.Add($u) }
            }
        } catch { }
    }
}

Write-Host "Found $($urls.Count) unique candidate URLs." -ForegroundColor Green

$downloaded = 0
$skipped = 0
$failed = 0

foreach ($url in $urls) {
    try{
        # sanitize and map to local path: host + absolutePath
        $uri = [uri]$url
        $absPath = $uri.AbsolutePath.TrimStart('/')
        if (-not $absPath) { $absPath = 'index' }
        # sanitize query by appending as safe suffix if present
        $querySuffix = ''
        if ($uri.Query) { $qs = $uri.Query.TrimStart('?'); $qs = $qs -replace '[\\/:*?"<>|&=]', '_'; $querySuffix = '_' + $qs }
        $localRel = Join-Path $uri.Host ($absPath + $querySuffix)
        # replace forward slashes with backslashes for Windows path
        $localRel = $localRel -replace '/','\\'
        $localPath = Join-Path $outDir $localRel
        $localDir = Split-Path $localPath -Parent
        if (-not (Test-Path $localDir)) { New-Item -ItemType Directory -Path $localDir -Force | Out-Null }
        if (Test-Path $localPath) { Write-Host "SKIP (exists): $localPath" -ForegroundColor Yellow; $skipped++; continue }

        Write-Host "Downloading: $url -> $localPath" -ForegroundColor Cyan
        try{
            Invoke-WebRequest -Uri $url -OutFile $localPath -UseBasicParsing -ErrorAction Stop
            Write-Host "Saved: $localPath" -ForegroundColor Green
            $downloaded++
        } catch {
            Write-Host "Failed to download $url : $_" -ForegroundColor Red
            $failed++
        }
    } catch {
        Write-Host "Skipping invalid URL: $url" -ForegroundColor Yellow
        $failed++
    }
}

Write-Host "Download summary: downloaded=$downloaded, skipped=$skipped, failed=$failed" -ForegroundColor Cyan

if ($failed -gt 0) { Write-Host "Some downloads failed. Check network connectivity or the URLs above." -ForegroundColor Red }
else { Write-Host "All assets downloaded or already present." -ForegroundColor Green }
