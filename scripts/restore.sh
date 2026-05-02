#!/usr/bin/env bash
set -euo pipefail

# Restore the joshespi WordPress stack from a backup pair in ./backups/.
#
# Usage:
#   ./scripts/restore.sh           # latest pair
#   ./scripts/restore.sh 1         # one step back from latest
#   ./scripts/restore.sh 3         # three steps back
#
# Pairs are matched by the YYYYMMDD-HHMMSS stamp in the filename.
# The joshespi_db container must already be running and healthy.

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
WP_FILE="$(ls -1t "$BACKUP_DIR"/joshespi-wp-content-*.tar.gz 2>/dev/null | sed -n "${INDEX}p" || true)"

if [ -z "$DB_FILE" ] || [ -z "$WP_FILE" ]; then
  echo "ERROR: couldn't find a backup pair $STEP_BACK step(s) back in $BACKUP_DIR" >&2
  exit 1
fi

DB_STAMP="$(basename "$DB_FILE" | sed 's/^joshespi-db-//; s/\.sql\.gz$//')"
WP_STAMP="$(basename "$WP_FILE" | sed 's/^joshespi-wp-content-//; s/\.tar\.gz$//')"
if [ "$DB_STAMP" != "$WP_STAMP" ]; then
  echo "WARN: DB stamp ($DB_STAMP) and wp-content stamp ($WP_STAMP) don't match." >&2
  echo "      Pairs may have drifted; review $BACKUP_DIR before continuing." >&2
fi

echo "About to restore (step back: $STEP_BACK):"
echo "  DB:  $DB_FILE"
echo "  WP:  $WP_FILE"
echo "This wipes the current DB contents and ./wp-content."
read -r -p "Continue? [y/N] " ans
[[ "$ans" =~ ^[Yy]$ ]] || { echo "Aborted."; exit 0; }

if [ ! -f .env ]; then
  echo "ERROR: .env not found in $ROOT_DIR" >&2
  exit 1
fi
set -a
# shellcheck disable=SC1091
. ./.env
set +a

if ! docker ps --format '{{.Names}}' | grep -qx joshespi_db; then
  echo "ERROR: joshespi_db is not running. Bring the stack up first." >&2
  exit 1
fi

echo "[$(date -Is)] Restoring database..."
gunzip -c "$DB_FILE" \
  | docker exec -i joshespi_db mariadb -u root -p"$MYSQL_ROOT" joshespi

echo "[$(date -Is)] Restoring wp-content..."
docker compose stop joshespi_web >/dev/null
sudo rm -rf "$ROOT_DIR/wp-content"
sudo tar -xzf "$WP_FILE" -C "$ROOT_DIR"
sudo chown -R 33:33 "$ROOT_DIR/wp-content"
docker compose start joshespi_web >/dev/null

echo "[$(date -Is)] Done. Restored from stamp $DB_STAMP."
echo "If this was a cross-host restore (dev → prod), run:"
echo "  docker exec -u www-data joshespi_web wp search-replace \\"
echo "    'http://OLD-URL' 'https://NEW-URL' --all-tables"
