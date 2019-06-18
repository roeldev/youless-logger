#!/bin/sh

for script in /etc/motd.d/*.sh
do
    if [ -r ${script} ]
    then
        . ${script}
    fi
done
echo
