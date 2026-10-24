#!/bin/sh
set -eu
cd "$(dirname "$0")/.."
umask 077
mkdir -p .local/secrets .local/backups
[ -f .env ] || cp .env.example .env
grep -q '^LOCAL_UID=' .env || printf 'LOCAL_UID=%s\n' "$(id -u)" >> .env
grep -q '^LOCAL_GID=' .env || printf 'LOCAL_GID=%s\n' "$(id -g)" >> .env
for name in db_password db_root_password exporter_password wp_password grafana_password; do
  if [ ! -s ".local/secrets/$name" ]; then
    od -An -N32 -tx1 /dev/urandom | tr -d ' \n' > ".local/secrets/$name"
  fi
done
printf '[client]\nuser=exporter\npassword=%s\n' "$(cat .local/secrets/exporter_password)" > .local/secrets/exporter.cnf
chmod 644 .local/secrets/*
echo 'Local configuration ready. Existing secrets preserved.'
