# Development commands for Xlerion CMR (PowerShell)

# 1. Create storage dirs
php "./scripts/make_dirs.php"

# 2. Create sqlite DB and run migrations
php "./scripts/run_sqlite_migrate.php"

# 3. Seed content
php "./seed/seed_content.php"

# 4. Start local PHP server (background job)
Start-Job -ScriptBlock { php -S 127.0.0.1:8080 -t "./public" }

# 5. Tail log (if started with redirection)
Get-Content phpdev.log -Wait -Tail 50

# 6. Stop background jobs (server)
Get-Job | Where-Object {$_.Command -like '*php -S*'} | Stop-Job

# 7. Run backup/digest manually
php "./scripts/backup.php"
php "./scripts/digest.php"

# 8. Run site in production-like: use built-in server but with real host binding
php -S 0.0.0.0:8080 -t "./public"

# Notes: Replace php with full path to PHP CLI if necessary in Windows environment.

# Quick create admin (local testing)
# IMPORTANT: For local development the app will auto-fallback to the included
# SQLite DB when `DB_CONNECTION` is not set. To force MySQL, set
# DB_CONNECTION=mysql in your environment and ensure the database exists.
# Example credentials (change immediately in production):
#   Email: admin@xlerion.com
#   Password: ChangeMe123!
# To create locally (will hash the password):
php "./scripts/create_admin.php" "Admin" admin@xlerion.com "ChangeMe123!"
