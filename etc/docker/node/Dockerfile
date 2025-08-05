ARG NODE_VERSION

FROM node:${NODE_VERSION}-alpine

ARG TZ="Europe/Paris"

RUN <<EOF
set -ex
apk add --no-cache \
    supervisor
## Configure timezone
apk add --no-cache tzdata
cp /usr/share/zoneinfo/${TZ} /etc/localtime
apk del tzdata
## Create required directories & set ownership
mkdir -p /.npm /var/log/supervisor /run/supervisor /etc/supervisord.conf.d
chown 82:82 /.npm /var/log/supervisor /run/supervisor
EOF

COPY --chmod=444 supervisord.conf /etc/
COPY --chmod=444 supervisord.conf.d/ /etc/supervisord.conf.d/
COPY --chmod=555 start-services.sh /usr/local/bin/start-services
COPY --chmod=444 app-supervisord.conf /etc/supervisord.conf.d/app.conf

USER 82:82
WORKDIR /var/www/app

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
