FROM php:8-alpine

ENV GRPC_VERSION=1.38.0

RUN apk add --update --no-cache ${PHPIZE_DEPS} zlib-dev linux-headers libstdc++ \
    && pecl install grpc-${GRPC_VERSION} \
    && docker-php-ext-enable grpc

RUN apk add --update --no-cache protoc grpc

COPY --from=composer:2.1.3 /usr/bin/composer /usr/bin/composer

COPY composer.* /app/

WORKDIR /app

RUN composer install --no-autoloader --no-interaction

COPY . /app

RUN composer dump-autoload --no-scripts --optimize \
    && rm -rf /root/.composer \
    && apk del ${PHPIZE_DEPS} linux-headers

ENTRYPOINT ["./docker-entrypoint.sh"]