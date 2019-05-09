# https://hub.docker.com/_/php
FROM php:7.1-cli-alpine as builder
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY docker/php/install-composer.sh /tmp/
COPY youless/ /youless/
WORKDIR /youless/

RUN set -x && \
    # install composer
    sh /tmp/install-composer.sh && \
    # install required php packages
    composer install \
        --no-dev \
        --no-progress \
        --no-suggest \
        --no-interaction \
        && \
    composer dumpautoload -o

# https://hub.docker.com/_/php
FROM php:7.1-cli-alpine

COPY docker/youless.sh /usr/bin/youless

RUN set -x && \
    chmod +x /usr/bin/youless && \
    apk add --no-cache \
        sqlite \
        supervisor

COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/conf.d/*.ini /usr/local/etc/php/conf.d/
COPY --from=builder /youless/ /youless/

WORKDIR /youless/
VOLUME ["/youless/data/", "/youless/log/"]

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
