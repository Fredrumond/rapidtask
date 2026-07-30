#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

echo "Aguardando MySQL..."
until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    sleep 2
done

rm -f bootstrap/cache/packages.php bootstrap/cache/services.php

if [ ! -f vendor/autoload.php ] || [ ! -d vendor/laravel/framework ]; then
    echo "Instalando dependências PHP..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --force
fi

mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    public/avatar \
    public/projetos/arquivos

chown -R www-data:www-data storage bootstrap/cache public/avatar public/projetos
chmod -R 775 storage bootstrap/cache public/avatar public/projetos

php artisan migrate --force

exec "$@"
