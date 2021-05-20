FROM php:8.0-fpm-alpine

ADD .docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN addgroup -g 1000 invillia && adduser -G invillia -g invillia -s /bin/sh -D invillia

RUN mkdir -p /var/www/html

RUN chown invillia:invillia /var/www/html

WORKDIR /var/www/html

RUN apk add --no-cache \
    freetype \
    libpng \
    libjpeg-turbo \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
    docker-php-ext-install -j${NPROC} gd

RUN apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

RUN docker-php-ext-install pdo pdo_mysql

RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# relevant to this answer
RUN echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
