FROM mlocati/php-extension-installer AS ext
# It's important that this FROM matches main Dockerfile's FROM,
# since different base images could use different underlying libraries
FROM php:8.4-cli-bookworm

COPY --from=ext /usr/bin/install-php-extensions /usr/local/bin/install-php-extensions

RUN install-php-extensions grpc-1.72.0
RUN install-php-extensions protobuf-4.30.1
