# joshespi.com

Self-hosted WordPress for joshespi.com. Runs in Docker behind a host nginx reverse proxy.

## Stack

- **Web**: WordPress (php8.3-apache) + WP-CLI, hardened PHP via `conf/php.custom.ini`
- **DB**: MariaDB 11.4
- **Reverse proxy**: host nginx (not in compose) → `127.0.0.1:8081`

## Ports

| Service | Host | Container |
|---|---|---|
| WordPress | `8081` | `80` |
| MariaDB | `127.0.0.1:3308` (loopback only) | `3306` |

## First-time setup

```bash
cp .env.example .env
# edit .env and set real passwords

docker compose build
docker compose up -d
```

WordPress install wizard will be at `http://localhost:8081` on this host.

## Nginx vhost (live host)

Drop into `/etc/nginx/sites-available/joshespi.com` and symlink to `sites-enabled/`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name joshespi.com www.joshespi.com;

    # Let certbot handle the redirect to HTTPS once TLS is set up.
    location / {
        proxy_pass http://127.0.0.1:8081;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    client_max_body_size 512M;
}
```

After `certbot --nginx -d joshespi.com -d www.joshespi.com`, certbot will rewrite this to a 443 server with HTTP→HTTPS redirect.

## Connecting MySQL Workbench

DB port is bound to loopback only on the host. Use Workbench's **Standard TCP/IP over SSH**:

- SSH Hostname: live host
- SSH Username: your user
- MySQL Hostname: `127.0.0.1`
- MySQL Port: `3308`
- MySQL Username: value of `MYSQL_USER` in `.env`

## WP-CLI

WP-CLI is baked into the web image. Run it as `www-data`:

```bash
docker exec -u www-data joshespi_web wp --info
docker exec -u www-data joshespi_web wp plugin list
docker exec -u www-data joshespi_web wp theme activate joshespi-theme
```

## Backups

```bash
./scripts/backup.sh
```

Writes timestamped `*.sql.gz` and `*.tar.gz` to `./backups/`, prunes to last 14 of each (override with `KEEP_LAST=N`).

Cron suggestion (live host):

```cron
15 3 * * * cd /home/joshe/joshespi-site && ./scripts/backup.sh >> /var/log/joshespi-backup.log 2>&1
30 3 * * * rsync -a /home/joshe/joshespi-site/backups/ user@remote:/path/to/joshespi-backups/
```

## Common operations

```bash
# Tail logs
docker compose logs -f joshespi_web

# Update images (WordPress core updates handled in admin; this updates the OS layer)
docker compose pull && docker compose up -d

# Stop / start
docker compose down
docker compose up -d

# Shell into web container as www-data
docker exec -it -u www-data joshespi_web bash
```
