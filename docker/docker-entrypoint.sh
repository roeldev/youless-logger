#!/bin/sh
set -e

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
    sqlite3 -init /youless-logger/database.sqlite /youless-logger/data/youless-logger.db
fi

exec "$@"
