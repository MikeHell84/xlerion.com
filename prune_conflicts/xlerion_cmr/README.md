Xlerion CMR - Vanilla PHP deployable on cPanel

Quick deploy steps (public_html):
1. Upload entire `xlerion_cmr` folder to your cPanel account. Put contents of `public/` into `public_html/` and keep other files outside public if possible.
2. Create a MySQL database and user in cPanel -> MySQL Databases. Grant all privileges.
3. Copy `.env.example` to `.env` in project root and fill DB_*, MAIL_* and APP_URL.
4. Import SQL: use `sql/migrations.sql` in phpMyAdmin to create tables.
5. Seed content: run `php seed/seed_content.php` from Terminal or via php-cgi in cPanel.
6. Ensure `storage/` and `storage/backups` are writable by the webserver (chown/chmod 755).
7. Configure Cron jobs in cPanel (see `CRON_JOBS.txt`) replacing /home/USER/public_html path.

Mail configuration:
- By default the app uses `sendmail` via PHP `mail()`.
- To use SMTP configure SMTP_* in `.env` and adjust `mail` sending logic.

Security and performance:
- Set restrictive permissions: files 644, dirs 755; storage writable only.
- Enable OPcache in cPanel PHP settings.

For Laravel alternative: see plans earlier in docs.

Run locally (no MySQL required)
1. Copy `.env.example` to `.env` in project root and set DB_CONNECTION=sqlite and DB_DATABASE=storage/database.sqlite
2. Create sqlite file: `php -r "file_exists('storage/database.sqlite')||touch('storage/database.sqlite');"`
3. Import SQLite schema: `sqlite3 storage/database.sqlite < sql/sqlite_migrations.sql`
4. Create storage dirs: `php scripts/make_dirs.php`
5. Seed content: `php seed/seed_content.php`
6. Serve locally: from project root run `php -S localhost:8080 -t public` and open http://localhost:8080

This mode is meant for local testing. For production on cPanel switch to MySQL and import `sql/migrations.sql`.
