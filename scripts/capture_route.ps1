param(
  [Parameter(Mandatory=$true)][string]$route
)

$collector = Join-Path $PSScriptRoot 'collect_artifacts.js'
$uri = "http://127.0.0.1:8080/$route"
Write-Host "Running collector for: $uri"
node $collector $uri
Start-Sleep -Milliseconds 500
$artifactsDir = Join-Path $PSScriptRoot '..\artifacts'
$latest = Get-ChildItem $artifactsDir -Directory | Sort-Object LastWriteTime -Descending | Select-Object -First 1
if ($null -ne $latest) {
  $src = Join-Path $latest.FullName 'screenshot.png'
  if (Test-Path $src) {
    $destDir = Join-Path $PSScriptRoot '..\public\artifacts'
    if (-not (Test-Path $destDir)) { New-Item -ItemType Directory -Path $destDir | Out-Null }
    $dest = Join-Path $destDir ("screenshot-$route.png")
    Copy-Item -Force $src $dest
    Write-Host "Copied to: $dest"
    exit 0
  } else {
    Write-Host "No screenshot found in: $($latest.FullName)"
    exit 2
  }
} else {
  Write-Host "No artifacts directories found"
  exit 3
}
