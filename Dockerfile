FROM php:7.2

RUN apt update && \
    apt install -y \
        autoconf \
        automake \
        libtool \
        libzip-dev \
        m4 && \
    docker-php-ext-install zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer global require aerospike/aerospike-client-php ~7.0 && \
    cd /root/.composer/vendor/aerospike/aerospike-client-php/src/ && \
    chmod +x **/*.sh && \
    bash build.sh && \
#    find . -name "*.sh" -exec chmod +x {} \; && \
    docker-php-ext-install $(pwd) && \
    docker-php-ext-configure $(pwd) && \
    make build && \
    docker-php-ext-enable aerospike