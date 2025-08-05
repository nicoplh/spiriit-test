ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm-alpine

ARG TZ="Europe/Paris"

COPY --chmod=444 .gitconfig /home/www-data/.gitconfig
COPY --chmod=444 php.ini ${PHP_INI_DIR}/conf.d/php.ini
COPY --chmod=444 php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY --chmod=555 docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --chmod=444 supervisord.conf /etc/supervisord.conf

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/install-php-extensions
# https://github.com/mlocati/docker-php-extension-installer#configuration

RUN <<EOF
set -ex
apk add --update --no-cache \
    git \
    supervisor \
    unzip \
    zip
rm -rf /tmp/*
ln -s ${PHP_INI_DIR}/php.ini-development ${PHP_INI_DIR}/php.ini
## Configure timezone
apk add --no-cache tzdata
cp /usr/share/zoneinfo/${TZ} /etc/localtime
apk del tzdata
sed -i -e "s~__TZ__~${TZ}~g" ${PHP_INI_DIR}/conf.d/php.ini
## Create required directories & set ownership
rm -rf /var/www/.composer/
mkdir -p /var/www/.composer /var/log/supervisor /run/supervisor /etc/supervisord.conf.d
chown 82:82 /var/www/.composer /var/log/supervisor /run/supervisor

# pm.status_listen is only available for PHP >=8.0
echo "8.0
${PHP_VERSION}" | sort -V -c 2>/dev/null || sed -i -e "s/^\s*pm\.status_listen/;&/" /usr/local/etc/php-fpm.d/www.conf
EOF

USER 82:82
WORKDIR /var/www/app

ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
