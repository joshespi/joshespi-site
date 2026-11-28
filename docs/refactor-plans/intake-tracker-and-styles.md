# Refactor plan: intake delivery and public-site styles

Came out of a review of the Tradesman Tracker push and the "studio" portfolio redesign (2026-11-24).
Each section stands alone; pick one per session.

## 1. Move intake emails and the tracker push onto a queue

**Why:** `IntakeForm::submit()` runs both emails and the tracker push in one `defer()` closure. The PHP-FPM worker stays busy for SMTP plus up to 5 seconds for the tracker request. Only the Livewire form triggers it, so intakes created any other way need a manual push. Retries are manual (`intake:resend`, `intake:push`).

**Where:** `src/app/Livewire/IntakeForm.php` (the `defer` block), `src/app/Models/Intake.php` (`deliver()`, `pushToTracker()`), `src/app/Console/Commands/IntakeResend.php`, `IntakePush.php`.

**Proposed approach:** One queued job per side effect (`SendIntakeEmail`, `PushIntakeToTracker`) with `tries`/`backoff`, dispatched from `Intake::created` (skip spam) or from the form. `QUEUE_CONNECTION=database` is already in `.env.example`. Keep the `*_sent_at`/`*_error` columns as the record; the commands become a fallback.

Both delivery methods record failures and return `false`. Skip the tracker job when `Intake::trackerConfigured()` is false. For attempted deliveries that return `false`, throw from the job so the worker applies its retry policy.

**Risks / open questions:** Production needs a queue worker (container/supervisor). Is that acceptable, or should it stay `defer()`? Should the emails and the push move together? They should, for consistency.

**Suggested scope:** In: the jobs, dispatch point, worker in docker-compose, and the test updates. Out: any change to what the emails or payload contain.

## 2. Heading typography and the label utility

**Why:** The unlayered `.studio h1, .studio h2`, `.studio section > div > h2`, and `.page-masthead h1` rules override Tailwind utilities, because Tailwind v4 utilities live in a layer. Markup classes such as `text-2xl` on nested h2s now fight the CSS, and the `section > div > h2` selector is structural and fragile. Separately, `font-mono text-brand text-xs tracking-widest uppercase` appears ~17 times, while the new `.studio-label` does nearly the same job.

**Where:** `src/resources/css/app.css` ("Public portfolio" block), every public blade (`home`, `about`, `services`, `work`, `privacy`, `terms`), `components/page-masthead.blade.php`.

**Proposed approach:** Add `--font-display` to `@theme` (giving a `font-display` utility) and replace the bare `--display` var. Move the base heading style into `@layer base` so utilities win again. Replace the structural h2 selector with a named `@utility section-heading`. Add a `@utility eyebrow` and use it for every label.

**Risks / open questions:** Visual regressions. Diff screenshots of each page before and after. Decide whether the admin should share the palette; today `.studio` scopes the new tokens, so the admin keeps the `@theme` ones.

**Suggested scope:** In: public pages and CSS. Out: admin restyle, copy changes.
