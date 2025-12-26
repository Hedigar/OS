FROM php:8.2-apache

# Ativa mod_rewrite
RUN a2enmod rewrite

# Instala extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Ajusta DocumentRoot (opcional, mas recomendado)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

WORKDIR /var/www/html
