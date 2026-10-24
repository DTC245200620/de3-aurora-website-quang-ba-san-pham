& "$PSScriptRoot/prepare.ps1"
. "$PSScriptRoot/compose.ps1"
Invoke-Compose config --quiet
Invoke-Compose up -d --wait --wait-timeout 300
Invoke-Compose ps -a
Write-Host 'Aurora: http://localhost:8080 | Admin: /wp-admin/ | Grafana: http://localhost:3002'
