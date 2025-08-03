# Usar la imagen oficial de PHP con el servidor Apache
FROM php:8.1-apache

# Copiar todos los archivos de tu proyecto a la carpeta pública del servidor
COPY . /var/www/html/
