$ErrorActionPreference='Stop'
. "$PSScriptRoot/compose.ps1"
New-Item -ItemType Directory -Path (Join-Path $RepoRoot '.local/backups') -Force | Out-Null
try {
  Invoke-Compose stop nginx wordpress phpmyadmin
  Invoke-Compose run --rm -T --no-deps backup
} finally {
  Invoke-Compose up -d --wait --wait-timeout 300
}
