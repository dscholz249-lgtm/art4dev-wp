#!/usr/bin/env bash
#
# Railway entrypoint for the Arts for Global Development WordPress image.
#
# Wraps the stock `wordpress` image entrypoint to do two Railway-specific things:
#   1. Bind Apache to Railway's assigned $PORT (the image hardcodes :80).
#   2. Seed the database from the baked SQL dump on the very first boot.
#
# The stock entrypoint (docker-entrypoint.sh) still does the heavy lifting: it copies
# WordPress core + our baked wp-content into /var/www/html and writes wp-config.php
# from the WORDPRESS_DB_* env vars. We hand off to it with `exec` at the end.
#
set -euo pipefail

# Build marker — if this line is absent from the deploy logs, Railway is running a stale
# image and didn't rebuild from the latest commit.
echo '[railway-entrypoint] build: mpm-fix-4'

# Diagnostic: show every MPM the Apache config would load. If more than one line
# appears here, that's the source of AH00534 and tells us exactly which file to fix.
echo '[railway-entrypoint] MPM modules in mods-enabled:'
ls -1 /etc/apache2/mods-enabled/ 2>/dev/null | grep -i mpm | sed 's/^/  /' || echo '  (none)'
echo '[railway-entrypoint] any mpm LoadModule elsewhere in the config:'
grep -rniE 'LoadModule .*mpm' /etc/apache2/ 2>/dev/null | sed 's/^/  /' || echo '  (none)'

# --- 1. Apache listens on Railway's $PORT (defaults to 80 for local runs) ---------
: "${PORT:=80}"
sed -ri "s!^Listen 80\$!Listen ${PORT}!" /etc/apache2/ports.conf || true
sed -ri "s!:80>!:${PORT}>!" /etc/apache2/sites-enabled/000-default.conf || true

# --- 2. First-boot database seed (backgrounded; no-op once seeded) -----------------
# Runs as a detached child so it can wait for the stock entrypoint (exec'd below) to
# write wp-config.php, then seed via PHP mysqli while Apache comes up. The Railway MySQL
# volume is persistent, so the import only ever happens on the first deploy.
seed_database() {
	local WP="wp --path=/var/www/html --allow-root"

	# Wait for the stock entrypoint to generate wp-config.php.
	local i=0
	until [ -f /var/www/html/wp-config.php ]; do
		sleep 1; i=$((i + 1))
		[ "$i" -gt 90 ] && { echo '[seed] wp-config.php never appeared — giving up.'; return 0; }
	done

	# import.php waits for the DB, then imports only if the database is empty.
	php /seed/import.php
	local rc=$?

	if [ "$rc" -eq 5 ]; then
		echo '[seed] Database already seeded — nothing to do.'
		return 0
	fi
	if [ "$rc" -ne 0 ]; then
		echo "[seed] Seeder exited ${rc} — leaving database as-is."
		return 0
	fi

	# Freshly imported. The seed carries the local dev domain; the mu-plugin already forces
	# home/siteurl to the Railway domain at runtime, but rewrite absolute references baked
	# into post content / GUIDs so nothing points back at art4development.local.
	local domain="${RAILWAY_PUBLIC_DOMAIN:-localhost}"
	echo "[seed] Rewriting art4development.local -> ${domain}"
	$WP search-replace 'http://art4development.local' "https://${domain}" --all-tables --report-changed-only >/dev/null 2>&1 || true
	$WP search-replace 'art4development.local' "${domain}" --all-tables --report-changed-only >/dev/null 2>&1 || true
	$WP cache flush >/dev/null 2>&1 || true
	$WP rewrite flush >/dev/null 2>&1 || true
	echo '[seed] Done.'
}
seed_database &

# --- 3. Hand off to the stock WordPress entrypoint --------------------------------
exec docker-entrypoint.sh "$@"
