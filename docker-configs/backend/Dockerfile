FROM spiralscout/roadrunner:2024.2 AS roadrunner
FROM example_project.backend.grpc:v1.0 AS grpc
FROM mlocati/php-extension-installer AS ext
FROM composer:2.8 AS composer
FROM php:8.3-cli-bookworm

ARG USER_NAME=app
ARG USER_ID=1000
ARG GROUP_ID=1000

COPY --from=ext /usr/bin/install-php-extensions /usr/local/bin/install-php-extensions
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

RUN install-php-extensions pgsql
RUN install-php-extensions redis
RUN install-php-extensions sockets
RUN install-php-extensions zip
RUN install-php-extensions amqp
RUN install-php-extensions swoole

# Copy grpc-extension binary
COPY --from=grpc \
	/usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so \
	/usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so

# Create docker-php-ext-grpc.ini configuration file
RUN docker-php-ext-enable grpc

# Copy protobuf-extension binary
COPY --from=grpc \
	/usr/local/lib/php/extensions/no-debug-non-zts-20230831/protobuf.so \
	/usr/local/lib/php/extensions/no-debug-non-zts-20230831/protobuf.so

# Create docker-php-ext-protobuf.ini configuration file
RUN docker-php-ext-enable protobuf

RUN apt-get update
RUN apt-get install -y htop
RUN apt-get install -y git zip
RUN rm -rf /var/lib/apt/lists/*

RUN groupadd --gid $GROUP_ID $USER_NAME
RUN useradd --uid $USER_ID --gid $GROUP_ID $USER_NAME

WORKDIR /app
USER $USER_NAME

CMD ["rr", "serve", "-c", ".rr.yaml"]
