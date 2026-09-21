<#
push.ps1 - stage, commit, and push to a git remote/branch

Examples:
  .\push.ps1 -Message "fix: patients loading state"
  .\push.ps1 -Message "chore: update" -Remote origin -Branch main
  .\push.ps1 -Message "quick fix" -SkipPull
#>

param(
    [string]$Message = "chore: update $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')",
    [string]$Remote = "origin",
    [string]$Branch = "main",
    [switch]$SkipPull
)

$ErrorActionPreference = 'Stop'

function Run-Git {
    param(
        [Parameter(Mandatory = $true)]
        [string[]]$Args
    )

    & git @Args
    if ($LASTEXITCODE -ne 0) {
        throw "git $($Args -join ' ') failed"
    }
}

try {
    if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
        throw "git is not installed or not in PATH."
    }

    $insideRepo = (& git rev-parse --is-inside-work-tree 2>$null)
    if ($LASTEXITCODE -ne 0 -or $insideRepo -ne 'true') {
        throw "Current directory is not a git repository."
    }

    $currentBranch = (& git rev-parse --abbrev-ref HEAD).Trim()
    if ($currentBranch -ne $Branch) {
        Write-Host "Switching branch: $currentBranch -> $Branch" -ForegroundColor Yellow
        & git checkout $Branch 2>$null
        if ($LASTEXITCODE -ne 0) {
            Write-Host "Branch '$Branch' does not exist locally. Creating it from current HEAD." -ForegroundColor Yellow
            Run-Git -Args @('checkout', '-b', $Branch)
        }
    }

    if (-not $SkipPull) {
        Write-Host "Pulling latest from $Remote/$Branch..." -ForegroundColor Cyan
        & git pull --rebase $Remote $Branch
        if ($LASTEXITCODE -ne 0) {
            throw "Pull failed. Resolve conflicts or run with -SkipPull."
        }
    }

    $status = (& git status --porcelain)
    if ($status) {
        Write-Host "Staging changes..." -ForegroundColor Cyan
        Run-Git -Args @('add', '-A')

        $staged = (& git diff --cached --name-only)
        if ($staged) {
            Write-Host "Creating commit..." -ForegroundColor Cyan
            Run-Git -Args @('commit', '-m', $Message)
        }
    } else {
        Write-Host "No local changes to commit. Continuing to push..." -ForegroundColor Yellow
    }

    Write-Host "Pushing to $Remote/$Branch..." -ForegroundColor Cyan
    Run-Git -Args @('push', '-u', $Remote, $Branch)

    Write-Host "Done: pushed to $Remote/$Branch" -ForegroundColor Green
}
catch {
    Write-Error $_.Exception.Message
    exit 1
}
