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
echo '[railway-entrypoint] build: v8-admin-reset-fix'

# --- 0. Force a single MPM (prefork) at RUNTIME --------------------------------------
# The base image ships with both mpm_event and mpm_prefork enabled -> Apache aborts with
# "AH00534: More than one MPM loaded". Doing this at runtime (not in the Dockerfile) means
# it can't be defeated by build-layer caching — it always applies to the running container.
echo '[railway-entrypoint] normalizing Apache MPM -> prefork'
rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf
a2enmod mpm_prefork >/dev/null 2>&1 || true
echo '[railway-entrypoint] MPM now enabled:'
ls -1 /etc/apache2/mods-enabled/ 2>/dev/null | grep -i mpm | sed 's/^/  /' || echo '  (none)'

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

	# import.php: 0 = freshly imported, 5 = already seeded, anything else = failure.
	# Capture the exit code WITHOUT tripping `set -e` — exit 5 (already seeded) is the normal
	# steady state, and a bare `php …` would abort the whole function there, so the admin
	# override below never ran on any boot after the first.
	local rc=0
	php /seed/import.php || rc=$?

	if [ "$rc" -ne 0 ] && [ "$rc" -ne 5 ]; then
		echo "[seed] Seeder exited ${rc} — leaving database as-is, skipping admin setup."
		return 0
	fi

	if [ "$rc" -eq 0 ]; then
		# Freshly imported. The mu-plugin already forces home/siteurl to the Railway domain
		# at runtime; also rewrite absolute references baked into post content / GUIDs so
		# nothing points back at art4development.local.
		local domain="${RAILWAY_PUBLIC_DOMAIN:-localhost}"
		echo "[seed] Rewriting art4development.local -> ${domain}"
		$WP search-replace 'http://art4development.local' "https://${domain}" --all-tables --report-changed-only >/dev/null 2>&1 || true
		$WP search-replace 'art4development.local' "${domain}" --all-tables --report-changed-only >/dev/null 2>&1 || true
		$WP cache flush >/dev/null 2>&1 || true
		$WP rewrite flush >/dev/null 2>&1 || true
		echo '[seed] Done.'
	else
		echo '[seed] Database already seeded — nothing to import.'
	fi

	# --- Optional admin credential override (Railway env) ---------------------------
	# Set WP_ADMIN_PASSWORD in Railway (and optionally WP_ADMIN_USER / WP_ADMIN_EMAIL) to
	# (re)set the admin login on boot — handy if the seeded password is lost. The seeded
	# admin is 'art4dev-admin'. Runs on every boot while the var is set, so remove it again
	# once you're logged in.
	if [ -n "${WP_ADMIN_PASSWORD:-}" ]; then
		local admin_user="${WP_ADMIN_USER:-art4dev-admin}"
		if wp --path=/var/www/html --allow-root user get "$admin_user" --field=ID >/dev/null 2>&1; then
			if wp --path=/var/www/html --allow-root user update "$admin_user" --user_pass="$WP_ADMIN_PASSWORD" >/dev/null 2>&1; then
				echo "[admin] password reset for existing administrator '${admin_user}'"
			else
				echo "[admin] FAILED to reset password for '${admin_user}'"
			fi
			if [ -n "${WP_ADMIN_EMAIL:-}" ]; then
				wp --path=/var/www/html --allow-root user update "$admin_user" --user_email="$WP_ADMIN_EMAIL" >/dev/null 2>&1 || true
			fi
		else
			if wp --path=/var/www/html --allow-root user create "$admin_user" "${WP_ADMIN_EMAIL:-admin@example.com}" --role=administrator --user_pass="$WP_ADMIN_PASSWORD" >/dev/null 2>&1; then
				echo "[admin] created administrator '${admin_user}'"
			else
				echo "[admin] FAILED to create administrator '${admin_user}'"
			fi
		fi
	else
		echo "[admin] WP_ADMIN_PASSWORD not set — skipping admin credential override."
	fi
}
seed_database &

# --- 3. Hand off to the stock WordPress entrypoint --------------------------------
exec docker-entrypoint.sh "$@"
