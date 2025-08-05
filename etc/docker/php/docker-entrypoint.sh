#!/bin/sh

[ -d vendor ] || composer install

/usr/local/bin/docker-php-entrypoint "$@"
