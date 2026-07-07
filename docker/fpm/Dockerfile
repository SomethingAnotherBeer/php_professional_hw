FROM php:8.5-fpm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    zlib1g-dev \
    libssl-dev \
    && docker-php-ext-install iconv mbstring pdo_mysql zip \
    && pecl install redis \
    && docker-php-ext-enable redis 


USER 1000:1000
EXPOSE 9000

WORKDIR /var/www/app