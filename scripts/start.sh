#!/bin/sh
set -eu
sh "$(dirname "$0")/prepare.sh"
. "$(dirname "$0")/compose.sh"
compose config --quiet
compose up -d --wait --wait-timeout 300
compose ps -a
