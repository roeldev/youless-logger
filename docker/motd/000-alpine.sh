#!/bin/sh

echo "Linux version: $( cat /proc/version | cut -d' ' -f 3-)"
