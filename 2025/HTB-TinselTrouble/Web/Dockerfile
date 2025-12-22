FROM wordpress:latest

# Install WP-CLI and required dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    mariadb-server \
    mariadb-client \
    curl \
    && curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set default environment variables
ENV WORDPRESS_DB_HOST=127.0.0.1 \
    WORDPRESS_DB_USER=wordpress \
    WORDPRESS_DB_PASSWORD=wordpress \
    WORDPRESS_DB_NAME=wordpress \
    WORDPRESS_URL=http://localhost:80 \
    WORDPRESS_TITLE="The Tinselwick Chronicles" \
    WORDPRESS_ADMIN_USER=admin \
    WORDPRESS_ADMIN_EMAIL=admin@example.com

# Copy custom entrypoint script
COPY custom-entrypoint.sh /usr/local/bin/custom-entrypoint.sh
RUN chmod +x /usr/local/bin/custom-entrypoint.sh

COPY ./src/plugins/my-plugin /usr/src/wordpress/wp-content/plugins/my-plugin
COPY ./src/themes/my-theme /usr/src/wordpress/wp-content/themes/my-theme

COPY flag.txt /flag.txt

ENTRYPOINT ["custom-entrypoint.sh"]
CMD ["apache2-foreground"]

