# build project, install composer dependecies
FROM roeldev/php-composer:7.3-v1.6 as builder
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

###############################################################################
# create actual image
###############################################################################
FROM roeldev/php-nginx:7.3-v1.3

# expose environment variables
ENV CRON_LOG_LEVEL=8 \
    CRON_LOG_FILE=/app/log/cron.log

RUN set -x \
 && apk update \
 && apk add \
    --no-cache \
        sqlite \
        php7.3-pdo_sqlite

COPY --from=builder /app/ /app/
COPY rootfs/ /
COPY LICENSE /app/LICENSE

ARG VERSION="dev"
RUN set -x \
 && echo "${VERSION}" >> /app/VERSION

WORKDIR /app/
VOLUME ["/app/data/", "/app/log/"]
