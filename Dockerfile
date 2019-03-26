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
    apk --no-cache add --virtual composerdeps && \
    apk add \
        git \
        unzip \
        && \
    apk del composerdeps && \
    curl -LsS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer

ENV PATH /root/.composer/vendor/bin:$PATH
