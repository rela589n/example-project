FROM spiralscout/roadrunner:2025.1.6 AS roadrunner
FROM example_project.backend.grpc:v2.0 AS grpc
FROM mlocati/php-extension-installer AS ext
FROM composer:2.9 AS composer
FROM bufbuild/buf:1.65 AS buf
FROM php:8.5-cli-bookworm

ARG RR_VERSION=2025.1.6
ARG PROTOC_VERSION=33.5
ARG USER_NAME=app
ARG USER_ID=1000
ARG GROUP_ID=1000
ARG TARGETOS
ARG TARGETARCH

RUN apt-get update
RUN apt-get install -y git zip

COPY --from=ext /usr/bin/install-php-extensions /usr/local/bin/install-php-extensions
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=buf /usr/local/bin/buf /usr/local/bin/buf
COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

RUN install-php-extensions pdo_pgsql
RUN install-php-extensions redis
RUN install-php-extensions sockets
RUN install-php-extensions zip
RUN install-php-extensions amqp
# RUN install-php-extensions swoole # - (pecl/swoole requires PHP (version >= 8.1.0, version <= 8.4.99), installed version is 8.5.2)
RUN install-php-extensions xdebug # dev-only

# Copy grpc-extension binary
COPY --from=grpc \
	/usr/local/lib/php/extensions/no-debug-non-zts-20250925/grpc.so \
	/usr/local/lib/php/extensions/no-debug-non-zts-20250925/grpc.so

# Create docker-php-ext-grpc.ini configuration file
RUN docker-php-ext-enable grpc

# Copy protobuf-extension binary
COPY --from=grpc \
	/usr/local/lib/php/extensions/no-debug-non-zts-20250925/protobuf.so \
	/usr/local/lib/php/extensions/no-debug-non-zts-20250925/protobuf.so

# Create docker-php-ext-protobuf.ini configuration file
RUN docker-php-ext-enable protobuf

# Install protobuf compiler (dev-only, https://protobuf.dev/installation/)
RUN PROTOC_ARCH=$(echo "${TARGETARCH}" | sed 's/amd64/x86_64/; s/arm64/aarch_64/') && \
    PROTOC_ZIP=protoc-$PROTOC_VERSION-$TARGETOS-$PROTOC_ARCH.zip && \
    curl -LO https://github.com/protocolbuffers/protobuf/releases/download/v$PROTOC_VERSION/$PROTOC_ZIP && \
    unzip $PROTOC_ZIP -d /opt/protoc && \
    rm $PROTOC_ZIP && \
    ln -s /opt/protoc/bin/protoc /usr/local/bin/protoc

# Install protoc-gen-php-grpc plugin (dev-only)
RUN PROTOC_PLUGIN_PHP_TAR=protoc-gen-php-grpc-$RR_VERSION-$TARGETOS-$TARGETARCH.tar.gz && \
	curl -LO https://github.com/roadrunner-server/roadrunner/releases/download/v$RR_VERSION/$PROTOC_PLUGIN_PHP_TAR && \
    tar -xzf $PROTOC_PLUGIN_PHP_TAR --strip-components=1 -C /opt/protoc/bin && \
    rm $PROTOC_PLUGIN_PHP_TAR && \
    ln -s /opt/protoc/bin/protoc-gen-php-grpc /usr/local/bin/protoc-gen-php-grpc

RUN apt-get update
RUN apt-get install -y htop telnet
RUN apt-get install -y bash-completion

RUN groupadd --gid $GROUP_ID $USER_NAME
RUN useradd --uid $USER_ID --gid $GROUP_ID --create-home $USER_NAME
RUN apt-get install -y sudo && \
    echo "$USER_NAME ALL=(ALL) NOPASSWD: ALL" > /etc/sudoers.d/$USER_NAME

RUN echo '[ -f ~/.bashrc.local ] && source ~/.bashrc.local' >> /home/$USER_NAME/.bashrc

RUN rm -rf /var/lib/apt/lists/*

WORKDIR /app
USER $USER_NAME

CMD ["rr", "serve", "-c", ".rr.dev.yaml"]
