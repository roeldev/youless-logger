# https://hub.docker.com/_/php
FROM php:7.1-cli-alpine as builder
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY docker/install-composer.sh /tmp/
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

COPY --from=builder /youless/ /youless/
COPY docker/supervisord.conf /etc/supervisord.conf

WORKDIR /youless/
VOLUME ["/youless/data/", "/youless/log/"]

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
