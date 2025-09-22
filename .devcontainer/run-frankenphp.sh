#!/usr/bin/env bash

set -e

USER_ID=$(id -u)
GROUP_ID=$(id -g)

# Ensure the Caddy config and data directories are owned by the current user
sudo chown -R $USER_ID:$GROUP_ID /config/caddy
sudo chown -R $USER_ID:$GROUP_ID /data/caddy
sudo touch /var/log/frankenphp.log
sudo chown $USER_ID:$GROUP_ID /var/log/frankenphp.log

# Run server in background
if [ "${CODESPACES}" = "true" ]; then
    # Ensure environment variables are set correctly for Codespaces
    SERVER_NAME="http://:8000" APP_URL="https://${CODESPACE_NAME}-8000.app.github.dev" /usr/local/bin/frankenphp run -c /etc/frankenphp/Caddyfile -a caddyfile > /var/log/frankenphp.log 2>&1 &
else
    /usr/local/bin/frankenphp run -c /etc/frankenphp/Caddyfile -a caddyfile > /var/log/frankenphp.log 2>&1 &
fi

