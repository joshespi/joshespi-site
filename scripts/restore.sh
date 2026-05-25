#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"

BACKUP_DIR="$ROOT_DIR/backups"
STEP_BACK="${1:-0}"

if ! [[ "$STEP_BACK" =~ ^[0-9]+$ ]]; then
  echo "ERROR: argument must be a non-negative integer (steps back from latest)" >&2
  exit 1
fi

INDEX=$((STEP_BACK + 1))
DB_FILE="$(ls -1t "$BACKUP_DIR"/joshespi-db-*.sql.gz 2>/dev/null | sed -n "${INDEX}p" || true)"
STORAGE_FILE="$(ls -1t "$BACKUP_DIR"/joshespi-storage-*.tar.gz 2>/dev/null | sed -n "${INDEX}p" || true)"

if [ -z "$DB_FILE" ] || [ -z "$STORAGE_FILE" ]; then
  echo "ERROR: couldn't find a backup pair $STEP_BACK step(s) back in $BACKUP_DIR" >&2
  exit 1
fi

DB_STAMP="$(basename "$DB_FILE" | sed 's/^joshespi-db-//; s/\.sql\.gz$//')"
STORAGE_STAMP="$(basename "$STORAGE_FILE" | sed 's/^joshespi-storage-//; s/\.tar\.gz$//')"
if [ "$DB_STAMP" != "$STORAGE_STAMP" ]; then
  echo "WARN: DB stamp ($DB_STAMP) and storage stamp ($STORAGE_STAMP) don't match." >&2
  echo "      Pairs may have drifted; review $BACKUP_DIR before continuing." >&2
fi

echo "About to restore (step back: $STEP_BACK):"
echo "  DB:      $DB_FILE"
echo "  Storage: $STORAGE_FILE"
echo "This wipes the current DB contents and src/storage/app."
read -r -p "Continue? [y/N] " ans
[[ "$ans" =~ ^[Yy]$ ]] || { echo "Aborted."; exit 0; }

# shellcheck source=scripts/_env.sh
. "$ROOT_DIR/scripts/_env.sh"
load_env

DB_CONNECTION="${DB_CONNECTION:-sqlite}"

echo "[$(date -Is)] Restoring database..."
if [ "$DB_CONNECTION" = "sqlite" ]; then
    SQLITE_FILE="$ROOT_DIR/src/database/database.sqlite"
    gunzip -c "$DB_FILE" > "$SQLITE_FILE"
else
    : "${DB_ROOT_PASSWORD:?DB_ROOT_PASSWORD is not set in .env — uncomment and fill it before restoring}"
    if ! docker ps --format '{{.Names}}' | grep -qx joshespi_db; then
        echo "ERROR: joshespi_db is not running. Bring the stack up first." >&2
        exit 1
    fi
    gunzip -c "$DB_FILE" \
        | docker exec -i joshespi_db mariadb -u root -p"$DB_ROOT_PASSWORD" joshespi
fi

echo "[$(date -Is)] Restoring storage/app..."
docker compose stop joshespi_app >/dev/null
rm -rf "$ROOT_DIR/src/storage/app"
tar -xzf "$STORAGE_FILE" -C "$ROOT_DIR/src"
docker compose start joshespi_app >/dev/null

echo "[$(date -Is)] Done. Restored from stamp $DB_STAMP."
