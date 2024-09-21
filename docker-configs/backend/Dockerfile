FROM spiralscout/roadrunner:2024.2 AS roadrunner
FROM composer:2.7 AS composer
FROM php:8.3-cli-bookworm

RUN apt-get update
RUN apt-get install -y htop
RUN rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

CMD ["rr", "serve", "-c", ".rr.yaml"]
