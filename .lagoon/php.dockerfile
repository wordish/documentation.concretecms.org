ARG CLI_IMAGE
FROM ${CLI_IMAGE} as cli

FROM amazeeio/php:7.4-fpm

ENV CONCRETE5_ENV=lagoon

COPY --from=cli /app /app

# Install required extensions
RUN docker-php-ext-install intl
