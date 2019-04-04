# ------------------------------------------------------------------------------
# Base image
# https://hub.docker.com/_/php
# ------------------------------------------------------------------------------
FROM php:7.1-cli-alpine AS base

COPY docker/youless.sh /usr/bin/youless
COPY youless/* /youless/

RUN set -x && \
    apk --no-cache add --virtual deps && \
    apk add \
        sqlite \
        supervisor \
        && \
    apk del deps && \
    chmod +x /usr/bin/youless

COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/docker-entrypoint.sh /

WORKDIR /youless/

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]

# ------------------------------------------------------------------------------
# Production image
# ------------------------------------------------------------------------------
FROM base AS prod
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN set -x && \
    # install composer
    apk add --no-cache --virtual composer-deps \
        git \
        unzip \
        && \
    curl -LsS https://getcomposer.org/installer | php -- \
        --install-dir=/tmp \
        --filename=composer \
        && \
    # install required packages
    /tmp/composer install \
        --no-dev \
        --no-progress \
        --no-suggest \
        --no-interaction \
        && \
    /tmp/composer dumpautoload -o && \
    # cleanup
    apk del composer-deps && \
    rm -rf \
        /root/.composer/ \
        /tmp/*

ENV PATH /root/.composer/vendor/bin:$PATH
VOLUME ["/youless/data/", "/youless/log/"]

# ------------------------------------------------------------------------------
# Development image
# ------------------------------------------------------------------------------
FROM base AS dev
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN set -x && \
    # install composer
    apk add --no-cache \
        git \
        unzip \
        && \
    curl -LsS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer \
        && \
    # install xdebug
    apk add --no-cache --virtual phpize-deps \
        autoconf \
        g++ \
        make \
        && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    # cleanup
    apk del phpize-deps && \
    rm -rf /tmp/pear

ENV PATH /root/.composer/vendor/bin:$PATH
VOLUME ["/youless/", "/root/.composer/"]
