FROM gitpod/workspace-mysql

# Install XDebug extension
RUN sudo apt-get update -q \
  && sudo apt-get install -y \
    php-dev \
    golang-go \
  && sudo pecl install xdebug

# Install Composer 2 (Gitpod comes pre-installed with Composer 1) - borrowed from official Composer Docker image
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 2.1.6

RUN set -eux; \
  curl \
    --silent \
    --fail \
    --location \
    --retry 3 \
    --output /tmp/keys.dev.pub \
    --url https://raw.githubusercontent.com/composer/composer.github.io/e7f28b7200249f8e5bc912b42837d4598c74153a/snapshots.pub \
  ; \
  php -r " \
    \$signature = '4ac45767e5ec22652f0c1167cbbb8a2b0c708369153e328cad90147dafe50952'; \
    \$hash = hash('sha256', preg_replace('{\s}', '', file_get_contents('/tmp/keys.dev.pub'))); \
    if (!hash_equals(\$signature, \$hash)) { \
      echo 'Integrity check failed, dev public key is either corrupt or worse.' . PHP_EOL; \
      exit(1); \
    }" \
  ; \
  curl \
    --silent \
    --fail \
    --location \
    --retry 3 \
    --output /tmp/keys.tags.pub \
    --url https://raw.githubusercontent.com/composer/composer.github.io/e7f28b7200249f8e5bc912b42837d4598c74153a/releases.pub \
  ; \
  php -r " \
    \$signature = '57815ba27e54dc317ecc7cc5573090d087719ba68f3bb7234e5d42d084a14642'; \
    \$hash = hash('sha256', preg_replace('{\s}', '', file_get_contents('/tmp/keys.tags.pub'))); \
    if (!hash_equals(\$signature, \$hash)) { \
      echo 'Integrity check failed, tags public key is either corrupt or worse.' . PHP_EOL; \
      exit(1); \
    }" \
  ; \
  curl \
    --silent \
    --fail \
    --location \
    --retry 3 \
    --output /tmp/installer.php \
    --url https://raw.githubusercontent.com/composer/getcomposer.org/f24b8f860b95b52167f91bbd3e3a7bcafe043038/web/installer \
  ; \
  php -r " \
    \$signature = '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3'; \
    \$hash = hash('sha384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
      echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
      exit(1); \
    }" \
  ; \
  sudo php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION}; \
  composer --ansi --version --no-interaction; \
  composer diagnose; \
  rm -f /tmp/installer.php; \
  sudo find /tmp -type d -exec chmod -v 1777 {} +

# Configure Xdebug
RUN sudo bash -c "echo -e '\nzend_extension = /usr/lib/php/20190902/xdebug.so\n\n[XDebug]\nxdebug.mode=debug\nxdebug.start_with_request = 1\nxdebug.client_host = 127.0.0.1\n' >> /etc/php/7.4/cli/php.ini"