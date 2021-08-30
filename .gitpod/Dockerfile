FROM gitpod/workspace-mysql

# Install XDebug extension
RUN apt-get update -q \
    && apt-get install -y \
        php-dev \
    && pecl install xdebug \
    # Install Composer 2 (Gitpod comes pre-installed with Composer 1)
    && apt-get remove -y \
        composer \
    && ./.gitpod/composer-installer \
    && mv ./composer.phar /usr/local/bin/composer
