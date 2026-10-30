# Plan: Course — "Self-host Laravel with Docker behind a reverse proxy"

**Goal:** a small, practical paid course ($49–99) that takes someone from a
working local Laravel app to a production deploy on their own hardware or VPS.

**Budget:** ~10–14 weeks at 8 hours/week, starting **after** the blog has
posts on this topic.

## Validate before building

Don't write the course until there are signals:

- 2–3 blog posts on Docker / proxy topics are getting steady search traffic.
- A "course waitlist" signup on those posts has **50+ signups**.
- Optional: a presale at a discount. If 10 people pay, build it. If not, the
  blog posts still earn on their own.

## Who it's for

Laravel developers who can build apps but deploy with Forge, Ploi, or shared
hosting, and want to run their own stack, whether to save money or to learn.
Also homelab people with a reverse proxy who keep hitting 502s.

## Outline (project-based: deploy one real app start to finish)

1. **The architecture.** App host vs proxy host, why they're split, how TLS
   termination works.
2. **Dockerizing Laravel.** php-fpm + nginx + database in compose, and a
   multi-stage Dockerfile.
3. **An idempotent entrypoint.** Composer, Vite, and migrations on boot.
4. **Config and secrets.** Env vars vs `.env` files, `APP_KEY`, keeping
   secrets out of images.
5. **The reverse proxy.** Binding to the LAN, the 502 trap, upstream config.
6. **Real client IPs.** `set_real_ip_from`, Laravel trusted proxies, and why
   rate limits break without them.
7. **Database in production.** MariaDB container, volumes, and moving off
   SQLite.
8. **Backups and restores.** Scripted backups, offsite copies, and actually
   testing a restore.
9. **Mail.** SMTP relay (Brevo), SPF/DKIM basics.
10. **Deploying updates.** Maintenance page, zero-surprise rebuilds, and
    rolling back.

Each module: a short written lesson, the exact commands, a "what breaks and
how you'll know" section, and the companion repo at a tagged commit.

## Format

- **v1: written + companion repo.** Faster to make and easier to update than
  video. Sell it as an ebook/PDF + repo on Lemon Squeezy.
- **v2: add short videos** (5–10 min per module) once v1 sells. Raise the
  price when the videos land.
- Companion code is the starter kit's Pro tier. Course buyers get it
  included, or a kit discount, which cross-sells both.

## Timeline

- **Weeks 1–2:** outline, companion repo tags per module, waitlist page.
- **Weeks 3–10:** one module a week (~8 hrs each, with the blog posts as
  first drafts for modules 3, 4, 5, 6 and 8).
- **Weeks 11–12:** editing, a beta read by 3–5 people from the waitlist
  (free copy in exchange for feedback), fixes.
- **Weeks 13–14:** sales page at `/course`, launch email to the list, and
  community posts.

## Pricing

- Presale: $39.
- Launch: $59 written; $99 once videos are added.
- Bundle with kit Pro: $119.

## Risks

- **Too broad.** Stick to one stack (Laravel + nginx + MariaDB + Docker
  Compose). No Kubernetes, no "also for Rails".
- **Goes stale.** Pin versions in the repo and do a yearly refresh.
- **Free alternatives exist.** The selling point is the specific
  proxy-on-another-machine setup and the "what breaks" sections, which free
  tutorials skip.

## First three nights (once validation passes)

1. Write a one-paragraph promise plus the module list, and add the waitlist
   form.
2. Set up the companion repo with a tag for module 1.
3. Draft module 1 (the architecture), with a diagram.
