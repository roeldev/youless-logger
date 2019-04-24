#!/bin/sh
set -e

# install dependencies
apk add \
    --no-cache \
    --virtual composer-deps \
        git \
        unzip

# download composer installer
curl -fLs \
    --retry 3 \
    --output /tmp/composer-installer.php \
    --url https://getcomposer.org/installer \

# check installer signature
FILE_HASH=$(php -r "echo hash_file('SHA384', '/tmp/composer-installer.php');")
SIGNATURE=$(curl -fLs --retry 3 https://composer.github.io/installer.sig)

if [[ "$SIGNATURE" != "$FILE_HASH" ]]
then
    >&2 echo 'ERROR: Invalid composer installer signature'
    exit 1
fi

# install composer
php /tmp/composer-installer.php \
    --quiet \
    --install-dir=/usr/local/bin \
    --filename=composer
