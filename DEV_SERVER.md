DEV server background initialization

Purpose

This document explains how to start the PHP built-in development server for this project in background mode, how to ensure it uses the local SQLite DB by default, how to stop it, and how to troubleshoot common issues (binding, logs, IPv6 vs IPv4).

Files

- `DEV_COMMANDS.ps1` (PowerShell helper): provides `Start-DevServer`, `Stop-DevServer`, `Show-DevServerLogs` functions.
- `scripts/start_php_server.ps1` (helper): optional wrapper that sets environment variables and launches the PHP built-in server with output redirected to `phpdev.log`.

Usage

Dot-source the helper and use the functions from PowerShell (run from project root):

```powershell
# dot-source helpers
. .\DEV_COMMANDS.ps1

# Start background server (bind to loopback only)
Start-DevServer -Port 8080 -Bind 127.0.0.1 -PublicPath '.\public' -Background -EnvVars @{ 'DB_CONNECTION' = 'sqlite' }

# Start background server listening on all IPv4 interfaces (for LAN testing)
Start-DevServer -Port 8080 -Bind 0.0.0.0 -PublicPath '.\public' -Background -EnvVars @{ 'DB_CONNECTION' = 'sqlite' }

# Start in foreground (useful for debugging)
Start-DevServer -Port 8080 -Bind 127.0.0.1 -PublicPath '.\public'

# Stop running php dev servers started by the helpers
Stop-DevServer

# Tail logs
Show-DevServerLogs -Tail 200
```

Recommendations and notes

- Default behavior: the helpers set `DB_CONNECTION=sqlite` by default when starting the server so the app uses local `storage/database.sqlite` without requiring MySQL.
- Binding: if you cannot access the site using `http://localhost:8080`, try `http://127.0.0.1:8080` (some systems prefer IPv6 for `localhost` which can fail if the server only listens on IPv4).
- For LAN access, use `-Bind 0.0.0.0` and ensure Windows Firewall allows incoming connections on the chosen port.
- The background server is started as a separate PowerShell process that sets env vars and redirects stdout/stderr to `phpdev.log` in the project root.

Troubleshooting

- php not found: edit `Get-PHPExecutable` in `DEV_COMMANDS.ps1` to return the full path to your `php.exe`.
- If you still see PDO MySQL errors in `phpdev.log`, make sure `DB_CONNECTION` wasn't explicitly set globally in your environment to `mysql` and that the `Start-DevServer` invocation included the `-EnvVars` hashtable.
- To inspect listening sockets:

```powershell
netstat -ano | Select-String ':8080' -SimpleMatch
Get-Process php -ErrorAction SilentlyContinue
```

- Hostname resolution:

```powershell
[System.Net.Dns]::GetHostAddresses('localhost')
```

Security

- Never expose the development server to the public internet. Use `127.0.0.1` for safe local development. If you must use `0.0.0.0` for LAN testing, restrict access via firewall rules.

Appendix: example Start-DevServer function contract

- Inputs: Port (int), Bind (string), PublicPath (string), Background (switch), LogFile (string), EnvVars (hashtable)
- Output: background process or foreground php process; log file written to `phpdev.log` when started in background.
- Errors: missing PublicPath, php not found, port already in use.


