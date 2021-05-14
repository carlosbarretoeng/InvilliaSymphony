FROM composer:latest

RUN addgroup -g 1000 invillia && adduser -G invillia -g invillia -s /bin/sh -D invillia

RUN chown -R invillia:invillia /var/www/html

WORKDIR /var/www/html