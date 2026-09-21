#!/usr/bin/env bash
# run.sh - start/stop the application for local dev (Linux/macOS)
#
# Usage examples:
#   ./run.sh            # start PHP builtin server on a free port (default 8000) and open browser
#   ./run.sh -p 8080    # start on port 8080 (or next free port if taken)
#   ./run.sh --no-open  # don't open the browser automatically
#   ./run.sh stop       # stop the server started by this script (via pid file)

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PID_FILE="$ROOT/.run.sh.pid"

PORT=8000
OPEN_BROWSER=1

print_usage() {
  sed -n '2,7p' "$0"
}

port_free() {
  local p="$1"
  if command -v ss >/dev/null 2>&1; then
    ! ss -tln | awk '{print $4}' | grep -E "(^|:)$p\$" >/dev/null
  elif command -v netstat >/dev/null 2>&1; then
    ! netstat -tln 2>/dev/null | awk '{print $4}' | grep -E "(^|:)$p\$" >/dev/null
  else
    ! (exec 3<>"/dev/tcp/localhost/$p") 2>/dev/null
  fi
}

stop_server() {
  if [[ -f "$PID_FILE" ]]; then
    local pid
    pid="$(cat "$PID_FILE")"
    if kill -0 "$pid" 2>/dev/null; then
      echo "Stopping server (pid $pid)."
      kill "$pid"
      rm -f "$PID_FILE"
      return 0
    else
      echo "Stale pid file detected (process $pid not running). Removing."
      rm -f "$PID_FILE"
    fi
  fi
  # Fall back to killing any php server from this project's public dir
  local pids
  pids="$(pgrep -f "php -S .* -t .*$ROOT/public" || true)"
  if [[ -n "$pids" ]]; then
    echo "Stopping: $pids"
    pkill -f "php -S .* -t .*$ROOT/public"
  else
    echo "No running server found."
  fi
}

ACTION="start"
while [[ $# -gt 0 ]]; do
  case "$1" in
    stop)
      ACTION="stop"
      shift
      ;;
    -p|--port)
      PORT="${2:?port required}"
      shift 2
      ;;
    -n|--no-open)
      OPEN_BROWSER=0
      shift
      ;;
    -h|--help)
      print_usage
      exit 0
      ;;
    *)
      echo "Unknown option: $1" >&2
      echo "Usage: $0 [stop] [-p PORT] [--no-open]" >&2
      exit 1
      ;;
  esac
done

if [[ "$ACTION" == "stop" ]]; then
  stop_server
  exit 0
fi

# If an instance from this script is already running, stop it first.
if [[ -f "$PID_FILE" ]] && kill -0 "$(cat "$PID_FILE")" 2>/dev/null; then
  echo "An instance is already running (pid $(cat "$PID_FILE")). Stopping it first."
  stop_server
  sleep 1
fi

echo "Project root: $ROOT"

# Locate php binary
PHP_CMD="$(command -v php || true)"
if [[ -z "$PHP_CMD" ]]; then
  for candidate in /opt/lampp/bin/php /usr/local/xampp/php/php; do
    if [[ -x "$candidate" ]]; then
      PHP_CMD="$candidate"
      break
    fi
  done
fi

if [[ -z "$PHP_CMD" ]]; then
  echo "php executable not found. Install PHP 8.0+ and re-run." >&2
  exit 1
fi

# Verify pdo_mysql driver (needed by the app)
if ! "$PHP_CMD" -r "exit(extension_loaded('pdo_mysql') ? 0 : 1);" 2>/dev/null; then
  echo "Warning: PHP ($PHP_CMD) does not have pdo_mysql enabled." >&2
  echo "Enable extension=pdo_mysql in your php.ini and restart, then run again." >&2
fi

# Pick first free port starting at $PORT
while ! port_free "$PORT"; do
  echo "Port $PORT is in use, trying $((PORT + 1)) ..."
  PORT=$((PORT + 1))
done

DOC_ROOT="$ROOT/public"
ROUTER="$DOC_ROOT/index.php"
URI="http://localhost:$PORT/"

echo "Starting PHP built-in server on $URI (serving public) ..."
echo "  $PHP_CMD -S localhost:$PORT -t \"$DOC_ROOT\" \"$ROUTER\""
nohup "$PHP_CMD" -S "localhost:$PORT" -t "$DOC_ROOT" "$ROUTER" >"$ROOT/.run.sh.log" 2>&1 &
echo $! > "$PID_FILE"
sleep 1

if ! port_free "$PORT"; then
  echo "Server is up on $URI"
  if [[ "$OPEN_BROWSER" == "1" ]]; then
    echo "Opening $URI"
    (xdg-open "$URI" >/dev/null 2>&1 || open "$URI" >/dev/null 2>&1 || true) &
  fi
  echo "Log: .run.sh.log  |  Stop with: ./run.sh stop"
else
  echo "Server failed to start. See .run.sh.log:" >&2
  cat "$ROOT/.run.sh.log" >&2
  rm -f "$PID_FILE"
  exit 1
fi