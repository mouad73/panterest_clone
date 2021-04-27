# Dockerfile
FROM php:7.2-cli

RUN apt-get update -y && apt-get install -y libmcrypt-dev libpng-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install gd

WORKDIR /app
COPY . /app

RUN composer install

EXPOSE 8000
CMD php bin/console server:run 0.0.0.0:8000