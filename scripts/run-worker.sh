#!/usr/bin/env sh
# Simple supervisor loop for media_worker.php
set -e
DIR=$(cd "$(dirname "$0")" && pwd)
PHP="php"
while true; do
  echo "Starting media worker at $(date -Is)"
  $PHP "$DIR/media_worker.php" || true
  echo "media worker exited, restarting in 5s..."
  sleep 5
done
