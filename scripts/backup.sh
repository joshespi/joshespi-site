#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"

# shellcheck source=scripts/_env.sh
. "$ROOT_DIR/scripts/_env.sh"
load_env

BACKUP_DIR="$ROOT_DIR/backups"
mkdir -p "$BACKUP_DIR"

KEEP_LAST="${KEEP_LAST:-14}"
STAMP="$(date +%Y%m%d-%H%M%S)"
DB_FILE="$BACKUP_DIR/joshespi-db-$STAMP.sql.gz"
STORAGE_FILE="$BACKUP_DIR/joshespi-storage-$STAMP.tar.gz"

DB_CONNECTION="${DB_CONNECTION:-sqlite}"

echo "[$(date -Is)] Archiving storage/app..."
tar -czf "$STORAGE_FILE" -C "$ROOT_DIR/src" storage/app &
STORAGE_PID=$!

if [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "[$(date -Is)] Dumping SQLite database..."
    SQLITE_FILE="$ROOT_DIR/src/database/database.sqlite"
    if [ ! -f "$SQLITE_FILE" ]; then
        echo "ERROR: SQLite file not found at $SQLITE_FILE" >&2
        kill "$STORAGE_PID" 2>/dev/null || true
        exit 1
    fi
    gzip -c "$SQLITE_FILE" > "$DB_FILE" &
    DB_PID=$!
else
    : "${DB_ROOT_PASSWORD:?DB_ROOT_PASSWORD is not set in .env — uncomment and fill it before running backups}"
    if ! docker ps --format '{{.Names}}' | grep -qx joshespi_db; then
        echo "ERROR: joshespi_db is not running. Bring the stack up first." >&2
        kill "$STORAGE_PID" 2>/dev/null || true
        exit 1
    fi
    echo "[$(date -Is)] Dumping MariaDB database..."
    docker exec joshespi_db \
        mariadb-dump --single-transaction --quick --lock-tables=false \
        -u root -p"$DB_ROOT_PASSWORD" joshespi \
        | gzip -c > "$DB_FILE" &
    DB_PID=$!
fi

wait "$DB_PID"      || { echo "ERROR: database dump failed" >&2; exit 1; }
wait "$STORAGE_PID" || { echo "ERROR: storage archive failed" >&2; exit 1; }

echo "[$(date -Is)] Pruning to last $KEEP_LAST of each..."
ls -1t "$BACKUP_DIR"/joshespi-db-*.sql.gz 2>/dev/null | tail -n +$((KEEP_LAST + 1)) | xargs -r rm --
ls -1t "$BACKUP_DIR"/joshespi-storage-*.tar.gz 2>/dev/null | tail -n +$((KEEP_LAST + 1)) | xargs -r rm --

echo "[$(date -Is)] Done."
echo "  DB:      $DB_FILE"
echo "  Storage: $STORAGE_FILE"
