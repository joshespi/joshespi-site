# joshespi.com

Laravel 12 + Livewire, self-hosted in Docker, behind host nginx. Port `0.0.0.0:8081`.

## Stack

- PHP 8.5-FPM + Laravel 12 + Livewire 4
- SQLite (dev) — MariaDB 11.4 when ready for prod
- nginx:alpine (inside compose)
- Tailwind CSS v4 via Vite
- Brevo SMTP (`smtp-relay.brevo.com:587`)

## First run

```bash
cp .env.example .env        # fill APP_KEY, MAIL_*, INTAKE_NOTIFY_ADDRESS
docker compose build
docker compose --profile build run --rm node npm install
docker compose --profile build run --rm node npm run build
docker compose up -d
docker compose exec joshespi_app php artisan migrate
```

App at `http://localhost:8081`.

## Assets (Tailwind/Vite)

Node runs one-shot via the `build` profile, never as a daemon.

```bash
docker compose --profile build run --rm node npm install   # after package.json changes
docker compose --profile build run --rm node npm run build
docker compose --profile build run --rm node npm run dev   # watch mode
```

## Day-to-day

```bash
docker compose exec joshespi_app php artisan <command>
docker compose logs -f joshespi_app
docker compose exec joshespi_app bash
```

## Backups

```bash
./scripts/backup.sh          # dump DB + storage/app → ./backups/, keeps last 14
./scripts/restore.sh         # restore latest pair
./scripts/restore.sh 1       # one step back
```

Cron on prod: `15 3 * * * cd /path/to/repo && ./scripts/backup.sh >> /var/log/joshespi-backup.log 2>&1`

## Env vars

| Var                     | Notes                                      |
|-------------------------|--------------------------------------------|
| `APP_KEY`               | `php artisan key:generate`                 |
| `MAIL_USERNAME`         | Brevo SMTP login; `null` for local Mailpit |
| `MAIL_PASSWORD`         | Brevo SMTP API key; `null` for Mailpit     |
| `INTAKE_NOTIFY_ADDRESS` | Where intake form emails land              |
