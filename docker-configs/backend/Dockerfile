FROM spiralscout/roadrunner:2024.2 AS roadrunner
FROM php:8.3-cli-bookworm

RUN apt-get update
RUN apt-get install -y htop
RUN rm -rf /var/lib/apt/lists/*

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

CMD ["rr", "serve", "-c", ".rr.yaml"]
