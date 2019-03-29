# ------------------------------------------------------------------------------
# Base image
# ------------------------------------------------------------------------------
FROM php:7.3-cli-alpine AS base

COPY youless.sh /usr/bin/youless
COPY youless/* /youless/

RUN set -x && \
    apk --no-cache add --virtual deps && \
    apk add \
        sqlite \
        && \
    apk del deps && \
    chmod +x /usr/bin/youless

VOLUME ["/youless/data", "/youless/log"]
WORKDIR /youless/

COPY docker-entrypoint.sh /
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["php", "/youless/youless.php", "--start"]

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
