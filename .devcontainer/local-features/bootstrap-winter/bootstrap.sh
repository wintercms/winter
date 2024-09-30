#!/usr/bin/env bash

set -e

echo "### Updating Composer dependencies"
php ${PWD}/.devcontainer/local-features/bootstrap-winter/update-composer.php
composer update --no-interaction --no-scripts --no-audit

if [ ! -f "${PWD}/.env" ]; then
    echo "### Generating .env files"
    php artisan winter:env -q
    php artisan key:generate -q

    if [ "${CODESPACES}" = "true" ]; then
        echo "### Updating .env file for Codespaces"
        sed -i "s/APP_URL=\"http:\/\/localhost\"/APP_URL=\"https:\/\/${CODESPACE_NAME}.app.github.dev\"/g" .env
    fi
fi

if [ "${DB_CONNECTION}" = "sqlite" ] && [ "${DB_DATABASE}" = "${PWD}/storage/database.sqlite" ] && [ ! -f "${PWD}/storage/database.sqlite" ]; then
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

echo "### Switch theme"
php artisan theme:use workshop

echo "### Ignoring files in Git"
echo "plugins/*" >> "${PWD}/.git/info/exclude"
echo "themes/*" >> "${PWD}/.git/info/exclude"
echo "composer.json" >> "${PWD}/.git/info/exclude"
git update-index --assume-unchanged composer.json
git restore config

cp ${PWD}/.devcontainer/.vscode/launch.json ${PWD}/.vscode/launch.json

if [ "${CODESPACES}" = "true" ]; then
    echo "### Make web port public"
    gh codespace ports visibility 8080:public -c ${CODESPACE_NAME}
fi