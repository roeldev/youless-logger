#!/bin/sh
set -e

# --clean       Remove existing configs
# --replace     Overwrite existing configs

replace=false

while true
do
    case "$1" in
        -c | --clean )   rm -rf /youless-logger/config/supervisord/*.conf;;
        -r | --replace ) replace=true;;
        -- ) ;;
        * ) if [ -z "$1" ]; then break; else echo "$1 is not a valid option"; exit 1; fi;;
    esac
    shift
done


if [[ replace || ! -d /youless-logger/config/supervisord/ || \
    `ls -1 /youless-logger/config/supervisord/*.conf 2>/dev/null | wc -l` -eq 0 ]]
then
    cp -avr \
        /youless-logger/resources/supervisord-defaults/* \
        /youless-logger/config/supervisord
fi
