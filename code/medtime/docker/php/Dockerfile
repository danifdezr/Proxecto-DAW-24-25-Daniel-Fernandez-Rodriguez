FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite

WORKDIR /var/www/html