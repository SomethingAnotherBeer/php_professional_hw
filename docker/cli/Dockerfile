FROM php:8.5-cli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    pkg-config \
    zlib1g-dev \
    libssl-dev \
    libpq-dev \
    && docker-php-ext-install iconv mbstring zip pdo_pgsql

USER 1000:1000
EXPOSE 9000

WORKDIR /var/www/app