# Product Marketing Context

**Document version:** v3
**Last updated:** 2026-09-12

## Product Overview
**One-liner:** Tillora is a self-serve, multi-branch retail POS SaaS — sign up online, get your own fully isolated running POS instance automatically, no sales call.

**What it does:** A shop owner picks a plan, runs a 5-step signup wizard, and pays via Stripe Checkout. On payment confirmation, a webhook mints a license and a background job provisions a dedicated database + deployed POS app on a white-label subdomain — fully automated, no manual onboarding. The product itself (once provisioned) is a full retail POS: multi-branch inventory, offline-capable checkout, receipt/barcode/shelf-label printing, and full accounting (chart of accounts, journal entries, financial reports).

**Product category:** Retail point-of-sale (POS) software / multi-branch retail management SaaS.

**Product type:** SaaS, single-tenant-per-customer (each customer gets a dedicated DB + container, not a shared multi-tenant app).

**Business model:** Subscription (Stripe Checkout, recurring, **live mode as of 2026-09-12** — real charges, no longer test mode). 3 published tiers — Starter, Pro, Business — differentiated by seat limit and feature gates (see Pricing below). No free trial: checkout charges immediately. Currency: USD (`CASHIER_CURRENCY=usd`), though target market is Jordan/MENA (JOD used inside the provisioned POS product itself).

**Pricing (live, real numbers — safe to quote):**
| Plan | Price | Seat limit | multi_store | advanced_reporting |
|------|-------|-----------|-------------|---------------------|
| Starter | $39/mo | 3 | No | No |
| Pro | $89/mo | 10 | Yes | Yes |
| Business | $179/mo | Unlimited | Yes | Yes |

Priced to comfortably clear the ~$17-20/mo Railway infra cost per customer instance (each customer = a dedicated app container + MySQL container) while still undercutting sales-gated enterprise competitors. Branch count is captured as a sizing input during signup (1-500) but is **not** an enforced per-plan limit today.

