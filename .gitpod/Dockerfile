FROM gitpod/workspace-mysql

# Install XDebug extension
RUN sudo apt-get update -q \
    && sudo apt-get install -y \
        php-dev \
    && sudo pecl install xdebug \
    # Install Composer 2 (Gitpod comes pre-installed with Composer 1)
    && sudo apt-get remove -y \
        composer \
    && ./.gitpod/composer-installer \
    && sudo mv ./composer.phar /usr/local/bin/composer
