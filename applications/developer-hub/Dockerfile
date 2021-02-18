FROM php:7.4-apache as base-web

RUN docker-php-ext-install pdo_mysql

# Use the new document root
ENV APP_ROOT_PATH /var/www/html/devhub
ENV APP_PATH ${APP_ROOT_PATH}/applications/developer-hub
ENV APACHE_DOCUMENT_ROOT ${APP_PATH}/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Required by "google cloud run"
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

#=======================================================================================================================
FROM php:7.4-cli as composer-prod-build

# install composer
RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip unzip

RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
# Make sure we're installing what we think we're installing!
&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot --version=1.10.10 \
&& rm -f /tmp/composer-setup.*

#Install dependencies
WORKDIR /app
COPY composer.lock ./
COPY composer.json ./
RUN composer install --no-dev

#=======================================================================================================================
FROM base-web as dev

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN rm -Rf /var/www/html/* # To ensure that, for development, local volume is used

#=======================================================================================================================
FROM base-web as prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Production source code
RUN mkdir -vp ${APP_ROOT_PATH}/src
COPY src/DeveloperHub ${APP_ROOT_PATH}/src/DeveloperHub
COPY src/Shared ${APP_ROOT_PATH}/src/Shared
COPY applications/developer-hub/config ${APP_PATH}/config
COPY applications/developer-hub/public ${APP_PATH}/public
COPY applications/developer-hub/bin ${APP_PATH}/bin
RUN touch ${APP_PATH}/.env
RUN mkdir -pv ${APP_PATH}/var/cache

# Vendors
COPY --from=composer-prod-build /app/vendor ${APP_ROOT_PATH}/vendor

# Dir perms
RUN chmod -R ugo+w ${APP_PATH}/var/ --quiet
