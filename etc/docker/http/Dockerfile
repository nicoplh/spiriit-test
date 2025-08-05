FROM nginx:stable-alpine
ARG TZ="Europe/Paris"
RUN <<EOF
set -ex
rm /etc/nginx/conf.d/default.conf
mkdir /scripts
## Configure timezone
apk add --no-cache tzdata
cp /usr/share/zoneinfo/${TZ} /etc/localtime
apk del tzdata
EOF
COPY --chmod=444 nginx.conf /etc/nginx/nginx.conf
COPY --chmod=555 generate-app-config.sh /scripts/generate-app-config
EXPOSE 80
ARG ENABLED_SERVICES="adminer maildev node php"
ARG HOSTNAME="app.localhost"
ARG DOCUMENTROOT_PATH="public"
ARG FRONT_CONTROLLER="index.php"
RUN <<EOF
set -ex
sh /scripts/generate-app-config "$HOSTNAME" "$ENABLED_SERVICES" "$DOCUMENTROOT_PATH" "$FRONT_CONTROLLER" > /etc/nginx/conf.d/app.conf
chmod 0440 /etc/nginx/nginx.conf /etc/nginx/conf.d/app.conf
cat /etc/nginx/conf.d/app.conf
EOF
