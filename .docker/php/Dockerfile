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

RUN apk add --no-cache $PHPIZE_DEPS \
	&& pecl install xdebug \
	&& docker-php-ext-enable xdebug