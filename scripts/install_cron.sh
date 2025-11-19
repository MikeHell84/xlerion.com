#!/usr/bin/env bash
# Helper to install a cron job for captures on a Linux server (cPanel-compatible)
# Usage: install_cron.sh [CRON_SPEC] [URL]
# Example: install_cron.sh '0 3 * * *' 'http://127.0.0.1:8080/'

set -euo pipefail

CRON_SPEC="${1:-0 3 * * *}"
URL="${2:-http://127.0.0.1:8080/}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
RUNNER="${SCRIPT_DIR}/run_capture_linux.sh"
ROTATOR="${SCRIPT_DIR}/rotate_artifacts.sh"

if [ ! -x "$RUNNER" ]; then
  echo "Runner not executable: $RUNNER" >&2
  exit 1
fi

CRON_CMD="${RUNNER} ${URL} && ${ROTATOR}"

tmpfile=$(mktemp)
crontab -l 2>/dev/null | grep -v -F "$RUNNER" > "$tmpfile" || true
echo "$CRON_SPEC $CRON_CMD" >> "$tmpfile"
crontab "$tmpfile"
rm -f "$tmpfile"
echo "Installed cron: $CRON_SPEC $CRON_CMD"
