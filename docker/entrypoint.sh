#!/bin/sh
set -e

APP_ENV="${APP_ENV:-local}"
STAMP_DIR="storage/framework/.stamps"
mkdir -p "$STAMP_DIR"

# Re-run an install step only when its lockfile actually changed. Guarding on
# "does vendor/ exist" instead meant a redeploy that added a dependency silently
# shipped without it.
stale() {
    lock="$1"; stamp="$STAMP_DIR/$2"
    [ -f "$lock" ] || return 1
    new=$(md5sum "$lock" | cut -d' ' -f1)
    [ -f "$stamp" ] && [ "$(cat "$stamp")" = "$new" ] && return 1
    return 0
}
mark() { md5sum "$1" | cut -d' ' -f1 > "$STAMP_DIR/$2"; }

if stale composer.lock composer || [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] composer install"
    # --no-dev only in production. vendor/ lives on the bind mount, so using it
    # everywhere would delete phpunit out from under the working copy the first
    # time the container booted and leave the host unable to run the suite.
    if [ "$APP_ENV" = "production" ]; then
        composer install --no-dev --optimize-autoloader --no-interaction
    else
        composer install --optimize-autoloader --no-interaction
    fi
    mark composer.lock composer
fi

# public/build is gitignored and nothing else builds it, so a fresh clone used to
# deploy with no Vite manifest and 500 on every page. This is why node is in the
# image at all now that the separate node container is gone.
if stale package-lock.json node || [ ! -f public/build/manifest.json ]; then
    echo "[entrypoint] npm ci && npm run build"
    npm ci --no-audit --no-fund
    npm run build
    mark package-lock.json node
fi

# 775 + www-data, never 777: world-writable storage/ lets any process in the
# container drop a compiled Blade view that PHP will then execute, and the bind
# mount carries that back out to the host. Tolerated as a no-op on Docker
# Desktop bind mounts, which do not carry POSIX ownership at all.
chown -R www-data:www-data storage bootstrap/cache database 2>/dev/null \
    || echo "[entrypoint] chown skipped — mount does not support ownership"
chmod -R 775 storage bootstrap/cache database 2>/dev/null \
    || echo "[entrypoint] chmod skipped — mount does not support modes"

# sessions live in the database by default, so an unmigrated schema 500s every
# Livewire request. Retry briefly: the DB is on the host and compose has no
# readiness gate for it.
echo "[entrypoint] migrate"
i=1
until php artisan migrate --force --no-interaction; do
    [ "$i" -ge 10 ] && echo "[entrypoint] database unreachable after 10 tries" && exit 1
    echo "[entrypoint] database not ready (attempt $i), retrying in 3s"
    i=$((i + 1))
    sleep 3
done

if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    # Stale caches from a previous production run would otherwise pin old config.
    php artisan optimize:clear
fi

exec "$@"
