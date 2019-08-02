#!/usr/bin/with-contenv bash

if [[ ! -f /usr/local/bin/composer ]]
then
    exit 0
fi

if [[ ! -f /app/vendor/autoload.php ]]
then
    echo Installing composer packages, please wait...
    composer install --no-suggest
fi

composer dumpautoload
