FROM php:8.3-apache-bookworm

# Install PHP extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions @composer gd intl zip xdebug redis memcached pdo_mysql pdo_pgsql pdo_sqlsrv

# Enable Apache2 rewrite module
RUN a2enmod rewrite
