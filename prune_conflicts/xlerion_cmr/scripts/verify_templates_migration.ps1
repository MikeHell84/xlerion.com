# Verify templates migration in sqlite
$root = Split-Path -Parent $PSScriptRoot
$db = Join-Path $root 'storage\database.sqlite'
if (-not (Test-Path $db)) { Write-Output 'DB_MISSING'; exit 2 }
Write-Output "DB: $db"
Write-Output "-- schema: templates --"
& sqlite3 $db ".schema templates"
Write-Output "-- schema: template_usage --"
& sqlite3 $db ".schema template_usage"
$tables = & sqlite3 $db "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;"
Write-Output "-- tables list --"
Write-Output $tables
