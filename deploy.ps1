# deploy.ps1 - Build, commit changes, push to main, and run the application
# Usage: .\deploy.ps1

$ErrorActionPreference = 'Stop'

Write-Host "Starting deployment process..." -ForegroundColor Green

# Step 1: Build the project
Write-Host "Building the project..." -ForegroundColor Cyan
& ".\build.ps1"

# Step 2: Add any new or modified files to Git
Write-Host "Adding files to Git..." -ForegroundColor Cyan
git add .

# Step 3: Commit changes if there are any
$status = git status --porcelain
if ($status) {
    Write-Host "Committing changes..." -ForegroundColor Cyan
    git commit -m "Deploy update: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
} else {
    Write-Host "No changes to commit." -ForegroundColor Yellow
}

# Step 4: Push to main branch
Write-Host "Pushing to main branch..." -ForegroundColor Cyan
git push origin main

# Step 5: Run the application
Write-Host "Starting the application..." -ForegroundColor Cyan
& ".\run.ps1"

Write-Host "Deployment complete!" -ForegroundColor Green