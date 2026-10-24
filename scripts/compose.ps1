$ErrorActionPreference = 'Stop'
$RepoRoot = Split-Path $PSScriptRoot -Parent
function Invoke-Compose {
  $composeArgs = @('--project-directory', $RepoRoot, '--env-file', (Join-Path $RepoRoot '.env'), '-f', (Join-Path $RepoRoot 'compose.yaml'))
  foreach ($file in @('compose.monitoring.yaml','compose.logging.yaml','compose.lock.yaml')) {
    if (Test-Path (Join-Path $RepoRoot $file)) { $composeArgs += @('-f', (Join-Path $RepoRoot $file)) }
  }
  $plugin = Join-Path $env:LOCALAPPDATA 'Programs/DockerDesktop/resources/bin/docker-compose.exe'
  if (Test-Path $plugin) { & $plugin @composeArgs @args } else { & docker compose @composeArgs @args }
  if ($LASTEXITCODE -ne 0) { throw "Docker Compose failed ($LASTEXITCODE)" }
}
