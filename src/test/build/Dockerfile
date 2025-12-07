FROM arm64v8/php:8.4-cli
RUN apt-get update && apt-get install -y \
        libxslt-dev \
    && docker-php-ext-install xsl \
	&& rm -rf /var/lib/apt/lists/*