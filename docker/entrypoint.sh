#!/bin/sh
set -e

# Asegurar que exista APP_KEY si no está definida para evitar Error 500
if [ -z "$APP_KEY" ]; then
  echo "ADVERTENCIA: APP_KEY no está configurada en las variables de entorno. Generando clave..."
  php artisan key:generate --force
fi

# Determinar el host y puerto de la base de datos (DB_HOST, PGHOST o extraído de DATABASE_URL)
TARGET_HOST="${DB_HOST:-$PGHOST}"
if [ -z "$TARGET_HOST" ] && [ -n "$DATABASE_URL" ]; then
  TARGET_HOST=$(echo "$DATABASE_URL" | sed -e 's|^.*@||' -e 's|:.*$||' -e 's|/.*$||')
fi

TARGET_PORT="${DB_PORT:-${PGPORT:-5432}}"
if [ -n "$DATABASE_URL" ] && [ -z "$DB_PORT" ] && [ -z "$PGPORT" ]; then
  EXTRACTED_PORT=$(echo "$DATABASE_URL" | sed -e 's|^.*:||' -e 's|/.*$||')
  case "$EXTRACTED_PORT" in
    ''|*[!0-9]*) ;;
    *) TARGET_PORT="$EXTRACTED_PORT" ;;
  esac
fi

# Esperar a que la base de datos esté lista
if [ -n "$TARGET_HOST" ]; then
  echo "Esperando a que la base de datos en $TARGET_HOST:$TARGET_PORT sea accesible..."
  MAX_TRIES=20
  TRY=1
  while [ $TRY -le $MAX_TRIES ]; do
    if nc -z -w 3 "$TARGET_HOST" "$TARGET_PORT" 2>/dev/null; then
      echo "Base de datos accesible en $TARGET_HOST:$TARGET_PORT."
      break
    fi
    echo "Intento $TRY de $MAX_TRIES: No se pudo conectar a $TARGET_HOST:$TARGET_PORT. Reintentando en 2 segundos..."
    sleep 2
    TRY=$((TRY + 1))
  done
fi

# Optimizaciones de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ESTO CREARÁ TU ADMIN AUTOMÁTICAMENTE
echo "Ejecutando migraciones..."
php artisan migrate --force || echo "ADVERTENCIA: Las migraciones fallaron. Verifica la conexión a la base de datos."

echo "Ejecutando seeder (creación de usuario admin ADM01)..."
php artisan db:seed --force || echo "ADVERTENCIA: El seeding falló."

# Enlace de almacenamiento público
php artisan storage:link --force || echo "ADVERTENCIA: No se pudo crear el enlace simbólico de storage."

# CORRECCIÓN VITAL PARA PERMISOS EN RAILWAY/DOCKER
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage 2>/dev/null || true

# Arrancar el servidor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf