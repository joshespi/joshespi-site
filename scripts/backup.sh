#!/usr/bin/env bash
set -euo pipefail

# Backs up the joshespi WordPress stack:
#   - MariaDB dump from the joshespi_db container
#   - Tarball of ./wp-content
# Output goes to ./backups/ as a timestamped pair, then prunes to KEEP_LAST.

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"

BACKUP_DIR="$ROOT_DIR/backups"
mkdir -p "$BACKUP_DIR"

KEEP_LAST="${KEEP_LAST:-14}"
STAMP="$(date +%Y%m%d-%H%M%S)"
DB_FILE="$BACKUP_DIR/joshespi-db-$STAMP.sql.gz"
WP_FILE="$BACKUP_DIR/joshespi-wp-content-$STAMP.tar.gz"

# Load DB creds from .env
if [ ! -f .env ]; then
  echo "ERROR: .env not found in $ROOT_DIR" >&2
  exit 1
fi
set -a
# shellcheck disable=SC1091
. ./.env
set +a

echo "[$(date -Is)] Dumping database..."
docker exec joshespi_db \
  mariadb-dump --single-transaction --quick --lock-tables=false \
  -u root -p"$MYSQL_ROOT" joshespi \
  | gzip -c > "$DB_FILE"

echo "[$(date -Is)] Archiving wp-content..."
tar -czf "$WP_FILE" -C "$ROOT_DIR" wp-content

echo "[$(date -Is)] Pruning to last $KEEP_LAST of each..."
ls -1t "$BACKUP_DIR"/joshespi-db-*.sql.gz 2>/dev/null | tail -n +$((KEEP_LAST + 1)) | xargs -r rm --
ls -1t "$BACKUP_DIR"/joshespi-wp-content-*.tar.gz 2>/dev/null | tail -n +$((KEEP_LAST + 1)) | xargs -r rm --

echo "[$(date -Is)] Done."
echo "  DB:  $DB_FILE"
echo "  WP:  $WP_FILE"
