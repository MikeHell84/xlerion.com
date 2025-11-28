#!/usr/bin/env bash
# Simple deploy script (Unix)
set -euo pipefail
if [ -z "${1:-}" ]; then echo "Usage: $0 user@host [remote_path]"; exit 1; fi
TARGET=$1
REMOTE_PATH=${2:-/var/www/xlerion_cmr}
ARCHIVE=/tmp/deploy_$(date +%Y%m%d_%H%M%S).zip
echo "Creating archive $ARCHIVE..."
zip -r "$ARCHIVE" . -x "./.git/*" "./storage/*"
echo "Uploading to $TARGET:$REMOTE_PATH"
scp "$ARCHIVE" "$TARGET:/tmp/"
ssh "$TARGET" bash -s <<EOF
set -e
sudo mkdir -p $REMOTE_PATH
sudo chown $(whoami):$(whoami) $REMOTE_PATH
rm -rf /tmp/deploy_extract
mkdir -p /tmp/deploy_extract
unzip -o /tmp/$(basename $ARCHIVE) -d /tmp/deploy_extract
rsync -a --delete /tmp/deploy_extract/ $REMOTE_PATH/
cd $REMOTE_PATH
if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi
php scripts/migrate_add_trash_media.php || true
php scripts/migrate_media_tables.php || true
php scripts/apply_analytics_migrations.php || true
mkdir -p storage/logs storage/app public/media/uploads
sudo chown -R www-data:www-data storage public/media
EOF
rm -f "$ARCHIVE"
echo "Deployed to $TARGET"
