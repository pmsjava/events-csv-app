FROM composer:2 AS composer

FROM php:8.5-cli-alpine

WORKDIR /app

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache unzip git

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress --prefer-dist

COPY public ./public
COPY src ./src
COPY views ./views
COPY data ./data

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
