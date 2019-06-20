#!/bin/sh
set -e

sh /youless-logger/bin/copy-configs.sh
sh /youless-logger/bin/create-database.sh

if [[ ! -f /youless-logger/vendor/autoload.php ]]
then
    echo Installing composer packages, please wait...
    composer install \
        --no-suggest \
        --optimize-autoloader
fi

exec "$@"
