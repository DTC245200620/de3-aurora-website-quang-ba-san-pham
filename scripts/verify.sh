#!/bin/sh
set -eu
. "$(dirname "$0")/compose.sh"
compose run --rm -T --no-deps verifier
