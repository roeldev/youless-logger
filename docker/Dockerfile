# build project, install composer dependecies
FROM roeldev/php-composer:7.1-latest as builder
COPY project/ /project/
WORKDIR /project/

RUN set -x \
 # install required php packages
 && composer install \
        --no-dev \
        --no-progress \
        --no-suggest \
        --no-interaction \
 && composer dumpautoload -o

# actual image
FROM roeldev/php-cli:7.1-latest
ARG PHP_VERSION="7.1"

RUN set -x \
 && apk add --no-cache \
        sqlite \
        php${PHP_VERSION}-pdo \
        php${PHP_VERSION}-pdo_sqlite

COPY --from=builder /project/ /yl/
COPY docker/rootfs/ /

RUN set -x \
 && chmod +x /usr/bin/yl

WORKDIR /yl/
VOLUME ["/yl/config/", "/yl/data/", "/yl/log/"]
