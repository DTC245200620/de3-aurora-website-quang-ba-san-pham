#!/bin/sh
set -eu
REPO_ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
cd "$REPO_ROOT"
compose() {
  docker compose --project-directory "$REPO_ROOT" --env-file "$REPO_ROOT/.env" -f compose.yaml -f compose.monitoring.yaml -f compose.logging.yaml -f compose.tools.yaml -f compose.lock.yaml "$@"
}
