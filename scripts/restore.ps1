param([Parameter(Mandatory=$true)][ValidatePattern('^[0-9]{8}T[0-9]{6}Z-[a-f0-9]{6}$')][string]$BackupId, [string]$Confirmation)
$ErrorActionPreference='Stop'
. "$PSScriptRoot/compose.ps1"
if (!(Test-Path (Join-Path $RepoRoot ".local/backups/$BackupId/SHA256SUMS"))) { throw 'Backup not found' }
if (!$Confirmation) { $Confirmation=Read-Host "Type RESTORE to replace Aurora data from $BackupId" }
if ($Confirmation -cne 'RESTORE') { throw 'Restore cancelled' }
try {
  Invoke-Compose stop nginx wordpress phpmyadmin
  Invoke-Compose run --rm -T --no-deps -e "BACKUP_ID=$BackupId" restore
} finally {
  Invoke-Compose up -d --wait --wait-timeout 300
}
