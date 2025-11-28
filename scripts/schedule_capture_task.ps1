Param(
  [string]$TaskName = 'Xlerion_Capture_Artefacts',
  [string]$TriggerTime = '03:00',
  [string]$Url = 'http://127.0.0.1:8080/'
)

$script = Join-Path $PSScriptRoot 'run_capture_now.ps1'

# Build the action
$action = New-ScheduledTaskAction -Execute 'PowerShell.exe' -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$script`" -Url `"$Url`""

# Daily trigger at specified time
$timeParts = $TriggerTime.Split(':')
$hour = [int]$timeParts[0]
$minute = [int]$timeParts[1]
$trigger = New-ScheduledTaskTrigger -Daily -At (Get-Date -Hour $hour -Minute $minute -Second 0)

# Register or update the task
try {
  if (Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue) {
    Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false
  }
  Register-ScheduledTask -TaskName $TaskName -Action $action -Trigger $trigger -RunLevel Highest -Description 'Captura periódica de pantallas para Xlerion' -User $env:USERNAME
  Write-Host "Scheduled task '$TaskName' created to run daily at $TriggerTime"
} catch {
  Write-Error "Failed to create scheduled task: $_"
  exit 1
}
