#!/bin/sh
set -e

if [[ ! -f /youless/vendor/autoload.php ]]; then
    echo Installing composer packages, please wait...
    composer install \
        --no-suggest \
        --optimize-autoloader
fi

exec "$@"
