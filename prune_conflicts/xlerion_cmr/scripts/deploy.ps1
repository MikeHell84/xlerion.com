# Simple deployment helper for Windows/PowerShell
# Usage: .\scripts\deploy.ps1 -Host xlerion.com -User deploy -RemotePath /var/www/xlerion_cmr -KeyPath C:\keys\id_rsa
param(
  [string]$TargetHost,
  [string]$User = 'deploy',
  [string]$RemotePath = '/var/www/xlerion_cmr',
  [string]$KeyPath = ''
)
if (-not $TargetHost) { Write-Error 'TargetHost is required (-TargetHost)'; exit 1 }
$local = Get-Location
$archive = Join-Path $env:TEMP ('deploy_' + (Get-Date -Format 'yyyyMMdd_HHmmss') + '.zip')
Write-Host "Creating archive $archive..."
Add-Type -AssemblyName System.IO.Compression.FileSystem
[IO.Compression.ZipFile]::CreateFromDirectory($local.Path, $archive)

$scp = 'scp'
$ssh = 'ssh'
if ($KeyPath -ne '') { $scp = "$scp -i $KeyPath"; $ssh = "$ssh -i $KeyPath" }
Write-Host "Uploading to $User@${TargetHost}:$RemotePath..."
& $scp $archive ("$User@${TargetHost}:/tmp/")
Write-Host "Extracting and deploying on remote host..."
$cmd = @"
set -e
sudo mkdir -p $RemotePath
sudo chown ${User}:${User} $RemotePath
rm -rf /tmp/deploy_extract
mkdir -p /tmp/deploy_extract
unzip -o /tmp/$(Split-Path -Leaf $archive) -d /tmp/deploy_extract
rsync -a --delete /tmp/deploy_extract/ $RemotePath/
cd $RemotePath
# install php deps if composer.json exists
if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi
# run migrations if any
php scripts/migrate_add_trash_media.php || true
php scripts/migrate_media_tables.php || true
php scripts/apply_analytics_migrations.php || true
# ensure storage dirs
mkdir -p storage/logs storage/app public/media/uploads
sudo chown -R www-data:www-data storage public/media
"@
& $ssh ("$User@${TargetHost}") $cmd
Write-Host "Deployment finished. Clean up local archive."
Remove-Item $archive -Force
