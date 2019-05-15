#!/bin/sh
set -e

if [[ ! -f /youless-logger/vendor/autoload.php ]]
then
    echo Installing composer packages, please wait...
    composer install \
        --no-suggest \
        --optimize-autoloader
fi

exec "$@"
