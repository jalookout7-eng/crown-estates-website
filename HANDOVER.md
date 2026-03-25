# Crowns Estates Website — Session Handover

**Date:** 2026-03-25
**Repo:** https://github.com/jalookout7-eng/crown-estates-website
**Domain:** www.crownsestates.co.uk
**Local path (original machine):** `C:\Users\John\crown-estates-website`

---

## What This Project Is

A WordPress website for **Crowns Estates** — a UK-registered real estate agency run by British expats (20 years in Saudi Arabia) who help UK/global investors buy property in Saudi Arabia.

**Client values:** Trust, Local Knowledge, Minimalism, Easy Navigation, Educational Content.

**Tagline:** "Connecting Investors with Quality Property Opportunities."

---

## What Was Done This Session

### 1. Design Spec Written
- Full website design spec based on client questionnaire answers
- Reviewed by Opus — 12 issues found and all fixed
- **File:** `docs/superpowers/specs/2026-03-24-crowns-estates-website-design.md`

### 2. Implementation Plan Written
- 18-task implementation plan covering full WordPress build
- Reviewed by Opus — 12 issues found and all fixed (DB table hook, caching conflict, missing templates, etc.)
- **File:** `docs/superpowers/plans/2026-03-24-crowns-estates-implementation.md`

### 3. Front-End Design Built (Current State)
All page templates created with **static placeholder content** for client presentation:

| File | Page |
|------|------|
| `front-page.php` | Homepage — hero, trust bar, featured properties, testimonials, about snippet, CTA |
| `page-projects.php` | Projects — city filter tabs, 6 property cards |
| `page-how-it-works.php` | How It Works — 5-step process, investment calculator, Golden Visa, FAQ |
| `page-about.php` | About Us — story, core values, stats |
| `page-contact.php` | Contact — enquiry form, contact info |
| `page-rentals.php` | Rentals — Coming Soon placeholder |
| `page.php` | Default page template (for legal pages) |
| `404.php` | Branded 404 page |
| `header.php` | Site header with nav + currency toggle |
| `footer.php` | Footer with links, disclaimer, WhatsApp button |
| `functions.php` | Theme setup, menus, widget areas, asset enqueue |
| `style.css` | Full design system — CSS variables, all component styles, responsive |

### 4. Static HTML Preview
- **File:** `preview/index.html`
- All pages combined into one scrollable HTML file with working FAQ accordion, filter tabs, and smooth scrolling
- Hosted via **GitHub Pages** (repo is public)
- **Preview URL:** `https://jalookout7-eng.github.io/crown-estates-website/preview/`
- Anyone with the link can view the design — no WordPress needed

---

## What Has NOT Been Done Yet

The backend is pending client approval of the design. Outstanding tasks from the implementation plan:

- [ ] Custom Post Types (`ce_property`, `ce_testimonial`)
- [ ] Custom Taxonomy (`ce_city`)
- [ ] ACF field groups (property fields, testimonial fields, site options)
- [ ] ACF options pages (exchange rates, calculator rates, WhatsApp number)
- [ ] Multi-currency helper functions + REST endpoint
- [ ] Template parts (reusable components — currently inline in page templates)
- [ ] Enquiry handler (form → DB storage + email + auto-responder)
- [ ] JavaScript modules (calculator logic, currency toggle, modal, city filter AJAX, FAQ)
- [ ] Blog templates (`archive.php`, `single.php`, `sidebar.php`)
- [ ] Sample blog post content (5 articles)
- [ ] Schema.org JSON-LD markup
- [ ] Screenshot.png for theme
- [ ] Server deployment

---

## Design System

| Token | Value |
|-------|-------|
| Background | `#FFFFFF` (white) |
| Text | `#0A0A0A` (near-black) |
| Gold accent | `#C4973A` |
| Heading font | Playfair Display (serif) |
| Body font | Inter (sans-serif) |
| Spacing unit | 8px multiples |
| Max width | 1200px |

All values are CSS custom properties in `style.css` — one change updates the entire site.

---

## Tech Stack

- **CMS:** WordPress 6.x
- **Theme:** Custom (built on Underscores `_s` starter)
- **Fields:** Advanced Custom Fields (ACF)
- **JS:** Vanilla JavaScript (no jQuery dependency)
- **Hosting:** TBD — VPS or managed WordPress (domain registered: crownsestates.co.uk)

---

## Key Decisions Made

1. **Custom theme over page builder** — client only needs to update listings, not redesign pages
2. **Currency conversion is client-side only** — server renders native currency with `data-*` attributes, JS handles conversion. This is cache-compatible with WP Super Cache.
3. **Testimonials are static grid, not carousel** — simpler, cache-friendly
4. **DB table for enquiries uses `after_switch_theme` hook** — not `register_activation_hook` (that's plugins only)
5. **Blog section included in Phase 1** — every competitor has one, critical for SEO
6. **Regulatory disclaimers mandatory** — UK company collecting investor data, GDPR compliance required
7. **`ce_city` taxonomy attached to both properties AND testimonials** — enables "related testimonials" on property pages

---

## GitHub Setup

- **Username:** jalookout7-eng
- **Email:** jalookout7@gmail.com
- **Repo visibility:** Public (for GitHub Pages)
- **GitHub Pages:** Enabled, deploying from `main` branch, root folder

---

## To Resume on Another Machine

```bash
git clone https://github.com/jalookout7-eng/crown-estates-website.git
cd crown-estates-website
git config user.email "jalookout7@gmail.com"
git config user.name "jalookout7-eng"
```

Then read:
1. `docs/superpowers/specs/2026-03-24-crowns-estates-website-design.md` — the approved spec
2. `docs/superpowers/plans/2026-03-24-crowns-estates-implementation.md` — the implementation plan
3. This file — for session context

The next step is: **wait for client feedback on the design preview**, then begin backend implementation starting from Task 4 of the plan (Tasks 1-3 are effectively done via the front-end work).

---

## Client Brief (Original Questionnaire Summary)

- **Business:** Crowns Estates — British expats, 20 years in Saudi Arabia
- **USP:** Local expertise, honest advice, end-to-end investor support
- **Target:** UK/global investors entering Saudi property market
- **Pain point:** Investors lack confidence in Saudi systems/developers/processes
- **Design vibe:** Easy / Informative / Minimalist
- **Competitors referenced:** saudipropertyinvestment.com, makan360.co.uk, labreezere.com
- **Features requested:** WhatsApp button, Register Interest form, city-based filtering
- **Phase 2:** Rentals, visa services, AI chat agent
- **Contact email:** info@crownsestates.co.uk
- **Domain:** www.crownsestates.co.uk (registered)

---

## Reference Screenshots

The user saved screenshots of a sample backend (griyakita.digision.id/admin) and property portals at `C:\Users\John\Pictures\Screenshots\` (2026-03-24 dated files). These were used as general reference for the backend admin panel concept — not directly implemented yet.
