# joshespi.com

Self-hosted WordPress, Docker, behind host nginx.

## Ports

| Service | Host | Container |
|---|---|---|
| WordPress (php8.3-apache + WP-CLI) | `8081` | `80` |
| MariaDB 11.4 | `127.0.0.1:3308` | `3306` |

## First run

```bash
cp .env.example .env   # set real passwords
docker compose up -d --build
```

Install wizard: `http://localhost:8081`.

## Nginx vhost (live host)

`/etc/nginx/sites-available/joshespi.com`, then symlink into `sites-enabled/` and run `certbot --nginx -d joshespi.com -d www.joshespi.com`.

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name joshespi.com www.joshespi.com;

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

## Workbench

DB is loopback-only. Connect via **Standard TCP/IP over SSH** → MySQL host `127.0.0.1:3308`, user from `.env`.

## WP-CLI

```bash
docker exec -u www-data joshespi_web wp <command>
```

## Backups

`./scripts/backup.sh` writes timestamped `*.sql.gz` + `*.tar.gz` to `./backups/`, keeps last 14 (`KEEP_LAST=N` to override).

```cron
15 3 * * * cd /home/joshe/joshespi-site && ./scripts/backup.sh >> /var/log/joshespi-backup.log 2>&1
30 3 * * * rsync -a /home/joshe/joshespi-site/backups/ user@remote:/path/to/joshespi-backups/
```

## Restore

```bash
./scripts/restore.sh           # latest pair
./scripts/restore.sh 1         # one step back
./scripts/restore.sh 3         # three steps back
```

Wipes the current DB + `./wp-content` and restores from the matching pair. Stack must be up; web container is stopped/started around the wp-content extraction. Uses `sudo` for the wp-content wipe/extract/chown.

**Cross-host restore** (e.g. seeding prod from a dev backup) — after running restore, fix the baked-in URLs:

```bash
docker exec -u www-data joshespi_web wp search-replace \
  'http://localhost:8081' 'https://joshespi.com' --all-tables
```

Run this *after* DNS+TLS are live, or WP will redirect-loop you out of admin.
