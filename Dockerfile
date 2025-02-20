FROM php:7.1-apache
MAINTAINER Jan Drábek <jan@drabek.cz>

# Enable various PHP extensions
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        unzip \
        git \
        mariadb-client \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli opcache gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable XDebug
RUN pecl install xdebug-2.5.0 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host=docker.for.mac.localhost" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install self-signed SSL certificate and enable SSL in Apache, also move document root and setup proxy to Keycloack
COPY .keycloak/keycloak.conf /etc/apache2/sites-available/
RUN mkdir -p /etc/ssl/localcerts \
    && openssl req -new -x509 -days 365 -nodes -out /etc/ssl/localcerts/apache.pem -keyout /etc/ssl/localcerts/apache.key -subj "/C=CZ/O=Instruktoři Brno, z. s./OU=TMOU/CN=tmou.test" \
    && chmod 600 /etc/ssl/localcerts/apache* \
    && sed -i "s#/etc/ssl/certs/ssl-cert-snakeoil.pem#/etc/ssl/localcerts/apache.pem#g" /etc/apache2/sites-available/default-ssl.conf \
    && sed -i "s#/etc/ssl/private/ssl-cert-snakeoil.key#/etc/ssl/localcerts/apache.key#g" /etc/apache2/sites-available/default-ssl.conf \
    && a2enmod ssl rewrite proxy proxy_http && a2ensite default-ssl \
    && echo "Listen 9990" >> /etc/apache2/ports.conf \
    && a2ensite keycloak

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir /usr/local/bin \
    && php -r "unlink('composer-setup.php');" \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

# Tune entrypoint in order to wait on database
COPY .docker-files/start-after-db.sh /usr/local/bin/start-after-db.sh
RUN chmod +x /usr/local/bin/start-after-db.sh

CMD ["start-after-db.sh"]
ENTRYPOINT ["start-after-db.sh"]
