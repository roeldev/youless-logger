#!/bin/sh

XDEBUG_VERSION=$( php -r "echo phpversion('xdebug');" )
if [ ! -z "${XDEBUG_VERSION}" ]
then
    echo "Xdebug version: ${XDEBUG_VERSION}"
fi
