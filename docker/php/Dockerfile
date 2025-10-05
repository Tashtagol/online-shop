FROM php:8.3-fpm

RUN apt update -q;
RUN apt install -q -y libpq-dev;
RUN docker-php-ext-install pdo_pgsql pgsql;
