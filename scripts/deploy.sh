#!/usr/bin/env bash
# Pulls the repo and brings the running stack up to date. Backs up the database and storage BEFORE
# anything migrates, rebuilds images only when Docker files changed, then (re)starts the app
# container: docker/entrypoint.sh owns composer, npm ci, migrate and the caches, and runs on every
# container start, so a restart is the whole "install + migrate" step. The entrypoint skips the
# Vite build unless the lockfile changed, so the deploy runs `npm run build` itself. Finally
# restarts the scheduler (schedule:work keeps old code loaded).
# Usage: scripts/deploy.sh [--no-pull] [--rebuild] [--skip-backup]
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"

pull=1
force_build=0
backup=1
for arg in "$@"; do
    case "$arg" in
        --no-pull) pull=0 ;;
        --rebuild) force_build=1 ;;
        --skip-backup) backup=0 ;; # emergencies only: a fix must ship and the dump can wait
        *) echo "Usage: $0 [--no-pull] [--rebuild] [--skip-backup]" >&2; exit 2 ;;
    esac
done

if [ ! -f .env ]; then
    echo "No .env found in $ROOT_DIR. Copy .env.example and fill it in first." >&2
    exit 1
fi

# Paths whose change means the images or service definitions need rebuilding.
BUILD_PATHS="Dockerfile docker docker-compose.yml"

before=$(git rev-parse HEAD)
if [ "$pull" = 1 ]; then
    echo "==> git pull"
    git pull --ff-only
fi
after=$(git rev-parse HEAD)

rebuild=$force_build
# shellcheck disable=SC2086
if [ "$before" != "$after" ] && ! git diff --quiet "$before" "$after" -- $BUILD_PATHS; then
    echo "==> Docker files changed since $(git rev-parse --short "$before")"
    rebuild=1
fi

# Safety net before any migration runs. Skipped on a first deploy when there is nothing to dump yet.
if [ "$backup" = 1 ]; then
    if [ -f src/database/database.sqlite ] \
        || docker ps --format '{{.Names}}' | grep -qx joshespi_db; then
        echo "==> pre-deploy backup"
        scripts/backup.sh
    else
        echo "==> no database yet, skipping backup"
    fi
fi

if [ "$rebuild" = 1 ]; then
    echo "==> docker compose up -d --build"
    docker compose up -d --build
else
    echo "==> docker compose up -d"
    docker compose up -d
    # Code is bind-mounted, so unchanged images mean compose leaves the app running as-is.
    # Restarting re-runs the entrypoint (composer, npm, build, migrate, caches) and reloads php-fpm.
    echo "==> restart app (runs entrypoint)"
    docker compose restart joshespi_app
fi

# The entrypoint does the whole install/build/migrate pass before php-fpm starts, so wait on the
# container healthcheck (an fpm socket probe) rather than on "running".
echo "==> waiting for app to become healthy"
tries=0
until [ "$(docker inspect --format '{{.State.Health.Status}}' joshespi_app 2>/dev/null || true)" = "healthy" ]; do
    tries=$((tries + 1))
    if [ "$tries" -gt 100 ]; then
        echo "app did not become healthy; check: docker compose logs joshespi_app" >&2
        exit 1
    fi
    sleep 3
done

# The entrypoint only builds when package-lock.json changed or the manifest is missing, so
# stylesheet/JS/Blade edits would otherwise never reach public/build. Always build here.
echo "==> npm run build"
docker compose exec -T joshespi_app npm run build
docker compose exec -T joshespi_app sh -c 'chown -R www-data:www-data public/build' || true

echo "==> restart scheduler"
docker compose restart joshespi_scheduler

echo "Deployed $(git rev-parse --short HEAD)."
