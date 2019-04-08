#!/bin/sh

# install composer dependencies
apk add --no-cache --virtual composer-deps \
    git \
    unzip

# install composer
curl -LsS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer
