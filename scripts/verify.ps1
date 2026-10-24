. "$PSScriptRoot/compose.ps1"
Invoke-Compose run --rm -T --no-deps verifier
$items = @(Invoke-Compose ps -a --format json | ForEach-Object { $_ | ConvertFrom-Json })
foreach ($item in $items) {
  if ($item.Service -in @('wordpress-init','log-init','positions-init')) {
    if ($item.ExitCode -ne 0) { throw "Init failed: $($item.Service)" }
  } elseif ($item.State -ne 'running') { throw "Service not running: $($item.Service)" }
}
Write-Host 'PASS container lifecycle'
