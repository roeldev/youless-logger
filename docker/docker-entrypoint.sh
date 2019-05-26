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
    sqlite3 /youless-logger/data/youless-logger.db "\
        CREATE TABLE data( \
           id INT PRIMARY KEY NOT NULL, \
           resolution CHAR(1) NOT NULL, \
           unit VARCHAR(4) NOT NULL, \
           value INT NOT NULL, \
           date DATETIME NOT NULL \
        );"
fi

exec "$@"
