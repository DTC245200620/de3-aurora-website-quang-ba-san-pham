#!/bin/sh
set -eu
. "$(dirname "$0")/compose.sh"
compose stop
