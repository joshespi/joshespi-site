# Plan: Writing & video

**Goal:** publish what you actually build so it brings in service leads,
affiliate income, and an audience for the kit and course later.

**Budget:** one post every two weeks (~4–6 hours each, split across 3–4 nights).

## Why this first

- It's cheap: the material already exists in this repo and its commit history.
- It tells you which topics people want before you spend months on a course.
- Every post can link to `/services` (retainers, Dockerizing, server setup) —
  that's the lead engine.

## Phase 0 — Add a blog to the site (2–3 weeks)

The site has no blog yet. Keep it boring:

- Markdown files in `src/resources/content/posts/*.md` with front matter
  (title, date, description, tags). No database, no admin UI.
- Routes: `/blog` (index) and `/blog/{slug}`. Parse with
  `league/commonmark` (already a Laravel dependency).
- Add posts to the sitemap and an `Article` JSON-LD block via the existing
  `<x-json-ld>` component.
- RSS feed at `/blog/feed.xml`.
- OG image per post — reuse/extend `scripts/generate-og-image.ps1`.
- End-of-post CTA partial: "Need this done for you? → /services#intake".
- Tests in `tests/Feature/RoutesTest.php` for index, post, 404 on bad slug.

## Phase 1 — First 6 posts (12 weeks)

All taken from things already solved here:

1. **Your Laravel app 502s behind a reverse proxy on another machine.** The
   bind-address gotcha, `JOSHESPI_BIND_ADDR`, and LAN vs loopback.
2. **Rate limiting gives the wrong IP behind a proxy.** `set_real_ip_from`,
   trusted proxies, and why the intake form's rate limit depends on it.
3. **`key:generate` can't write `.env` in Docker.** Env vars vs files, `--show`.
4. **An idempotent Docker entrypoint for Laravel.** Composer, Vite, and
   migrations on every boot, skipping what hasn't changed.
5. **Backing up MariaDB in Docker with a 30-line script.** `scripts/backup.sh`
   and `restore.sh`, plus testing a restore.
6. **Accessible Livewire form errors.** Linking errors to fields with
   `aria-describedby` (from commit d285500).

Each post: the problem, the error message people would google, the fix, and
the why. Lead with the exact error text, since that's what people search for.

## Phase 2 — Distribution (ongoing, ~30 min per post)

- Cross-post to dev.to and Hashnode with `canonical_url` pointing to your site.
- Share in r/laravel, r/selfhosted, Laravel News "community links", and the
  Laracasts forum, but only where the post answers a question someone asked.
- Collect emails with a simple "get new posts" form. Brevo is already set up
  for SMTP, and its free tier covers lists too. This list is the launch
  audience for the kit and course.

## Phase 3 — Video (optional, after ~6 posts)

- Turn the top 2 posts by traffic into 5–10 min screen recordings. OBS is
  free. No face cam needed.
- YouTube, linking back to the post. Embed the video in the post.
- Only continue if the videos get more reach than the posts.

## Money

- **Affiliate links** only for things you actually use, with a disclosure
  line on each post. Candidates: VPS hosts (Hetzner, DigitalOcean, Vultr
  referral programs) and Brevo. Expect small amounts at first; it grows with
  traffic.
- **Leads:** the CTA partial. Track it by adding a "How did you hear about me?"
  option (or a `?ref=blog` source) to the intake form so you can see which
  posts produce clients.

## Metrics to check monthly

- Visits per post (add privacy-friendly analytics: Plausible or self-hosted
  Umami, which fits the Docker setup).
- Email signups.
- Intakes that came from the blog.

## First three nights

1. Add the content folder, the Markdown parser, and the `/blog/{slug}` route
   with one placeholder post.
2. Add the index page, sitemap entries, and tests.
3. Draft post #1 (the 502 gotcha).
