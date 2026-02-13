#!/usr/bin/env pwsh
Write-Host "== Stock-Intelligent MySQL diagnostics =="

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
Write-Host "Project root: $root" -ForegroundColor Cyan

function Show-Header($t) { Write-Host "`n---- $t ----`n" -ForegroundColor Yellow }

Show-Header "Attempt to start Laragon (if installed at C:\laragon)"
if (Test-Path 'C:\laragon\laragon.exe') {
    Try {
        Start-Process 'C:\laragon\laragon.exe' -WindowStyle Minimized -ErrorAction Stop
        Write-Host "Laragon process started (or already running)." -ForegroundColor Green
    } Catch {
        Write-Host "Failed to start Laragon: $_" -ForegroundColor Red
    }
} else {
    Write-Host "Laragon not found at C:\laragon\laragon.exe. Skipping start." -ForegroundColor DarkYellow
}

Show-Header "Check mysqld process"
$mysqld = Get-Process -Name mysqld -ErrorAction SilentlyContinue
if ($mysqld) { $mysqld | Format-Table Id, ProcessName, CPU, WS -AutoSize } else { Write-Host "No mysqld process found." -ForegroundColor Red }

Show-Header "Check TCP port 3306"
Try { Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -InformationLevel Detailed } Catch { Write-Host "Test-NetConnection failed: $_" -ForegroundColor Red }

Show-Header "Netstat (listening entries for :3306)"
cmd /c "netstat -ano | findstr :3306" | ForEach-Object { Write-Host $_ }

Show-Header "Read .env DB settings (masked)"
$envPath = Join-Path $root '.env'
if (Test-Path $envPath) {
    $lines = Get-Content $envPath | Where-Object { $_ -match '^DB_' }
    foreach ($l in $lines) {
        if ($l -match '^DB_PASSWORD=') {
            $val = $l -replace '^DB_PASSWORD=', ''
            if ($val) { $masked = $val.Substring(0,1) + ('*' * ([Math]::Max(0,$val.Length-2))) + ($val.Substring($val.Length-1)) } else { $masked = '' }
            Write-Host "DB_PASSWORD=$masked"
        } else {
            Write-Host $l
        }
    }
} else { Write-Host ".env not found at $envPath" -ForegroundColor Red }

Show-Header "Search Laragon MySQL logs"
$possible = @('C:\laragon\data\mysql','C:\laragon\bin\mysql')
$found = @()
foreach ($p in $possible) {
    if (Test-Path $p) {
        $found += Get-ChildItem -Path $p -Recurse -Include *.err,*.log -ErrorAction SilentlyContinue | Select-Object -First 5
    }
}
if ($found.Count -gt 0) {
    foreach ($f in $found) {
        Write-Host "\n--- Log: $($f.FullName) (last 200 lines) ---" -ForegroundColor Cyan
        Try { Get-Content $f.FullName -Tail 200 } Catch { Write-Host "Cannot read $($f.FullName): $_" -ForegroundColor Red }
    }
} else { Write-Host "No Laragon MySQL logs found in common locations." -ForegroundColor DarkYellow }

Show-Header "Done. Copy the above output and paste it back to me for analysis."
