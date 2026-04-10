#!/bin/sh
set -e

# Optimizaciones de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ESTO CREARÁ TU ADMIN AUTOMÁTICAMENTE
php artisan migrate --force
php artisan db:seed --force

# CORRECCIÓN VITAL PARA POSTGRESQL Y LOGS EN RAILWAY/DOCKER
# Asignar propiedad a www-data (el usuario del servidor web) para que pueda leer/escribir en la base de datos y logs.
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

# Arrancar el servidor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf