FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2dismod mpm_event mpm_worker \
    && a2enmod mpm_prefork rewrite

COPY . /var/www/html/

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80