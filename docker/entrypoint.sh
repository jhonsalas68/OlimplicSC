#!/bin/sh

# Exit on error
set -e

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start Supervisor
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
