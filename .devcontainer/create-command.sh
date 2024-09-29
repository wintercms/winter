#!/usr/bin/env bash

set -e

echo "### Updating Composer dependencies"
php ${PWD}/.devcontainer/update-composer.php
composer update --no-interaction --no-scripts --no-audit

if [ ! -f "${PWD}/.env" ]; then
    echo "### Generating .env files"
    php artisan winter:env -q
    php artisan key:generate -q
fi

if [ "${DB_CONNECTION}" = "sqlite" ] && [ "${DB_DATABASE}" = "storage/database.sqlite" ] && [ ! -f "${PWD}/storage/database.sqlite" ]; then
    SETUP_ADMIN=true
    echo "### Creating SQLite database"
    touch storage/database.sqlite
fi

echo "### Run migrations"
php artisan migrate

if [ "${SETUP_ADMIN}" = true ]; then 
    echo "### Setup admin"
    php artisan winter:passwd admin admin
fi

echo "### Mirroring site and making it available via web"
php artisan theme:use workshop
sudo rm -rf public/
php artisan winter:mirror public/
sudo rm -rf /var/www/html
sudo ln -s "$(pwd)/public" /var/www/html

echo "### Ignoring files in Git"
echo "plugins/*" >> "$(pwd)/.git/info/exclude"
echo "themes/*" >> "$(pwd)/.git/info/exclude"
echo "composer.json" >> "$(pwd)/.git/info/exclude"
git restore config
