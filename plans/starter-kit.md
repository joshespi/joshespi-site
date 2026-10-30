# Plan: Laravel starter kit

**Goal:** package this site's reusable parts into a kit for freelancers and
small agencies, sold for $39–99 on Lemon Squeezy.

**Budget:** ~8–10 weeks at 8 hours/week.

## What's already here to package

| Piece | Source in this repo |
|---|---|
| Livewire intake form: validation, rate limit, accessible errors | `app/Livewire/IntakeForm.php`, `livewire/intake-form.blade.php` |
| Intake storage, status enum, internal notes | `Models/Intake.php`, `IntakeNote.php`, `Enums/IntakeStatus.php` |
| Admin login + intake inbox / detail | `Admin/LoginController.php`, `Livewire/Admin/*` |
| Notification + confirmation emails | `Mail/*`, `views/emails/*` |
| Artisan: `admin:user`, `intake:list`, `intake:resend` | `Console/Commands/*` |
| Branded 404 / 419 / 500 pages | `views/errors/*`, `components/error-hero` |
| Docker: php-fpm + nginx + MariaDB, idempotent entrypoint, maintenance page | `Dockerfile`, `docker/*`, `docker-compose.yml` |
| Backup / restore scripts | `scripts/backup.sh`, `restore.sh` |
| SEO: sitemap, JSON-LD, OG image generation | `SitemapController`, `partials/schema`, `scripts/generate-og-image.ps1` |
| Feature tests | `tests/Feature/*` |

That's most of a product. The work is making it generic and documenting it.

## Who it's for

Freelancers and small agencies who want a **"services site that takes
inquiries and manages them"** without building the boring parts. That's a
narrower and easier-to-sell pitch than a generic Laravel starter, which is a
crowded space with free official options.

## Tiers

- **Core — $39:** intake form, admin inbox, emails, commands, error pages,
  tests.
- **Pro — $79:** Core plus the Docker production stack, backup/restore,
  reverse-proxy docs, and SEO pieces.
- Optional later: **Agency license — $149** for unlimited client sites.

Use Lemon Squeezy rather than Gumroad. It acts as merchant of record, so it
handles sales tax/VAT for you.

## Phase 1 — Extract and genericize (3–4 weeks)

- New **private** repo `intake-kit`. Copy over, don't fork, so none of this
  site's history comes along.
- Strip joshespi branding. Move the name, colors, and logo into
  `config/kit.php` plus Tailwind theme tokens.
- Make the intake form's service list config-driven. It's hard-coded in
  `IntakeForm.php` right now.
- Replace this site's pages with a single neutral demo homepage.
- Make the proxy IP in `docker/nginx.conf` an env var instead of a hard-coded
  address.
- Keep the test suite green throughout.

## Phase 2 — Docs (2 weeks)

- README: a 10-minute quick start. Then one page each for the intake form,
  admin, emails, Docker in production, backups, and customizing branding.
- A "behind a reverse proxy" page (the same material as blog posts #1–2;
  write it once and reuse it).
- A short CHANGELOG and a license file (single-site and agency terms).

## Phase 3 — Sales page and launch (2 weeks)

- Live demo instance (admin login with a read-only demo account, reset
  nightly by cron).
- Product page on your site, `/kit`, with screenshots, the feature list,
  pricing, and an FAQ. Link the checkout to Lemon Squeezy.
- Delivery: GitHub repo access (Lemon Squeezy → GitHub invite via webhook or
  done manually at first) **or** a zip download. Start with the zip; it's
  simpler.
- Launch to the email list from the writing plan, plus r/laravel, Laravel
  News, and dev.to.

## Phase 4 — Maintain (1–2 hrs/month)

- Keep it current with Laravel and Livewire releases. Version-bump
  announcements are also good reasons to email the list again.
- Track the questions buyers ask and turn them into docs or blog posts.

## Risks

- **Support load.** Set an expectation up front: docs plus email, 30-day
  updates guarantee, no custom work. Custom work goes through `/services`.
- **Laravel churn.** Pin versions and state which ones are supported.
- **Low sales.** Fine. The kit is also the base of the course and the
  micro-SaaS, so the work isn't wasted.

## First three nights

1. Create the private repo and copy the app; get it booting under a new name.
2. Pull branding into `config/kit.php`.
3. Make the service list config-driven, and update the tests.
