$files = @(
    'public\patients.php',
    'public\patient.php',
    'public\allergis.php',
    'public\401.php',
    'public\404.php',
    'public\500.php'
)

foreach ($file in $files) {
    $path = Join-Path -Path $PWD -ChildPath $file
    if (Test-Path $path) {
        Remove-Item -LiteralPath $path -Force
        Write-Host "Removed: $file"
    } else {
        Write-Host "Not found: $file"
    }
}
