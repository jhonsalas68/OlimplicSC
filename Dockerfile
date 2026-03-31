# Stage 1: PHP dependencies
FROM composer:2 AS composer_build
WORKDIR /app
COPY composer.json composer.lock ./
# Añadimos --no-scripts para evitar el error "Could not open input file: artisan"
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev

# Stage 2: Frontend assets
FROM node:20-alpine AS node_build
WORKDIR /app
# Copiamos vendor para que Vite/Tailwind escaneen las clases de los paquetes (como Filament)
COPY --from=composer_build /app/vendor ./vendor
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 3: Final Runtime
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    postgresql-client \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd bcmath zip intl pdo_pgsql mbstring exif pcntl opcache sockets

WORKDIR /var/www/html

# Copiar el código del proyecto
COPY . .

# Copiar los resultados de las etapas anteriores
COPY --from=composer_build /app/vendor ./vendor
COPY --from=node_build /app/public/build ./public/build

# Copiar configuraciones de la carpeta docker/
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php-upload.ini /usr/local/etc/php/conf.d/php-upload.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Crear directorios necesarios y ajustar permisos
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto que usa Railway
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]