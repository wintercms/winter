#!/usr/bin/env bash

set -e

if [ ! -d "${PWD}/vendor" ] && [ ! -f "${PWD}/composer.lock" ]; then
    echo "### Updating Composer dependencies"
    php ${PWD}/.devcontainer/local-features/bootstrap-winter/update-composer.php
    composer update --no-interaction --no-scripts --no-audit
fi

if [ ! -f "${PWD}/.env" ]; then
    echo "### Generating .env file"
    php artisan winter:env -q
    php artisan key:generate -q
fi

if [ "${DB_CONNECTION}" = "sqlite" ] && [ "${DB_DATABASE}" = "${PWD}/storage/database.sqlite" ] && [ ! -f "${PWD}/storage/database.sqlite" ]; then
    SETUP_ADMIN=true
    echo "### Creating SQLite database"
    touch storage/database.sqlite
fi

echo "### Run migrations"
php artisan migrate

echo "### Set theme"
php artisan theme:use workshop

if [ "${SETUP_ADMIN}" = true ]; then
    echo "### Setup admin"
    php artisan winter:passwd admin admin
fi

echo "### Ignoring files in Git"
echo "plugins/*" >> "${PWD}/.git/info/exclude"
echo "themes/*" >> "${PWD}/.git/info/exclude"
echo "composer.json" >> "${PWD}/.git/info/exclude"
git update-index --assume-unchanged composer.json
git restore config

cp ${PWD}/.devcontainer/.vscode/launch.json ${PWD}/.vscode/launch.json

echo "### Mirror site to public directory"
php artisan winter:mirror public

if [ "${CODESPACES}" = "true" ]; then
    echo "### Configure for Codespaces"
    php ${PWD}/.devcontainer/local-features/bootstrap-winter/codespaces.php
    git update-index --assume-unchanged config/app.php
    gh codespace ports visibility 8000:public -c $CODESPACE_NAME
fi
