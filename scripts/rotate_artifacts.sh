#!/usr/bin/env bash
# Rotate artifacts by age and total size. Usage:
# rotate_artifacts.sh [ARTIFACTS_DIR] [MAX_DAYS] [MAX_SIZE_MB]

set -euo pipefail

ARTIFACTS_DIR="${1:-$(cd "$(dirname "${BASH_SOURCE[0]}")/../public/artifacts" && pwd)}"
MAX_DAYS="${2:-30}"
MAX_SIZE_MB="${3:-500}"

log(){ echo "[rotate] $(date '+%Y-%m-%d %H:%M:%S') - $*"; }

if [ ! -d "$ARTIFACTS_DIR" ]; then
  log "Artifacts dir not found: $ARTIFACTS_DIR"
  exit 0
fi

# Delete files older than MAX_DAYS
find "$ARTIFACTS_DIR" -type f -mtime +$MAX_DAYS -print -delete || true

# Ensure total size under MAX_SIZE_MB by deleting oldest files
total_kb=$(du -sk "$ARTIFACTS_DIR" | cut -f1)
max_kb=$((MAX_SIZE_MB * 1024))
if [ "$total_kb" -gt "$max_kb" ]; then
  log "Total size ${total_kb}KB greater than ${max_kb}KB — trimming oldest files"
  # iterate files by oldest first
  while [ "$total_kb" -gt "$max_kb" ]; do
    oldest=$(find "$ARTIFACTS_DIR" -type f -printf '%T@ %p\n' | sort -n | head -n1 | cut -d' ' -f2-)
    if [ -z "$oldest" ]; then break; fi
    rm -f "$oldest"
    log "Removed $oldest"
    total_kb=$(du -sk "$ARTIFACTS_DIR" | cut -f1)
  done
fi