## Target Audience
**Target companies:** Retail shops running 2+ physical branches, Jordan / MENA region primarily (matches Arabic-language support in the underlying POS product and JOD as the product's working currency).

**Decision-makers:** Owner-operators of small/mid retail chains (the person who'd personally run a self-serve signup wizard — not an enterprise buying committee).

**Primary use case:** Running day-to-day retail operations (checkout, inventory, accounting) across multiple branches from one admin panel, without needing a sales call or IT setup to get started.

**Jobs to be done:**
- Get a working, branded POS for all my branches running today, without waiting on a vendor sales cycle.
- Keep inventory and accounting in sync and accurate across branches in real time.
- Keep selling even when internet drops (offline-capable checkout).

**Use cases:**
- Multi-branch retail chain onboarding itself same-day via self-serve checkout.
- Shop wanting a white-label, branded storefront/admin (auto-wired subdomain) without hiring a developer.
- Owner comparing self-serve pricing against MENA enterprise POS vendors (e.g. Foodics) that require a sales call.

## Personas
Single-buyer B2B (owner-operator is user + champion + decision-maker + financial buyer in one, at this SMB size) — full persona table not yet needed at this stage. Revisit once mid-market/enterprise segment is targeted.

## Problems & Pain Points
**Core problem:** Retail POS in MENA is dominated by sales-gated, enterprise-first vendors — you can't just see a price and start using the product.

**Why alternatives fall short:**
- Sales-call-gated onboarding (e.g. Foodics) — slow, opaque pricing, enterprise sales friction for what is often a small multi-branch operation.
- Generic/global POS tools that lack Arabic-language and JOD-currency fit for the local market.

**What it costs them:** Time lost to sales cycles and demos before they can even evaluate the product; uncertainty about real pricing until a sales call.

**Emotional tension:** Not yet validated with real customers (pre-launch, zero paying customers) — avoid asserting unverified customer emotional language until interviews happen.

## Competitive Landscape
**Direct:** Foodics — the main named MENA competitor. Larger, enterprise-focused, sales-gated. Tillora differentiates on self-serve signup, transparent published pricing, and instant automated provisioning. A comparison/SEO page exists (`/foodics-alternative`) targeting "foodics alternative" search intent.

**Direct #2:** V-TECH (vtech-sys.com) — a real, established ERP/POS vendor across Jordan, Saudi Arabia, Kuwait and the UAE, covering restaurant + retail with a broad traditional ERP feature set. No public pricing found (sales-gated, same framing as Foodics). A comparison/SEO page exists (`/vtech-alternative`) targeting "vtech alternative" search intent.

**Secondary/Indirect:** Not yet researched/validated — no other named competitors confirmed by the founder yet.

**Caution:** Any competitor claim in copy should stay generic/qualified ("typical enterprise POS" framing) rather than asserting specific unverified facts about a competitor's current pricing or features.

## Differentiation
**Key differentiators:**
- Fully automated self-serve signup → live, provisioned, branded instance, with no manual setup or sales call.
- Each customer gets a genuinely isolated deployment (own database, own container) rather than a shared multi-tenant app — a real infrastructure difference, not just a marketing claim.
- Transparent, published pricing (once live prices are set) vs. sales-gated competitors.
- Confirmation-email-before-payment flow protects customers from wasting a payment session on a typo'd email.

**How we do it differently:** Stripe Checkout + a webhook-driven provisioning pipeline (dedicated DB creation, POS app deployment from its own repo, license/branding wiring, subdomain registration via Railway + Cloudflare DNS) — all automatic, same session as payment.

**Why that's better:** Speed to value (minutes, not a sales cycle) and infrastructure isolation (no noisy-neighbor risk from a shared multi-tenant system).

**Why customers choose us:** Self-serve speed + transparent pricing + genuine per-customer isolation, for a MENA retail audience currently underserved by enterprise-first vendors.

## Objections
| Objection | Response |
|-----------|----------|
| No free trial — is that risky? | Checkout charges immediately today; there is no trial. This is a real gap to flag to the founder before broad marketing push — don't imply a trial exists in copy. |
| Does it integrate with Shopify/WooCommerce/Lightspeed? | Not confirmed/implemented — do not claim these integrations. |
| Does it support named hardware (Star Micronics, Zebra, etc.)? | Not certified — works with generic USB/Bluetooth thermal printers and barcode scanners; say that, not brand names. |
| Is it secure / compliant (SOC2, uptime SLA, 2FA)? | None of these exist today. Do not make compliance or SLA claims. |

**Anti-persona:** Enterprise retail chains needing SOC2/compliance guarantees, named hardware certification, or third-party e-commerce platform integrations (Shopify/WooCommerce/Lightspeed) — not a fit until those exist.

## Switching Dynamics
**Push:** Frustration with slow, sales-gated onboarding at enterprise POS vendors (Foodics-style).

**Pull:** Instant self-serve signup, transparent pricing, same-session provisioning.

**Habit:** Existing relationship/contract with an incumbent enterprise POS vendor; comfort with an existing (even if slower) setup.

**Anxiety:** No free trial to de-risk the decision; lack of SOC2/uptime SLA/2FA may worry security-conscious buyers; no confirmed integrations with e-commerce platforms they may already use.

*(Not yet validated with real customer interviews — pre-launch, zero paying customers. Update this section once real switching conversations happen.)*

## Customer Language
Not yet collected — zero real customers so far (Stripe still in test mode). **Action item:** capture verbatim customer language once first real signups/conversations happen; this section should not be filled with invented phrasing in the meantime.

**Glossary:**
| Term | Meaning |
|------|---------|
| Instance | A customer's dedicated, isolated deployment of the POS app (own DB + container) |
| License | Record minted on successful Stripe payment that gates/enables an instance |
| Pending signup | A wizard submission awaiting email confirmation, before Stripe Checkout |
| Provisioning | The automated post-payment pipeline: DB creation, app deployment, subdomain/DNS wiring |

## Brand Voice
**Tone:** Not yet formally defined by the founder. Landing page copy currently reads as clear/direct, benefit-led SaaS marketing (typical modern POS-SaaS tone) — confirm intentional tone with founder.

**Style:** Direct, benefit-focused; avoids unverified/aspirational claims in this doc's guidance (real product capabilities are explicitly separated from not-yet-real ones — keep that discipline in all copy).

**Personality:** Not yet defined — recommend founder pick 3-5 adjectives explicitly in a future pass.

**Brand identity assets (confirmed):**
- Name: Tillora
- Palette: cream `#F2F5EF`, deep forest green `#214944`, chartreuse `#9CFF1E` — deliberately matched to the owner's other company (thegoodleads.net, a performance-marketing/lead-gen business) for visual consistency across their portfolio.
- Logo: Stitch-generated mark (till-drawer + branching-network icon), light/dark wordmark variants + app icon — `public/images/logo.svg`, `logo-dark.svg`, `favicon.svg`.
- Fonts: Plus Jakarta Sans (headlines), DM Sans (body), via Google Fonts. Icons: Material Symbols Outlined.

## Proof Points
**Metrics:** None yet — pre-launch, zero real paying customers. Infrastructure and the Stripe→provisioning pipeline have been tested end-to-end and confirmed working with real (test-mode) transactions, but this is an internal engineering fact, not a customer-facing proof point.

**Customers:** None yet.

**Testimonials:** None yet.

**Value themes (to validate with real usage, not yet proven with customer evidence):**
| Theme | Proof (current status) |
|-------|-------|
| Self-serve speed | Verified end-to-end technically (signup → payment → live instance), not yet proven at scale with real customers |
| Genuine isolation (own DB/container per customer) | Verified in infrastructure, real architectural fact |
| Full-featured POS (multi-branch, offline checkout, accounting) | Verified feature set in the underlying POS product (`SaasPOS` repo) |

## Goals
**Business goal:** Live as of 2026-09-12 — real Stripe payments, real pricing, first end-to-end paid signup confirmed working (payment → license → provisioned instance → admin login), all in the same day several critical bugs were found and fixed (see below). Now moving from "technically live" to actually acquiring paying customers in the Jordan/MENA multi-branch retail segment.

**Key conversion action:** Complete the 5-step signup wizard (Plan → Shop Details → Address/Subdomain → Admin Account → Review) and confirm the emailed signup link, which triggers Stripe Checkout.

**Current metrics:** Effectively zero real customers still (the one completed signup was a $1 internal test, not a real customer) — but the funnel itself is now proven end-to-end and instrumented. GA4 (`G-9B9CK3J8HK`) is live on every public page; UTM first-touch capture feeds `/dashboard/signups`.

## Acquisition & Campaign Capabilities (updated 2026-09-12)
- **Analytics: live.** GA4 installed on all public pages (landing, both comparison pages, full signup flow), plus a `signup_completed` conversion event on the success page.
- **UTM capture: live.** First-touch UTM params captured on landing → stored on `pending_signups` → visible per-signup in `/dashboard/signups` with a converted/not-converted flag.
- **SEO/comparison pages:** `/foodics-alternative` and `/vtech-alternative`, targeting those two competitors' "alternative" search intent. No blog yet.
- **Still missing:** coupon/promo codes, referral/affiliate system. Neither exists yet — no `allow_promotion_codes` on Checkout, no referral tables.
- **Implication:** the previous blocker (no way to measure spend) is closed. Paid acquisition can now be run and measured. The remaining real blocker was never marketing — it was three production bugs that would have silently killed every real signup (a crash-looping tenant deploy, a broken subscription-webhook schema mismatch, and a scheduler with a stuck 24h lock); all three found and fixed 2026-09-12 during the first live test.

## Live Links (reference)
- Marketing site / homepage: https://tillora.sphereofthesun.com
- Signup wizard entry (pricing page): https://tillora.sphereofthesun.com/pricing
- Foodics comparison/SEO page: https://tillora.sphereofthesun.com/foodics-alternative
- Staff (SMTGROUP) dashboard login: https://tillora.sphereofthesun.com/login
- Fallback Railway URL: https://license-server-production-28a5.up.railway.app

## Related Repos
- **This repo** (`SmtLicenseServer`) — marketing site, signup wizard, billing (Stripe/Cashier), SMTGROUP staff dashboard (customer list, license management, plan CRUD). "The SaaS layer."
- **`D:\newDesktop\My\SaasPOS`** (GitHub: `AhmadAlawi/saaspos`) — the actual POS application deployed per-customer; source of truth for every real feature claim above. Check here before writing any new capability claim.
- `thegoodleads.net` — owner's other business (performance-marketing/lead-gen, home improvement/insurance/debt-settlement verticals) — brand-palette reference only, not otherwise related to Tillora's product or audience.

## Changelog
*Newest first. One line per revision: what changed and why.*
- v3 (2026-09-12) — Went live: real Stripe payments and real pricing ($39/$89/$179, replacing placeholder/unset prices); added V-TECH as a second named direct competitor with its own comparison page; marked analytics/UTM gap from v2 as closed (GA4 + UTM capture shipped); noted first real end-to-end signup test surfaced and fixed three production bugs (tenant deploy crash-loop, broken subscription webhook schema, stuck scheduler lock).
- v2 (2026-09-11) — Restructured into the standard product-marketing template; added verified pricing tiers/seat limits/feature gates, confirmed signup-flow and Stripe/webhook provisioning details, confirmed **no analytics/tracking and no UTM/coupon/referral capability exists** (key gap for any paid acquisition work), added objections/anti-persona/switching-dynamics/goals sections. Source: direct codebase investigation (Plan model, migrations, seeders, PricingController, StripeWebhookController, routes/web.php, landing views).
- v1 (date unknown) — Initial freeform context document (product overview, brand, verified capabilities, competitors, live links, how-it-works, related repos) captured before this doc adopted the standard template.
