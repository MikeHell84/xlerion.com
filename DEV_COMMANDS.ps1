# Development commands for Xlerion CMR (PowerShell)

<#
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
#>

# Notes: Replace php with full path to PHP CLI if necessary in Windows environment.

# Quick create admin (local testing)
# IMPORTANT: For local development the app will auto-fallback to the included
# SQLite DB when `DB_CONNECTION` is not set. To force MySQL, set
# DB_CONNECTION=mysql in your environment and ensure the database exists.
# Example credentials (change immediately in production):
#   Email: admin@xlerion.com
#   Password: ChangeMe123!
# To create locally (will hash the password):
# php "./scripts/create_admin.php" "Admin" admin@xlerion.com "ChangeMe123!"

# --- Helpful PowerShell functions for dev server management -----------------
# Usage examples (uncomment to use):
# Start-DevServer -Port 8080 -Bind 0.0.0.0 -PublicPath "./public" -Background
# Stop-DevServer
# Show-DevServerLogs -Tail 200

function Get-PHPExecutable {
	# Return php executable path or 'php' if not found explicitly
	$php = Get-Command php -ErrorAction SilentlyContinue
	if ($php) { return $php.Source }
	# try common locations on Windows (user may adjust)
	$candidates = @("C:\\php\\php.exe", "C:\\Program Files\\php\\php.exe")
	foreach ($c in $candidates) { if (Test-Path $c) { return $c } }
	return 'php'
}

function Start-DevServer {
	[CmdletBinding()]
	param(
		[int]$Port = 8080,
	# Default bind changed to 0.0.0.0 to make the server reachable from other devices on the LAN.
	# Use 127.0.0.1 explicitly when you want loopback-only (safer for local development).
	[string]$Bind = '0.0.0.0',
		[string]$PublicPath = './public',
	[switch]$Background = $true,
		[string]$LogFile = 'phpdev.log',
		[hashtable]$EnvVars = @{ 'DB_CONNECTION' = 'sqlite' }
	)
	$php = Get-PHPExecutable
	$absPublic = Resolve-Path -LiteralPath $PublicPath -ErrorAction SilentlyContinue
	if (-not $absPublic) { Write-Error "Public path '$PublicPath' not found."; return }
	$pub = $absPublic.Path
	$cmd = "$php -S $Bind`:$Port -t `"$pub`""
	if ($Background) {
		Write-Host "Starting PHP dev server in background on ${Bind}:${Port} (log -> $LogFile)"
		# Stop any existing php processes that look like dev servers
		Get-Process php -ErrorAction SilentlyContinue | Where-Object { $_.Path -like '*' } | ForEach-Object { Try { Stop-Process -Id $_.Id -Force -ErrorAction SilentlyContinue } Catch {} }
		# Compose absolute log path
		$scriptDir = $PSScriptRoot
		$logPath = if ([System.IO.Path]::IsPathRooted($LogFile)) { $LogFile } else { Join-Path $scriptDir $LogFile }
		# Build environment assignments for the new pwsh process
		$envSet = ''
		if ($EnvVars) { $envSet = ($EnvVars.GetEnumerator() | ForEach-Object { "`$env:$($_.Key) = '$($_.Value)';" }) -join ' ' }
		# Inner command executed by the new pwsh: set env vars and run php, redirecting output to the log
		$inner = "$envSet php -S '$Bind`:$Port' -t '$pub' 2>&1 | Out-File -FilePath '$logPath' -Append -Encoding utf8"
		$psExe = (Get-Command pwsh -ErrorAction SilentlyContinue | Select-Object -First 1).Source
		if (-not $psExe) { $psExe = Join-Path $PSHome 'pwsh.exe' }
		$args = "-NoProfile -Command & { $inner }"
		Write-Host "Spawning pwsh to run php dev server; log -> $logPath"
		Start-Process -FilePath $psExe -ArgumentList $args -WindowStyle Hidden | Out-Null
		Start-Sleep -Milliseconds 400
		Get-Process php -ErrorAction SilentlyContinue | Select-Object Id, ProcessName | Format-Table -AutoSize
	} else {
		Write-Host "Starting PHP dev server on ${Bind}:${Port} (foreground)"
		# set environment for this process run
		foreach ($k in $EnvVars.Keys) { ${env:$k} = $EnvVars[$k] }
		& $php -S "$Bind`:$Port" -t $pub
	}
}

function Stop-DevServer {
	# Try to stop background pwsh instances launched by the helper first (they set php with a redirected log)
	$pwshProcs = Get-Process pwsh -ErrorAction SilentlyContinue
	if ($pwshProcs) {
		foreach ($p in $pwshProcs) {
			try {
				$cmdline = (Get-CimInstance Win32_Process -Filter "ProcessId=$($p.Id)").CommandLine
				if ($cmdline -and $cmdline -match 'php -S') {
					Write-Host "Stopping helper pwsh process Id=$($p.Id)"
					Stop-Process -Id $p.Id -Force -ErrorAction SilentlyContinue
				}
			} catch {
				# ignore
			}
		}
	}

	# As a fallback, stop php processes that look like dev servers (careful: may stop other php tasks)
	$phpProcs = Get-Process php -ErrorAction SilentlyContinue
	if ($phpProcs) {
		foreach ($pp in $phpProcs) {
			try {
				$cmdline = (Get-CimInstance Win32_Process -Filter "ProcessId=$($pp.Id)").CommandLine
				if ($cmdline -and $cmdline -match '-S') {
					Write-Host "Stopping php process Id=$($pp.Id) (likely dev server)"
					Stop-Process -Id $pp.Id -Force -ErrorAction SilentlyContinue
				}
			} catch {
				# ignore
			}
		}
		Write-Host "Stopped php dev server processes."
	} else {
		Write-Host "No php dev server processes found."
	}
}

function Show-DevServerLogs {
	param([int]$Tail = 100, [string]$LogFile = 'phpdev.log')
	if (-not (Test-Path $LogFile)) { Write-Host "Log file '$LogFile' not found."; return }
	Get-Content $LogFile -Tail $Tail -Wait
}

# End helper functions
