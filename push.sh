#!/usr/bin/env bash
# push.sh - stage, commit, and push to a git remote/branch (Linux/macOS)
#
# Examples:
#   ./push.sh                      # commit with an auto message and push
#   ./push.sh -m "fix: patients"   # commit with a custom message
#   ./push.sh --force              # force-push, replacing remote history
#   ./push.sh --no-pull            # skip pulling before pushing
#   ./push.sh -r origin -b main    # custom remote / branch
#
# If .git is missing or broken it is re-initialized (old .git is moved to
# .git.broken as a backup) and the default remote below is configured.

set -euo pipefail

REPO_URL="https://github.com/sheivon/Hospital-MariaNorori.git"

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

MESSAGE="chore: update $(date '+%Y-%m-%d %H:%M:%S')"
REMOTE="origin"
BRANCH="main"
SKIP_PULL=0
FORCE=0

print_usage() {
  sed -n '2,9p' "$0"
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    -m|--message)  MESSAGE="${2:?message required}"; shift 2 ;;
    -r|--remote)   REMOTE="${2:?remote required}"; shift 2 ;;
    -b|--branch)   BRANCH="${2:?branch required}"; shift 2 ;;
    --no-pull)     SKIP_PULL=1; shift ;;
    -f|--force)    FORCE=1; shift ;;
    -h|--help)     print_usage; exit 0 ;;
    *) echo "Unknown option: $1" >&2; echo "Usage: $0 [-m MESSAGE] [-r REMOTE] [-b BRANCH] [--no-pull] [--force]"; exit 1 ;;
  esac
done

command -v git >/dev/null 2>&1 || { echo "git is not installed or not in PATH." >&2; exit 1; }
command -v php >/dev/null 2>&1 && php -l "app/bootstrap.php" >/dev/null 2>&1 || { echo "warning: php not found; skipping syntax check."; }

# ----- ensure a working git repository ------------------------------------
if git rev-parse --git-dir >/dev/null 2>&1 && [[ -f "$ROOT/.git/HEAD" ]]; then
  HAS_REPO=0
else
  HAS_REPO=1
fi

if [[ $HAS_REPO -ne 0 ]]; then
  echo "Git repository missing or broken. Re-initializing..."
  if [[ -d "$ROOT/.git" ]]; then
    mv "$ROOT/.git" "$ROOT/.git.broken"
  fi
  git init -b "$BRANCH" >/dev/null
  git remote add "$REMOTE" "$REPO_URL"
  echo "Initialized repo with remote $REMOTE -> $REPO_URL"
fi

if ! git remote get-url "$REMOTE" >/dev/null 2>&1; then
  echo "Adding remote $REMOTE -> $REPO_URL"
  git remote add "$REMOTE" "$REPO_URL"
fi

# ----- safety: never push secrets -------------------------------------------
if git check-ignore .env >/dev/null 2>&1; then
  echo "OK: .env is git-ignored."
else
  echo "WARNING: .env is NOT ignored. It may contain secrets." >&2
  echo "Add .env to .gitignore before pushing (see push.ps1 project setup)." >&2
  exit 1
fi

# ----- branch ----------------------------------------------------------------
current="$(git symbolic-ref --short -q HEAD 2>/dev/null || true)"
if [[ "$current" != "$BRANCH" ]]; then
  if git show-ref --verify --quiet "refs/heads/$BRANCH"; then
    git checkout "$BRANCH"
  else
    git checkout -b "$BRANCH"
  fi
fi

if [[ $FORCE -eq 1 || $SKIP_PULL -eq 1 ]]; then
  echo "Skipping pull (--force or --no-pull)."
else
  echo "Pulling latest from $REMOTE/$BRANCH..."
  git pull --rebase "$REMOTE" "$BRANCH" || { echo "Pull failed. Run with --no-pull or resolve conflicts." >&2; exit 1; }
fi

# ----- identity fallback (repo-local, never touches global config) ----------
if ! git config user.name >/dev/null || ! git config user.email >/dev/null; then
  echo "Setting local git identity (you can change it later):"
  git config user.name  "${GIT_USER_NAME:-sheivon}"
  git config user.email "${GIT_USER_EMAIL:-sheivon@users.noreply.github.com}"
  echo "  user.name  = $(git config user.name)"
  echo "  user.email = $(git config user.email)"
fi

# ----- commit -------------------------------------------------------------
if [[ -n "$(git status --porcelain)" ]]; then
  echo "Staging changes..."
  git add -A
  echo "Creating commit: $MESSAGE"
  git commit -m "$MESSAGE"
else
  echo "No local changes to commit. Continuing to push..."
fi

# ----- push ------------------------------------------------------------------
if [[ $FORCE -eq 1 ]]; then
  echo "Force-pushing to $REMOTE/$BRANCH (replaces remote history)..."
  git push --force --set-upstream "$REMOTE" "$BRANCH"
else
  git push --set-upstream "$REMOTE" "$BRANCH"
fi

echo "Done: pushed to $REMOTE/$BRANCH"