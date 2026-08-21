FROM php:8.5-cli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    zlib1g-dev \
    && docker-php-ext-install mbstring zip

USER 1000:1000

WORKDIR /app