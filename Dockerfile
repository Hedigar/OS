FROM php:8.2-apache

# Ativa mod_rewrite e SSL
RUN a2enmod rewrite ssl

# Instala extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Ajusta DocumentRoot (opcional, mas recomendado)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Copia certificados gerados e configura SSL
COPY ./certs/server.crt /etc/ssl/certs/server.crt
COPY ./certs/server.key /etc/ssl/private/server.key

RUN sed -ri 's!/etc/ssl/certs/ssl-cert-snakeoil.pem!/etc/ssl/certs/server.crt!g' /etc/apache2/sites-available/default-ssl.conf \
    && sed -ri 's!/etc/ssl/private/ssl-cert-snakeoil.key!/etc/ssl/private/server.key!g' /etc/apache2/sites-available/default-ssl.conf

# Ativa o site SSL padrão
RUN a2ensite default-ssl

WORKDIR /var/www/html
