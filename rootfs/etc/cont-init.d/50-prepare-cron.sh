#!/usr/bin/with-contenv bash

if [[ ! -f /etc/crontabs/abc ]]
then
    ln -s /app/config/crontab /etc/crontabs/abc
fi

mkdir -p "$( dirname "${CRON_LOG_FILE}" )"
chmod -R 0644 /etc/crontabs/
