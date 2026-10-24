#!/bin/sh
set -eu
. "$(dirname "$0")/compose.sh"
id="${1:-}"
printf '%s' "$id" | grep -Eq '^[0-9]{8}T[0-9]{6}Z-[a-f0-9]{6}$' || { echo 'Invalid backup ID'; exit 1; }
[ -f ".local/backups/$id/SHA256SUMS" ] || { echo 'Backup not found'; exit 1; }
printf 'Type RESTORE to replace Aurora data: '
read -r confirmation
[ "$confirmation" = RESTORE ] || exit 1
trap 'compose up -d --wait --wait-timeout 300' EXIT
compose stop nginx wordpress phpmyadmin
compose run --rm -T --no-deps -e "BACKUP_ID=$id" restore
