# Arts for Global Development — WordPress image for Railway.
#
# Bakes the custom block theme, ACF, the placeholder upload, and a Railway adapter
# mu-plugin into the WordPress source tree. The stock entrypoint copies that tree
# into /var/www/html alongside core on first boot, so everything ships in the image
# and survives redeploys — no manual theme upload, no content import step.
#
# php8.2-apache matches the local LocalWP stack (PHP 8.2). The `wordpress` tag tracks
# the latest core; WordPress runs its own minor DB upgrade if the seed's schema is older.
FROM wordpress:php8.2-apache

# wp-cli + the mysql client are needed by the first-boot seed (db import / search-replace).
RUN set -eux; \
	apt-get update; \
	apt-get install -y --no-install-recommends curl ca-certificates default-mysql-client; \
	rm -rf /var/lib/apt/lists/*; \
	curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar; \
	chmod +x /usr/local/bin/wp

# Bake our content into /usr/src/wordpress — the stock entrypoint copies this into
# /var/www/html together with core, so a non-empty wp-content doesn't block that copy.
COPY wp-content/themes/art4dev                 /usr/src/wordpress/wp-content/themes/art4dev
COPY wp-content/plugins/advanced-custom-fields /usr/src/wordpress/wp-content/plugins/advanced-custom-fields
COPY wp-content/plugins/wordpress-importer     /usr/src/wordpress/wp-content/plugins/wordpress-importer
COPY wp-content/uploads                        /usr/src/wordpress/wp-content/uploads
COPY wp-content/mu-plugins                     /usr/src/wordpress/wp-content/mu-plugins

# First-boot database seed.
COPY seed/database.sql /seed/database.sql

COPY railway-entrypoint.sh /usr/local/bin/railway-entrypoint.sh
RUN chmod +x /usr/local/bin/railway-entrypoint.sh

ENTRYPOINT ["railway-entrypoint.sh"]
CMD ["apache2-foreground"]
