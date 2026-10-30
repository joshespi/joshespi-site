# Plan: Niche micro-SaaS — client intake & portal for one profession

**Goal:** a hosted intake + client portal tool for **one** profession at
$15–40/month. The target is 50–100 paying customers ($1.5k–3k MRR) over time.

**Budget:** ~6–9 months to a paid v1 at 8 hours/week. This is the biggest
plan; do it last, but start phase 0 early.

## The seed

This site already has the core loop: a public form, stored submissions, a
status workflow, internal notes, email notifications, and an admin inbox. The
SaaS version is that, **multi-tenant**, with a form each customer can
customize, plus a page where their clients can check status and upload files.

## Phase 0 — Pick the vertical (4–6 weeks, runs alongside other plans)

Shortlist professions that take structured inquiries, collect documents, and
are underserved by big tools:

- **Small bookkeeping / tax prep offices.** Document collection, seasonal
  rush, and they hate email attachments.
- **Residential contractors / handymen.** Quote requests with photos and
  addresses.
- **Home inspectors.** Booking details, property info, report delivery.
- **Freelance photographers.** Session inquiries, questionnaires, contracts.

**Avoid** anything regulated (therapists → HIPAA, lawyers → privilege/ethics
rules) unless you want compliance work.

Validation, per vertical:

- Talk to 5–10 people in it. Use Reddit, Facebook groups, local businesses,
  and existing clients. Ask how they collect intake today and what's annoying
  about it. Don't pitch.
- Look at what they use now (Google Forms, JotForm, email, a bloated CRM) and
  what that costs.
- **Pick the vertical where people describe the pain without prompting and
  already pay for a clumsy fix.**

Output: one page on the chosen vertical, their top 3 pains, and 3 people
willing to try a beta.

## Phase 1 — Foundation (6–8 weeks)

Start from the starter kit (if done), otherwise from this repo's code.

- Multi-tenancy: single database, `team_id` on every tenant table, a global
  scope, and a subdomain or path per tenant (`acme.yourapp.com` or
  `/f/acme`). Avoid database-per-tenant; it's overkill at this size.
- Tenant sign-up, owner + staff users, password reset, email verification.
- Test that tenants can't see each other's data, from day one.

## Phase 2 — The vertical's core (8–10 weeks)

- **Form builder:** start with templates for that profession plus toggling
  and reordering fields, not a full drag-and-drop builder.
- **Intake inbox:** port `IntakeIndex` / `IntakeShow`, status workflow, notes.
- **File uploads** to S3-compatible storage (Backblaze B2 or R2 is cheap),
  with virus scanning and size limits.
- **Client portal:** a magic-link page where the end client sees status, the
  outstanding items list, and can upload more files.
- **Email notifications** to tenant and client. Brevo works to start; move
  to Postmark or SES when volume grows.
- Embed snippet so tenants can put the form on their own site.

## Phase 3 — Billing and beta (3–4 weeks)

- Laravel Cashier (Stripe) or Lemon Squeezy subscriptions. Lemon Squeezy
  handles sales tax for you.
- Pricing to start: **Solo $19/mo, Team $39/mo**, 14-day trial, no free
  plan.
- Give the 3 beta users free access in exchange for weekly feedback. Fix
  what they hit before opening sign-ups.

## Phase 4 — Launch and grow (ongoing)

- Landing page written in the vertical's language, not developer language.
- Content aimed at that profession ("how to collect tax documents without
  email"). This is the writing plan pointed at a different audience.
- Show up in the communities you used for validation.
- Hosted on its own server/VPS, not the homelab. Paying customers need
  uptime, monitoring, and backups you don't have to think about. Reuse the
  backup scripts with offsite storage.

## Non-goals for v1

No mobile app, no integrations marketplace, no payments inside the portal,
no custom domains, no AI features. Add these only if paying customers ask.

## Risks

- **Building the wrong thing.** Phase 0 exists to prevent this. Don't skip it.
- **Support at 1–2 hours a night.** Keep features small. State support hours
  and email-only support on the pricing page.
- **Security and data.** Client documents are sensitive. Encrypt at rest,
  enforce tenant isolation tests, and keep audit logs of file access. Write
  privacy terms specific to the product.
- **Slow growth.** Expected. 10 customers in the first 6 months after launch
  is a normal start.

## First three nights

1. Write the shortlist and a 5-question interview script.
2. Find 3 communities per vertical where people talk shop.
3. Start conversations, and log what you hear in a simple table.
