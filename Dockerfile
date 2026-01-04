FROM dunglas/frankenphp:php8.4 AS base

RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    intl \
    gd \
    zip \
    exif

# TODO: Ne pas utiliser gosu ? (Pour ne pas démarrer les conteneurs en root)
#RUN apt-get update && apt-get install -y supervisor gosu && rm -rf /var/lib/apt/lists/*

WORKDIR /app

FROM base AS builder

# Install composer and Node.js
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs

# Copy dependencies (cached layer)
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Install dependencies (cached layer)
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts
RUN npm ci

# Copy vite config and sources
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build && npm prune --omit=dev

COPY . .

FROM base AS production

COPY --from=builder /app /app

RUN setcap -r /usr/local/bin/frankenphp

# Create dir and give permissions
RUN mkdir -p /app/storage/logs /app/storage/tmp /tmp \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/public /tmp

# Activate PHP production mode
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "expose_php = Off" >> "$PHP_INI_DIR/php.ini"

USER www-data

CMD ["php", "artisan", "octane:frankenphp"]
