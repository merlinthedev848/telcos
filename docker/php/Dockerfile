FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash git icu-dev libzip-dev nodejs npm oniguruma-dev \
    && docker-php-ext-install intl mbstring opcache pdo_mysql zip pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]
