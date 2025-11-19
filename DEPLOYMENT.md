Deployment guide — xlerion.com

Goal
- Deploy the project to xlerion.com and run it under PHP-FPM + Nginx (recommended) or as a simple PHP built-in server for testing.

Server requirements
- Linux (Debian/Ubuntu recommended) or Windows (IIS) — instructions below target Linux.
- PHP 8.0+ with extensions: pdo_mysql, gd, mbstring, fileinfo, json
- Composer (optional, for vendor deps)
- MySQL or MariaDB for production
- ffmpeg (for video transcodes)
- nginx and php-fpm (or Apache + mod_php)

Directory layout recommendation
- Deploy path: /var/www/xlerion_cmr
- public/ should be served as document root
- storage/ must be writable by web server (www-data)

Nginx example (server block)

server {
    listen 80;
    server_name xlerion.com www.xlerion.com;
    root /var/www/xlerion_cmr/public;

    index index.php index.html;

    access_log /var/log/nginx/xlerion_access.log;
    error_log /var/log/nginx/xlerion_error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock; # adjust for your PHP-FPM
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}

Deployment steps (high-level)
1. On your local machine, prepare a production `.env` based on `.env.production.example` and set DB credentials.
2. Upload the project archive or use the provided scripts in `scripts/deploy.sh` or `scripts/deploy.ps1`.
3. On the server: install PHP, composer, and necessary PHP extensions; install ffmpeg; install MySQL and create the production DB and user.
4. Run composer install on the server (if using composer).
5. Ensure `storage/` and `public/media/uploads/` are writable by the web user (e.g., www-data).
6. Run migrations / helper scripts (see below).
7. Start the worker using systemd (use `scripts/systemd.media_worker.service`) or run `php scripts/media_worker.php --once` for testing.

Migrations / DB setup
- The project includes SQL and small PHP migration helpers:
  - `sql/migrations_media.sql` — raw SQL for media tables (SQLite/MySQL variants)
  - `scripts/migrate_media_tables.php` — creates media tables programmatically
  - `scripts/migrate_add_trash_media.php` — adds `deleted_at` and `deleted_by` to `media_files` if needed
  - `scripts/apply_analytics_migrations.php` — creates analytics tables

Running migrations
- php scripts/migrate_media_tables.php
- php scripts/migrate_add_trash_media.php
- php scripts/apply_analytics_migrations.php

Post‑migration (host run)
-------------------------
The following migration run was executed on the target host (base path: /home/xlerionc/public_html/xlerion_cmr):

Script: /home/xlerionc/public_html/xlerion_cmr/scripts/run_sqlite_migrate.php
sqlite migrations executed

Script: /home/xlerionc/public_html/xlerion_cmr/scripts/migrate_media_tables.php
Media tables ensured.

Script: /home/xlerionc/public_html/xlerion_cmr/scripts/migrate_add_trash_media.php
added deleted_at
added deleted_by
Done

Important immediate next steps
------------------------------
- DELETE any web-runner file you uploaded (for example `public/run_setup.php` or `public/run_migrations_include.php`). These files are one-time helpers and are a serious security risk if left on a public webroot.
- Verify the webserver DocumentRoot points to `/home/xlerionc/public_html/xlerion_cmr/public`. If you cannot change the DocumentRoot in the control panel, either ask your host to change it or copy the contents of `public/` into the account root (not ideal).
- Ensure these folders are writable by the webserver user (no `sudo` required from you if the control panel provides an option, else ask hosting support):
  - `/home/xlerionc/public_html/xlerion_cmr/storage`
  - `/home/xlerionc/public_html/xlerion_cmr/bootstrap/cache`
- After the runner deletion and permission check, open the public URL and verify the site responds (load `/`, `/admin` and a few pages). If you get errors, capture the exact error output or the contents of `storage/logs/laravel.log` (or the app log files in `storage/logs`).
- If you used SQLite, confirm the `storage/database.sqlite` file exists and is owned/accessible by the webserver user. If using MySQL, ensure your `.env` file has correct DB_* values.

Security note
-------------
One-time web runners and uploadable installers are convenient but risky. Remove them immediately after use and avoid leaving backup archives in a public folder (delete any `deploy_*.zip` from webroot once extraction is complete).

Worker and background tasks
- Use `scripts/run-worker.sh` or create a systemd unit using `scripts/systemd.media_worker.service` (edit paths/user as necessary).
- The worker respects env vars:
  - MEDIA_WORKER_MAX_ATTEMPTS
  - MEDIA_WORKER_JOB_TIMEOUT

SSL
- Use certbot or your preferred TLS provider to issue certificates and apply them to the nginx server block.

Troubleshooting
- Check PHP-FPM and nginx logs under /var/log.
- Worker logs: storage/logs/media_worker.log
- Metrics: storage/logs/media_worker_metrics.json

If you want, I can:
- Generate a full `systemd` unit with your exact service user and paths.
- Help create a CI pipeline for automatic deploy from GitHub.
