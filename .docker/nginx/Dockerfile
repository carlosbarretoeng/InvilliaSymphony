FROM nginx:stable-alpine

RUN apk add --no-cache bash

ADD .docker/nginx/nginx.conf /etc/nginx/nginx.conf
ADD .docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/www/html

RUN addgroup -g 1000 invillia && adduser -G invillia -g invillia -s /bin/sh -D invillia

RUN chown invillia:invillia /var/www/html