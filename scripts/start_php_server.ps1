param(
    [Parameter(Mandatory=$true)][string]$PublicPath,
    [string]$Bind = '127.0.0.1',
    [int]$Port = 8080,
    [hashtable]$EnvVars,
    [string]$LogFile = 'phpdev.log'
)

# Validate public path
if (-not (Test-Path $PublicPath)) {
    Write-Error "PublicPath '$PublicPath' does not exist"
    exit 3
}

# Apply environment variables for this process (child php will inherit)
if ($EnvVars) {
    foreach ($k in $EnvVars.Keys) {
        ${env:$k} = $EnvVars[$k]
        Write-Host "Set env $k=$($EnvVars[$k])"
    }
}

 $cmd = "php -S $Bind`:$Port -t `"$PublicPath`""
 Write-Host "Launching: $cmd"

# Ensure log file path is absolute (relative to script path)
if (-not (Test-Path (Split-Path -Parent $LogFile))) {
    # If LogFile is relative, make it relative to the script's parent folder
    $scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
    $LogFile = Join-Path $scriptDir $LogFile
}

# Launch PHP built-in server; redirect stdout/stderr to log so the background job writes to disk
& php -S "$Bind`:$Port" -t "$PublicPath" 2>&1 | Out-File -FilePath $LogFile -Append -Encoding utf8
