#!/bin/sh
HOSTNAME="$1"
SERVICES="$2"
DOCUMENTROOT_PATH="$3"
FRONT_CONTROLLER="$4"

cat <<EOF
server {
    server_name ${HOSTNAME:-app.localhost};
    root /var/www/app/${DOCUMENTROOT_PATH:-public};

EOF

for SERVICE in $SERVICES
do
  case "$SERVICE" in
  *adminer*)
    cat <<EOF
    location /_adminer/ {
        proxy_pass http://adminer:8080/;
    }

EOF
    ;;
  *maildev*)
    cat <<EOF
    location /_maildev/ {
        proxy_pass http://mail:1080/;
    }

EOF
    ;;
  *node*)
    cat <<EOF
    location = /_webpack {
        proxy_pass http://node:3000/ws;

        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
    }

    location /_webpack/ {
        proxy_pass http://node:3000/_webpack/;
    }

EOF
    ;;
  *php*)
    cat <<EOF
    location /_fpm/status {
        access_log off;
        fastcgi_pass php:9001;
        fastcgi_param REQUEST_METHOD \$request_method;
        fastcgi_param QUERY_STRING \$query_string;
        fastcgi_param SCRIPT_NAME /_fpm/status;
        fastcgi_param SCRIPT_FILENAME "";
    }

    location / {
        try_files \$uri @rewriteapp;
    }

    location @rewriteapp {
        rewrite ^(.*)\$ /${FRONT_CONTROLLER:-index.php}/\$1 last;
    }

    location ~ \.php(/|\$) {
        fastcgi_pass php:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)\$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param HTTPS \$https_real;
        fastcgi_read_timeout 600;
    }

EOF
    ;;
  esac
done

cat <<EOF
    error_log /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
}
EOF
