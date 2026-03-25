# Crowns Estates Website — Session Handover

**Last Updated:** 2026-03-25
**Sessions:** 2
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
- [ ] Custom admin dashboard (branded, stat cards, clean sidebar — matching griyakita reference)
- [ ] Newsletter subscribers admin page + DB table
- [ ] Tour requests management page
- [ ] Branded login page (Crowns Estates logo, gold/black)
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
- **Hosting:** TBD — recommended: Cloudways + DigitalOcean (~£10-15/mo). Alternatives: SiteGround (easiest), bare DigitalOcean/Vultr (cheapest ~£4/mo). Domain registered: crownsestates.co.uk

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

The next step is: **wait for client to confirm the job**, then begin backend implementation starting from Task 4 of the plan (Tasks 1-3 are effectively done via the front-end work). Task 19 (Custom Admin Dashboard) can be built alongside other backend tasks.

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

The user saved screenshots of a sample backend (griyakita.digision.id/admin) showing a Laravel + Filament PHP admin panel. 7 screenshots saved in project root (`Screenshot 2026-03-25 14465*.png`) showing:
1. **Dashboard** — stat cards (Total Properties, Active, Sold/Rented, Pending Review, Tour Requests, Total Users, Property Value) with sparkline charts + visitor stats
2. **Visitor Locations** — table with location, visits, unique visitors, last visit
3. **Posts** — stats cards (Total Posts, Published, Draft, Views, Featured) + searchable list
4. **Categories** — table with name, icon, status, post count, order, edit/delete
5. **Newsletter Subscribers** — list page with search
6. **Properties** — list with thumbnail, title, type, listing status, price, city, beds/baths
7. **Visitor Analytics** — total/today/unique visitors, weekly/monthly, countries, device type

These are the **reference design** for the custom WordPress admin dashboard (Task 19 in the implementation plan). WordPress admin will be customised to match this clean, branded experience.

---

## Session Log

### Session 1 — 2026-03-25
- Created project from client questionnaire answers
- Wrote design spec, reviewed by Opus (12 fixes applied)
- Added: investment calculator, multi-currency, blog section, testimonials/Google Reviews, regulatory disclaimers
- Wrote implementation plan (18 tasks, 79 steps), reviewed by Opus (12 fixes applied)
- Built full front-end design preview (all 7 pages with static content)
- Created standalone HTML preview at `preview/index.html`
- Pushed to GitHub, enabled GitHub Pages (repo made public)
- Fixed mobile header bug (nav links showing instead of hamburger)
- **Preview URL:** `https://jalookout7-eng.github.io/crown-estates-website/preview/`
- **Status:** Awaiting client feedback on design before starting backend

### Session 2 — 2026-03-25
- Reviewed 7 griyakita admin dashboard screenshots (Laravel + Filament PHP)
- Confirmed WordPress.org (self-hosted) is the correct foundation
- Mapped all griyakita features to WordPress equivalents — all achievable
- Added **Task 19** to implementation plan: Custom Admin Dashboard & Branded Backend
  - Custom dashboard page with stat cards + sparkline charts (Chart.js)
  - Reorganised admin sidebar (Dashboard, Properties, Tour Requests, Blog, Users, Analytics, Settings)
  - Branded login page (Crowns Estates logo, gold/black colours)
  - Newsletter Subscribers admin page + DB table
  - Tour Requests management page
  - Admin colour scheme customisation
- Updated implementation plan: now **19 tasks, 86 steps** (was 18/79)
- Discussed VPS/hosting options (recommended: Cloudways with DigitalOcean, ~£10-15/mo)
- Updated file structure in plan to include `admin-dashboard.php` and `admin-dashboard.js`
- **Status:** Still awaiting client confirmation of the job before proceeding with backend
