# joshespi.com

Laravel 13 + Livewire, self-hosted in Docker, behind an nginx reverse proxy that
runs on a **different machine** and terminates TLS. Published on `8081`.

That split is load-bearing: the port must be reachable on the LAN, not just on
the docker host's loopback, or the proxy has nothing to connect to and every
request comes back 502. Set `JOSHESPI_BIND_ADDR` in `.env` to pin it to one
interface; it defaults to `0.0.0.0`. The proxy's address is also the only one
trusted by `set_real_ip_from` in `docker/nginx.conf` — update it there if the
proxy ever moves, or client IPs (and the intake form's rate limit) go wrong.

## Stack

- PHP 8.5-FPM + Laravel 13 + Livewire 4
- SQLite (dev) — MariaDB 11.4 (prod, `joshespi_db` container)
- nginx:alpine (inside compose)
- Tailwind CSS v4 via Vite
- Brevo SMTP (`smtp-relay.brevo.com:587`)

## First run

```bash
cp .env.example .env        # fill MAIL_*, INTAKE_NOTIFY_ADDRESS
docker compose build
docker compose up -d
docker compose exec joshespi_app php artisan key:generate --show   # paste into APP_KEY
docker compose up -d --force-recreate joshespi_app                 # pick up the key
docker compose exec -it joshespi_app php artisan admin:user you@example.com
```

`entrypoint.sh` does composer install, the Vite build and migrations on every
boot, skipping whatever hasn't changed. So there's no separate install, build or
migrate step. (`key:generate` needs `--show` because `.env` reaches the
container as env vars, not as a file it can write to.)

App at `http://localhost:8081`, admin at `/admin`.

## MariaDB (prod)

The database runs as the `joshespi_db` service under the `mariadb` compose
profile. That keeps a dev checkout on SQLite from needing DB passwords just to
boot. To turn it on, set these in `.env`:

```ini
COMPOSE_PROFILES=mariadb
DB_CONNECTION=mariadb
DB_HOST=joshespi_db
DB_DATABASE=joshespi
DB_USERNAME=joshespi
DB_PASSWORD=...
DB_ROOT_PASSWORD=...   # also what backup.sh / restore.sh log in with
```

Then `docker compose up -d`. The app waits for the DB's healthcheck and
migrates on boot. Data lives in `./joshespi_db_data` (gitignored), and the port
is never published.

## Assets (Tailwind/Vite)

Built on boot by `entrypoint.sh` whenever `package-lock.json` changes or
`public/build` is missing. To rebuild by hand after editing Blade or CSS:

```bash
docker compose exec joshespi_app npm run build
```

`node_modules` is baked into the image. After changing `package.json`, run
`docker compose build` so the image matches the new lockfile.

## Intakes

Every intake form submission is saved to the `intakes` table **before** any
email is sent. Mail only notifies: the full message, details and your notes
live in the admin at `/admin`. Each intake records whether its two emails went
out (the notification to `INTAKE_NOTIFY_ADDRESS` and the receipt to the
client). If Brevo is down or its key has lapsed, the lead is kept and the
failure is saved on the row.

```bash
docker compose exec joshespi_app php artisan intake:list             # newest 20
docker compose exec joshespi_app php artisan intake:list --failed    # emails that never went out
docker compose exec joshespi_app php artisan intake:resend           # retry every failed email
docker compose exec joshespi_app php artisan intake:resend 12 15     # just these intakes
docker compose exec joshespi_app php artisan intake:resend 12 --force --only=notification
```

Failed emails can also be resent from the intake's page in the admin, which
flags them on the list as well.

Admin logins come only from the command line (there's no sign-up). Run it again
with the same email to reset a password:

```bash
docker compose exec -it joshespi_app php artisan admin:user you@example.com
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

## Keep Brevo alive

Brevo expires SMTP keys after inactivity. Send a throwaway test email through
prod's Brevo relay periodically (e.g. every couple weeks) to keep it active:

PowerShell:

```powershell
docker compose exec joshespi_app php artisan tinker --execute="Illuminate\Support\Facades\Mail::raw('Brevo keepalive test - ' . now(), fn (`$m) => `$m->to(config('mail.from.address'))->subject('Brevo keepalive'));"
```

bash:

```bash
docker compose exec joshespi_app php artisan tinker --execute="Illuminate\Support\Facades\Mail::raw('Brevo keepalive test - ' . now(), fn (\$m) => \$m->to(config('mail.from.address'))->subject('Brevo keepalive'));"
```

Sends to `MAIL_FROM_ADDRESS`, so no extra recipient config needed. (PowerShell only treats
backtick as an escape character, not backslash, so `` `$ `` is needed there instead of `\$`.)

## Env vars

| Var                     | Notes                                          |
| ----------------------- | ---------------------------------------------- |
| `APP_KEY`               | `php artisan key:generate --show`, pasted in   |
| `APP_URL`               | Base for the admin link in notification emails |
| `MAIL_USERNAME`         | Brevo SMTP login; `null` for local Mailpit     |
| `MAIL_PASSWORD`         | Brevo SMTP API key; `null` for Mailpit         |
| `INTAKE_NOTIFY_ADDRESS` | Where new-intake notifications land            |
| `COMPOSE_PROFILES`      | `mariadb` to run the DB container (prod)       |
| `DB_*`                  | See [MariaDB (prod)](#mariadb-prod)            |
