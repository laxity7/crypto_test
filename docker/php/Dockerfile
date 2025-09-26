FROM php:8.4-fpm-alpine

RUN apk add --no-cache $PHPIZE_DEPS icu-dev libzip-dev git curl unzip mysql-client supervisor

RUN docker-php-ext-install pdo_mysql intl zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG UID=1000
ARG GID=1000
RUN addgroup -g $GID -S appuser && adduser -u $UID -S appuser -G appuser

COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]