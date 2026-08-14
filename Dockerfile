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

# wp-cli, bundled directly (used by the seed's search-replace / flush, which run via mysqli —
# no mysql binary needed). Bundling the phar avoids any apt/curl/network during the build: that
# step was both flaky AND the thing that pulled Debian's apache2 meta-package, which re-enabled
# a second MPM and crashed Apache. No apt in this image at all now.
COPY bin/wp-cli.phar /usr/local/bin/wp
RUN chmod +x /usr/local/bin/wp

# Belt-and-suspenders: ensure exactly one MPM (prefork) in case the base image ships two.
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
	&& a2enmod mpm_prefork \
	&& { echo "MPM modules enabled after fix:"; ls -1 /etc/apache2/mods-enabled/ | grep -i mpm || true; }

# First-boot database seeder (PHP mysqli — no mysql client needed).
COPY seed/import.php /seed/import.php

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
