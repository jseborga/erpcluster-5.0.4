FROM php:7.0-apache

# Repos archivados para Debian Stretch (sin stretch-updates)
RUN printf 'deb http://archive.debian.org/debian stretch main\n' \
        > /etc/apt/sources.list && \
    printf 'deb http://archive.debian.org/debian-security stretch/updates main\n' \
        >> /etc/apt/sources.list && \
    printf 'Acquire::Check-Valid-Until "false";\nAcquire::AllowInsecureRepositories "true";\nAPT::Get::AllowUnauthenticated "true";\n' \
        > /etc/apt/apt.conf.d/99archive

RUN a2enmod rewrite && \
    apt-get -o Acquire::Check-Valid-Until=false -o Acquire::AllowInsecureRepositories=true update && \
    apt-get install -y --no-install-recommends --allow-unauthenticated \
      ca-certificates gnupg pkg-config \
      libcurl4-openssl-dev \ 
      libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
      libxml2-dev libicu-dev libzip-dev libldap2-dev \
      zlib1g-dev libmagickwand-dev && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install gd intl zip soap xml mbstring pdo_mysql mysqli curl && \
    echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf && a2enconf servername

COPY . /var/www/html
VOLUME ["/var/www/html/documents", "/var/www/html/conf", "/var/www/html/htdocs/custom"]
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80
