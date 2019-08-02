# build project, install composer dependecies
ARG PHP_VERSION="7.3"
FROM roeldev/php-composer:${PHP_VERSION}-v1.4 as builder
COPY app/ /app/
WORKDIR /app/

RUN set -x \
 # install required php packages
 && composer install \
        --no-dev \
        --no-progress \
        --no-suggest \
        --no-interaction \
 && composer dumpautoload -o

# actual image
ARG PHP_VERSION="7.3"
FROM roeldev/php-nginx:${PHP_VERSION}-v1.0

ARG PHP_VERSION="7.3"
RUN set -x \
 && apk add --no-cache \
        sqlite \
        php${PHP_VERSION}-pdo \
        php${PHP_VERSION}-pdo_sqlite

COPY --from=builder /app/ /app/
COPY rootfs/ /

WORKDIR /app/
VOLUME ["/app/config/", "/app/data/", "/app/log/"]
