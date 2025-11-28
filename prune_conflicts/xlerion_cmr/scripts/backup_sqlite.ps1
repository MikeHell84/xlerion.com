# Backup sqlite DB to storage/backups with timestamp
$root = Split-Path -Parent $PSScriptRoot
$src = Join-Path $root 'storage\database.sqlite'
if (-not (Test-Path $src)) {
    Write-Output 'MISSING'
    exit 2
}
$destDir = Join-Path $root 'storage\backups'
New-Item -ItemType Directory -Path $destDir -Force | Out-Null
$ts = Get-Date -Format 'yyyyMMdd_HHmmss'
$dest = Join-Path $destDir ("database.sqlite.$ts.bak")
Copy-Item -Path $src -Destination $dest -Force
Write-Output $dest
