#!/bin/sh
set -e

# Optimizaciones de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Esperar a que la base de datos esté lista
if [ -n "$DB_HOST" ]; then
  echo "Esperando a que la base de datos en $DB_HOST sea accesible..."
  MAX_TRIES=15
  TRY=1
  while [ $TRY -le $MAX_TRIES ]; do
    if nc -z -w 3 "$DB_HOST" "${DB_PORT:-5432}" 2>/dev/null; then
      echo "Base de datos accesible."
      break
    fi
    echo "Intento $TRY de $MAX_TRIES: No se pudo conectar a $DB_HOST. Reintentando en 2 segundos..."
    sleep 2
    TRY=$((TRY + 1))
  done
fi

# ESTO CREARÁ TU ADMIN AUTOMÁTICAMENTE
php artisan migrate --force
php artisan db:seed --force

# CORRECCIÓN VITAL PARA POSTGRESQL Y LOGS EN RAILWAY/DOCKER
# Asignar propiedad a www-data (el usuario del servidor web) para que pueda leer/escribir en la base de datos y logs.
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

# Arrancar el servidor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf