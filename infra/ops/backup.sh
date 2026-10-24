#!/bin/sh
set -eu
umask 077
id="$(date -u +%Y%m%dT%H%M%SZ)-$(head -c 3 /dev/urandom | od -An -tx1 | tr -d ' \n')"
dest="/backups/$id"
mkdir -p "$dest"
export MYSQL_PWD="$(cat /run/secrets/db_password)"
mysqldump -hmysql -uaurora --single-transaction --no-tablespaces --routines --triggers aurora > "$dest/database.sql"
tar -czf "$dest/uploads.tar.gz" -C /site/wp-content uploads
(cd "$dest"; sha256sum database.sql uploads.tar.gz > SHA256SUMS)
chown -R "$BACKUP_UID:$BACKUP_GID" "$dest"
printf '%s\n' "$id"
