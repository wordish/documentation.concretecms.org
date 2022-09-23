ARG CLI_IMAGE
FROM ${CLI_IMAGE} as cli

FROM amazeeio/nginx-drupal

COPY --from=cli /app /app

RUN apk update && apk upgrade --all

# Define where the Drupal Root is located
ENV WEBROOT=public

