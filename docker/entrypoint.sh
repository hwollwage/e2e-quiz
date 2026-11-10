#!/bin/sh
set -e

if [ -d /shared-public ]; then
    echo "[entrypoint] Syncing public assets to shared volume..."
    cp -r /var/www/html/public/. /shared-public/
    echo "[entrypoint] Sync complete."
fi

if [ "$APP_ENV" = "production" ]; then
    php artisan optimize
fi

exec "$@"
