#!/usr/bin/env bash
# Runner for Linux / cPanel environments: runs Node capture_with_retry.js when Node is available.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="${SCRIPT_DIR%/*}"
CAPTURE_SCRIPT="${SCRIPT_DIR}/capture_with_retry.js"

URL="${1:-http://127.0.0.1:8080/}"

log() { echo "[run_capture_linux] $(date '+%Y-%m-%d %H:%M:%S') - $*"; }

if command -v node >/dev/null 2>&1; then
  log "Node found: running ${CAPTURE_SCRIPT} for ${URL}"
  node "$CAPTURE_SCRIPT" "$URL"
  EXIT=$?
  if [ $EXIT -ne 0 ]; then
    log "capture script exited with $EXIT"
    exit $EXIT
  fi
  log "capture completed"
  # regenerate index
  if command -v node >/dev/null 2>&1; then
    node "${SCRIPT_DIR}/generate_artifacts_index.js" || true
  fi
else
  log "Node not found in PATH — cannot run headless collector."
  log "Fallback: attempt simple HTTP GET to warm cache (no screenshot)"
  if command -v curl >/dev/null 2>&1; then
    curl -sSf --max-time 30 "$URL" >/dev/null || log "curl failed to fetch $URL"
  else
    log "curl not available either. Install Node or curl to enable captures."
    exit 2
  fi
fi
