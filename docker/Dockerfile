# https://hub.docker.com/_/php
FROM php:7.1-cli-alpine
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY docker/install-composer.sh /tmp/
COPY docker/youless.sh /usr/bin/youless
COPY youless/ /youless/
WORKDIR /youless/

RUN set -x && \
    chmod +x /usr/bin/youless && \
    apk add --no-cache \
        sqlite \
        supervisor \
        && \
    # install composer
    sh /tmp/install-composer.sh && \
    # install required php packages
    composer install \
        --no-dev \
        --no-progress \
        --no-suggest \
        --no-interaction \
        && \
    composer dumpautoload -o && \
    # cleanup
    apk del composer-deps && \
    rm -rf \
        /usr/local/bin/composer \
        /root/.composer/ \
        /tmp/*

VOLUME ["/youless/data/", "/youless/log/"]

COPY docker/docker-entrypoint.sh /
COPY docker/supervisord.conf /etc/supervisord.conf

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
