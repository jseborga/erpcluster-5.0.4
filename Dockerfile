# ./Dockerfile
FROM php:7.0-apache

RUN a2enmod rewrite && \
    apt-get update && apt-get install -y \
      libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
      libxml2-dev libicu-dev libzip-dev libldap2-dev \
      zlib1g-dev libmagickwand-dev --no-install-recommends && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install gd intl zip soap xml mbstring curl pdo_mysql mysqli && \
    echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf && a2enconf servername

# Copia tu código modificado (incluye htdocs/, scripts/, etc.)
COPY . /var/www/html
# Rutas persistentes
VOLUME ["/var/www/html/documents", "/var/www/html/conf", "/var/www/html/htdocs/custom"]

# Seguridad básica y permisos
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

EXPOSE 80
