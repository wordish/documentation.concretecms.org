ARG CLI_IMAGE
FROM ${CLI_IMAGE} as cli

FROM amazeeio/php:8.2-fpm

ENV CONCRETE5_ENV=lagoon

COPY --from=cli /app /app

RUN apk update && apk upgrade --all

# Install required extensions
RUN docker-php-ext-install intl
