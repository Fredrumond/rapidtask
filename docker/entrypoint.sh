#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

# Ensure MySQL settings for Docker (compose env overrides at runtime for PHP-FPM children)
if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

php artisan key:generate --force --ansi 2>/dev/null || true

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

# Wait for MySQL then migrate default Laravel tables (sessions/cache/jobs)
ATTEMPTS=0
until php -r "new PDO('mysql:host=mysql;port=3306;dbname=rapidtask', 'rapidtask', 'secret');" 2>/dev/null; do
    ATTEMPTS=$((ATTEMPTS + 1))
    if [ "$ATTEMPTS" -ge 30 ]; then
        echo "MySQL not ready after 30 attempts; continuing without migrate"
        break
    fi
    echo "Waiting for MySQL... ($ATTEMPTS)"
    sleep 2
done

if [ "$ATTEMPTS" -lt 30 ]; then
    php artisan migrate --force --ansi || true
fi

exec "$@"
