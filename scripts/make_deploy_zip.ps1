# Create deploy_package.zip excluding development folders
$exclusions = @('.git','node_modules','storage/logs','storage/framework','.vscode')
$items = Get-ChildItem -Force | Where-Object { $exclusions -notcontains $_.Name }
$paths = $items | ForEach-Object { $_.FullName }
if (!$paths) { Write-Error 'No files to archive'; exit 1 }
Compress-Archive -Path $paths -DestinationPath .\deploy_package.zip -Force
if (Test-Path .\deploy_package.zip) { Write-Host 'OK' } else { Write-Error 'ZIP failed' }
