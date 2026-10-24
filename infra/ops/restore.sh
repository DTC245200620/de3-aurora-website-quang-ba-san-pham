#!/bin/sh
set -eu
case "${BACKUP_ID:-}" in *[!0-9a-fTZ-]*|'') echo 'Invalid backup ID' >&2;exit 1;; esac
src="/backups/$BACKUP_ID"
cd "$src"
sha256sum -c SHA256SUMS
tar -tzf uploads.tar.gz | awk '/(^\/|(^|\/)\.\.(\/|$))/ {exit 1} !/^uploads(\/|$)/ {exit 1}'
export MYSQL_PWD="$(cat /run/secrets/db_password)"
mysql -hmysql -uaurora aurora < database.sql
mkdir -p /site/wp-content/uploads
find /site/wp-content/uploads -mindepth 1 -delete
tar -xzf uploads.tar.gz --no-same-owner -C /site/wp-content
chown -R 33:33 /site/wp-content/uploads
echo 'RESTORE PASS'
