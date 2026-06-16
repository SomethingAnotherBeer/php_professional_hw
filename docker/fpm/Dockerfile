FROM php:8.5-fpm

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libssl-dev \
    && docker-php-ext-install iconv mbstring pdo_mysql zip


USER 1000:1000
EXPOSE 9000

WORKDIR /var/www/app

#CMD ['php-fpm']