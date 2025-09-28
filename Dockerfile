FROM php:7.0-apache

# Fix repos archivados de Stretch
RUN sed -i 's|deb.debian.org/debian|archive.debian.org/debian|g;s|security.debian.org/debian-security|archive.debian.org/debian-security|g' /etc/apt/sources.list \
 && printf 'Acquire::Check-Valid-Until "false";\nAcquire::AllowInsecureRepositories "true";\n' > /etc/apt/apt.conf.d/99archive

RUN a2enmod rewrite && \
    apt-get -o Acquire::Check-Valid-Until=false update && \
    apt-get install -y --no-install-recommends \
      libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
      libxml2-dev libicu-dev libzip-dev libldap2-dev \
      zlib1g-dev libmagickwand-dev && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install gd intl zip soap xml mbstring curl pdo_mysql mysqli && \
    echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf && a2enconf servername

COPY . /var/www/html
VOLUME ["/var/www/html/documents", "/var/www/html/conf", "/var/www/html/htdocs/custom"]
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80

