$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent
$secretsDir = Join-Path $root '.local/secrets'
New-Item -ItemType Directory -Path $secretsDir -Force | Out-Null
if (!(Test-Path (Join-Path $root '.env'))) { Copy-Item (Join-Path $root '.env.example') (Join-Path $root '.env') }
foreach ($name in @('db_password','db_root_password','exporter_password','wp_password','grafana_password')) {
  $path = Join-Path $secretsDir $name
  if (!(Test-Path $path)) {
    $bytes = New-Object byte[] 32
    $rng = [Security.Cryptography.RandomNumberGenerator]::Create()
    $rng.GetBytes($bytes); $rng.Dispose()
    $value = -join ($bytes | ForEach-Object { $_.ToString('x2') })
    [IO.File]::WriteAllText($path, $value, (New-Object Text.UTF8Encoding($false)))
  }
}
$exporter = [IO.File]::ReadAllText((Join-Path $secretsDir 'exporter_password'))
$config = "[client]" + [Environment]::NewLine + "user=exporter" + [Environment]::NewLine + "password=$exporter" + [Environment]::NewLine
[IO.File]::WriteAllText((Join-Path $secretsDir 'exporter.cnf'), $config, (New-Object Text.UTF8Encoding($false)))
Write-Host 'Local configuration ready. Existing secrets preserved.'
