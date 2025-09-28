FROM php:7.1-apache
ARG PHP_TIMEZONE=America/La_Paz
ENV APACHE_DOCUMENT_ROOT=/var/www/html/htdocs

# Repos EOL → usar archive.debian.org
RUN sed -i -e 's|deb.debian.org/debian|archive.debian.org/debian|g' \
           -e 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' \
           -e 's|^deb .* buster-updates .*|# disabled buster-updates|g' /etc/apt/sources.list && \
    apt-get -o Acquire::Check-Valid-Until=false update && \
    apt-get install -y \
      git curl zip \
      libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
      libxml2-dev libicu-dev libzip-dev && \
    # Para PHP 7.1 usa los flags antiguos de GD
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install -j"$(nproc)" gd intl zip pdo pdo_mysql mysqli soap && \
    pecl install apcu && docker-php-ext-enable apcu && a2enmod rewrite && \
    rm -rf /var/lib/apt/lists/*

# Dolibarr 5.0.4
RUN git clone --branch 5.0.4 --depth 1 https://github.com/Dolibarr/dolibarr.git /var/www/html

# DocumentRoot → htdocs
RUN sed -ri -e "s!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e "s!<Directory /var/www/>!<Directory ${APACHE_DOCUMENT_ROOT}/>!g" /etc/apache2/apache2.conf

COPY php.ini /usr/local/etc/php/conf.d/zz-dolibarr.ini
WORKDIR /var/www/html

