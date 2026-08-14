# Arts for Global Development — Railway deployment

A self-contained WordPress image for deploying the site to [Railway](https://railway.com).
The custom theme, ACF, the placeholder image, and a full snapshot of the local database are
all baked in — so a fresh deploy comes up as a complete, populated copy of the local site with
no manual theme upload or content import.

## What's in here

| Path | Purpose |
|------|---------|
| `Dockerfile` | Builds `FROM wordpress:php8.2-apache`; bakes in theme + plugins + uploads + seed |
| `railway-entrypoint.sh` | Binds Apache to Railway's `$PORT`; seeds the DB on first boot |
| `wp-content/themes/art4dev/` | The block theme |
| `wp-content/plugins/` | ACF (required) + WordPress Importer |
| `wp-content/mu-plugins/railway.php` | Makes WP work behind Railway's HTTPS proxy; forces the URL to the Railway domain |
| `wp-content/uploads/` | The placeholder image (attachment ID 1) |
| `seed/database.sql` | Full snapshot of the local database (all 48 posts, ACF fields, settings) |
| `railway.json` | Tells Railway to build with the Dockerfile |

## Deploy (one time)

### 1. Push this folder to a new GitHub repo

From inside `deploy/` (git is already initialised here):

```bash
git remote add origin https://github.com/<you>/art4dev-wp.git
git push -u origin main
```

### 2. Create the Railway project

1. Railway → **New Project** → **Deploy from GitHub repo** → pick the repo.
   Railway detects the `Dockerfile` and starts a build. It will fail/crash-loop until the
   database exists and is linked — that's expected; finish the next steps first.
2. In the project, **New** → **Database** → **Add MySQL**.

### 3. Link WordPress to MySQL

Open the **WordPress service → Variables** and add these (the `${{MySQL.*}}` bits are
Railway *reference variables* — type them exactly; Railway resolves them):

| Variable | Value |
|----------|-------|
| `WORDPRESS_DB_HOST` | `${{MySQL.RAILWAY_PRIVATE_DOMAIN}}:3306` |
| `WORDPRESS_DB_USER` | `${{MySQL.MYSQLUSER}}` |
| `WORDPRESS_DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |
| `WORDPRESS_DB_NAME` | `${{MySQL.MYSQLDATABASE}}` |

> Using `RAILWAY_PRIVATE_DOMAIN` keeps DB traffic on Railway's private network (faster, and it
> doesn't burn egress). The seed dump has no hardcoded database name, so it imports cleanly into
> whatever `WORDPRESS_DB_NAME` points at.

### 4. Give it a public URL

WordPress service → **Settings → Networking → Generate Domain**. Railway injects that hostname as
`RAILWAY_PUBLIC_DOMAIN`, which the entrypoint and the mu-plugin use automatically — no URL config
needed. Redeploy the WordPress service (**Deploy**) so it boots with the DB variables and domain
in place.

### 5. Watch it come up

First boot: the entrypoint waits for the DB, imports `seed/database.sql`, rewrites
`art4development.local` → your Railway domain, and flushes rewrites. Watch the deploy **Logs** for
`[seed] Done.` Then open the generated URL — you should see the full site.

**Admin:** the local admin account came over in the seed. Log in at `/wp-admin/` with the same
username/password you use on the LocalWP site. (Change the password once you're in — this is a
public URL now.)

## Updating later

- **Theme/code change:** re-copy the theme into `deploy/wp-content/themes/art4dev/`, commit, push.
  Railway rebuilds and redeploys. The database is untouched (seed only runs on an empty DB).
- **Refresh the content snapshot:** re-export the local DB over `seed/database.sql` (see the
  command in the project WORKLOG), commit, push — but note the seed **only imports into an empty
  database**. To reseed an existing Railway DB you'd wipe its tables first, or just edit content
  directly in the live wp-admin.

## Known limitations (fine for a preview, worth knowing)

- **Uploads added later are ephemeral.** New media uploaded through wp-admin writes to the
  container filesystem, which resets on redeploy. The baked placeholder always survives. To make
  new uploads persistent, add a Railway **Volume** mounted at `/var/www/html/wp-content/uploads`
  on the WordPress service.
- **Not free long-term.** Railway's trial credit is limited; sustained hosting is the Hobby plan
  (~$5/mo). This is a preview/staging step ahead of the eventual SiteGround/Bluehost move.
- **Plugins installed via wp-admin are ephemeral** for the same reason — bake anything you want to
  keep into the `Dockerfile` instead.
