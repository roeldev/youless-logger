#!/bin/sh
set -e

# --replace     Replace existing database

replace=false

while true
do
    case "$1" in
        -b | --backup )  cp /youless-logger/data/youless-logger.db /youless-logger/data/backup.db;;
        -r | --replace ) replace=true;;
        -- ) ;;
        * ) if [ -z "$1" ]; then break; else echo "$1 is not a valid option"; exit 1; fi;;
    esac
    shift
done

if [[ replace || ! -f /youless-logger/data/youless-logger.db ]]
then
    echo Creating sqlite database, please wait...
    sqlite3 -init \
        /youless-logger/resources/db-schema.sqlite \
        /youless-logger/data/youless-logger.db
fi
