FROM php:8.2-cli

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
# Copiar archivos mínimos necesarios para que los scripts de Composer no fallen
COPY artisan ./
COPY bootstrap ./bootstrap

# Instalar dependencias sin ejecutar los scripts (evita package:discover antes de tener 'artisan' y 'vendor')
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# Copiar el resto del proyecto
COPY . .

# Ejecutar los scripts de Composer que requieren que el proyecto esté completo
RUN php artisan package:discover --ansi || true

# Compilar assets
RUN npm install \
    && npm run build

EXPOSE 10000

CMD ["sh", "-c", "php artisan serve --host 0.0.0.0 --port ${PORT:-10000}"]