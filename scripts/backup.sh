#!/bin/sh
set -eu
. "$(dirname "$0")/compose.sh"
mkdir -p .local/backups
trap 'compose up -d --wait --wait-timeout 300' EXIT
compose stop nginx wordpress phpmyadmin
compose run --rm -T --no-deps backup
