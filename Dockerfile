FROM dunglas/frankenphp:1.11.1-php8.4 AS base

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libexif-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pcntl pdo_mysql intl gd zip exif \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

FROM base AS builder

# Install composer and Node.js
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs

# Copy and install dependencies (cached layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader

COPY package.json package-lock.json ./
RUN npm ci

COPY .env .env

# Copy vite config and sources
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build && npm prune --omit=dev

COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

FROM base AS production

COPY --from=builder /app /app

RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp

# Activate PHP production mode
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "expose_php = Off" >> "$PHP_INI_DIR/php.ini"

# Create directories and set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    /data/caddy \
    /config/caddy \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache /data /config

RUN php artisan storage:link

USER www-data

CMD ["php", "artisan", "octane:frankenphp"]
