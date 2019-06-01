#!/bin/sh
set -e

if [[ ! -d /youless-logger/config/supervisord ]]
then
    cp -avr /youless-logger/resources/supervisord-defaults /youless-logger/config/supervisord
fi

if [[ ! -f /youless-logger/vendor/autoload.php ]]
then
    echo Installing composer packages, please wait...
    composer install \
        --no-suggest \
        --optimize-autoloader
fi

if [[ ! -f /youless-logger/data/youless-logger.db ]]
then
    echo Creating sqlite database, please wait...
    sqlite3 -init /youless-logger/resources/db-schema.sqlite /youless-logger/data/youless-logger.db
fi

exec "$@"
