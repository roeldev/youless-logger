#!/usr/bin/with-contenv bash

if [[ ! -f /app/data/youless-logger.db ]]
then
    echo Creating sqlite database, please wait...
    sqlite3 -init \
        /app/resources/db-schema.sqlite \
        /app/data/youless-logger.db
fi
