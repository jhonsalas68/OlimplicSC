#!/bin/sh
set -e

# Optimizaciones de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migraciones
php artisan migrate --force

# Arrancar Supervisor (Esto lanza Nginx y PHP)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf