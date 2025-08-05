#!/bin/sh
set -e

echo -n "Waiting for composer packages to be installed ..."
while [ ! -f "vendor/autoload.php" ]
do
    echo -n "."
    sleep 1
done
echo " OK !"

if [ -f "package.json" ]
then
  if [ -f "node_modules/.installed" ] && [ "node_modules/.installed" -nt "package.json" ] && [ "node_modules/.installed" -nt "package-lock.json" ]
  then
    echo "Node modules already installed"
  else
    echo "Installing node modules"
    if [ -f "package-lock.json" ] && [ "package-lock.json" -nt "package.json" ]
    then
      /usr/local/bin/npm ci
    else
      /usr/local/bin/npm install
    fi

    touch "node_modules/.installed"
  fi
else
  echo "Error : no package.json file"
  exit 1
fi

echo "Starting Webpack"

/usr/bin/supervisorctl -c /etc/supervisord.conf start webpack:
