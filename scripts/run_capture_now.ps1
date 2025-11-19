Param(
  [string]$Url = 'http://127.0.0.1:8080/'
)

$node = (Get-Command node -ErrorAction SilentlyContinue)
if (-not $node) { Write-Error 'node not found in PATH'; exit 1 }

$script = Join-Path $PSScriptRoot 'capture_with_retry.js'
Write-Host "Running capture for $Url"
& node $script $Url
