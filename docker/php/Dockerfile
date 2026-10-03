FROM php:8.2-apache

# Habilitar mod_rewrite de Apache para URLs amigables del patrón MVC
RUN a2enmod rewrite

# Instalar extensiones requeridas para MySQL PDO
RUN docker-php-ext-install pdo pdo_mysql

# Ajustar DocumentRoot de Apache a la carpeta public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html