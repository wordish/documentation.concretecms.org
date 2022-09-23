FROM amazeeio/php:7.4-cli

RUN apk update && apk upgrade --all

# Use composer v2
RUN composer global remove hirak/prestissimo
RUN composer self-update --2

# Install PHP extensions
RUN docker-php-ext-install intl

# Add tusc
RUN curl -LO https://github.com/jackhftang/tusc/releases/download/0.4.7/tusc_linux_amd64 && mv tusc_linux_amd64 /usr/bin/tusc && chmod +x /usr/bin/tusc

# Pull in concrete console
RUN curl -L -o /usr/local/bin/concrete https://github.com/concrete5/console/releases/latest/download/concrete.phar && chmod +x /usr/local/bin/concrete

COPY . /app
RUN php -d memory_limit=-1 `which composer` install --no-dev -o

# Set up symlinks
RUN rm -rf /app/public/application/files
RUN rm -rf /app/public/application/config/generated_overrides
RUN rm -rf /app/public/application/config/doctrine

RUN mkdir -p /storage/files
RUN mkdir -p /storage/generated_overrides
RUN mkdir -p /app/public/application/config/doctrine

RUN ln -sf /storage/files /app/public/application/files
RUN ln -sf /storage/proxies /app/public/application/config/doctrine/proxies
RUN ln -sf /storage/generated_overrides /app/public/application/config/generated_overrides

# Define where the webroot is located
ENV WEBROOT=public
