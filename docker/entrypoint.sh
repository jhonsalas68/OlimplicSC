#!/bin/sh
set -e

# Optimizaciones de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ESTO CREARÁ TU ADMIN AUTOMÁTICAMENTE
php artisan migrate --force
php artisan db:seed --force

# Arrancar el servidor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf