# Tillora — Product Marketing Context

## What it is

Tillora is a multi-branch retail cloud point-of-sale (POS) SaaS. A shop
owner signs up online, picks a plan, and gets their own private, fully
isolated running instance of the POS app automatically — no manual setup,
no sales call. Every customer's instance is a separate deployment (own
database, own container) on a subdomain of the platform's own domain, not
a shared multi-tenant app.

## Stage

Pre-launch / soft-launch. Real infrastructure is live and working
end-to-end, but Stripe is still in **test mode** (no real payments taken
yet) and there are zero real paying customers. Landing page, signup
wizard, and Stripe→provisioning pipeline have all been tested live and
confirmed working with real (test-mode) transactions.

## Target audience

Retail shop owners running 2+ physical branches, primary market **Jordan
/ MENA region** (matches the infra domain, Arabic-language support already
built into the underlying POS product, and JOD default currency). Not yet
targeting the broader English-speaking global market.

## Brand

- **Name**: Tillora
- **Palette**: cream `#F2F5EF`, deep forest green `#214944`, chartreuse
  `#9CFF1E` — deliberately matched to the owner's other company (a
  performance-marketing/lead-gen business, thegoodleads.net) for visual
  consistency across their portfolio.
- **Logo**: real Stitch-generated mark (till-drawer + branching-network
  icon), light/dark wordmark variants + app icon, all in
  `public/images/logo.svg`, `logo-dark.svg`, `favicon.svg`.
- **Fonts**: Plus Jakarta Sans (headlines), DM Sans (body), via Google
  Fonts. Icons: Material Symbols Outlined.

## Real product capabilities (verified — don't claim beyond these)

- Multi-branch/multi-store management from one admin panel
- Real-time inventory across branches
- Offline-capable checkout (queues locally, syncs when back online)
- Receipt, barcode, and shelf-label printing
- Full accounting: chart of accounts, journal entries, financial reports
- Each shop gets a white-label subdomain, automatically wired
- Customer can later add their own separate domain (staff-assisted, not
  self-service yet)

**Not real / don't claim**: no free trial (Stripe checkout charges
immediately), no confirmed Shopify/WooCommerce/Lightspeed integration, no
named hardware certifications (Star Micronics/Zebra/etc — works with
generic USB/Bluetooth thermal printers and barcode scanners instead), no
SOC2 or uptime SLA claims, no 2FA (not implemented).

## Competitors

**Foodics** is the main named competitor for the MENA market — a larger,
enterprise-focused, sales-gated POS platform. Tillora's differentiation:
self-serve signup, transparent published pricing, instant provisioning.
A comparison page already exists (see links below) — keep any future
competitor claims generic/qualified (`"typical enterprise POS"` framing)
rather than asserting specific unverified facts about a competitor's
current pricing or features.

## Live links

- **Marketing site / homepage**: https://tillora.sphereofthesun.com
- **Signup wizard** (5-step: plan → shop details/logo/branches →
  subdomain → admin account → review): https://tillora.sphereofthesun.com/pricing
- **Foodics comparison/SEO page**: https://tillora.sphereofthesun.com/foodics-alternative
- **Staff (SMTGROUP) dashboard login**: https://tillora.sphereofthesun.com/login
- **Fallback Railway URL** (same app, if the custom domain ever has
  issues): https://license-server-production-28a5.up.railway.app
- **`pos.sphereofthesun.com`** — an earlier domain attempt that hit an
  unresolved Railway conflict; not in use, `tillora.sphereofthesun.com`
  is the real one.

## How it works (for anyone writing about the product accurately)

1. Visitor lands on the marketing site, picks a plan.
2. 5-step signup wizard: plan → shop name/logo/branch count → subdomain →
   admin account → review.
3. Submitting sends a **confirmation email first** (not straight to
   payment) — clicking that link is what starts Stripe Checkout. Stops
   typo'd emails from wasting a payment session.
4. On successful payment, a webhook mints a license and dispatches a
   background job that: creates a dedicated database, deploys the actual
   POS app from its own GitHub repo, wires in the license/branding, and
   registers the customer's white-label subdomain (Railway + Cloudflare
   DNS, fully automated).
5. Customer gets an email once their instance is ready, logs in with the
   admin account they created in step 2.

## Related folders / repos

- **This repo** (`SmtLicenseServer`) — the marketing site, signup wizard,
  billing (Stripe/Cashier), and the SMTGROUP staff dashboard (customer
  list, license management, plan CRUD). This is "the SaaS layer."
- **`D:\newDesktop\My\SaasPOS`** (GitHub: `AhmadAlawi/saaspos`) — the
  actual POS application product that gets deployed per-customer. This is
  what a Tillora customer actually uses day to day (cashier screen,
  inventory, accounting, admin panel). Not customer-facing marketing
  material itself, but the source of every real feature claim above —
  check here before writing any new capability claim.

## Owner's other business (for brand consistency reference only)

`thegoodleads.net` — "The Good Leads, LLC," a performance-marketing/
lead-generation company (home improvement, insurance, debt settlement
verticals). Tillora's color palette was deliberately matched to this
site's visual identity. Not otherwise related to Tillora's product or
audience.
