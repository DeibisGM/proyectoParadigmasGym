FROM php:8.1-apache

# Activar el módulo de reescritura de Apache
RUN a2enmod rewrite

COPY . /var/www/html/
