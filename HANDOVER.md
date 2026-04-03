# Crowns Estates Website — Session Handover

**Last Updated:** 2026-04-02
**Sessions:** 3
**Repo:** https://github.com/jalookout7-eng/crown-estates-website
**Domain:** www.crownsestates.co.uk
**Local path (current machine):** `/Users/farhanraiss/crown-estates-website`

---

## What This Project Is

A WordPress website for **Crowns Estates** — a UK-registered real estate agency run by British expats (20 years in Saudi Arabia) who help UK/global investors buy property in Saudi Arabia.

**Client values:** Trust, Local Knowledge, Minimalism, Easy Navigation, Educational Content.

**Tagline:** "Connecting Investors with Quality Property Opportunities."

### Delivery Model

- **R2 Design** — client-facing agency. Handles client communication, weekly Friday updates, revision rounds, content collection.
- **jalookout7-eng** — builds everything (Figma designs, WordPress theme, backend, 3D immersive front-end, AI chatbot) and hands over all deliverables to R2.
- **Client receives:** Figma source files (upon final payment), full WordPress codebase, admin access, 30-day post-launch warranty.
- **Timeline:** 8 weeks. Content from client by end of Week 2 (branding/data) and Week 4 (copy).

---

## Current State (After Session 3)

### What Exists in the Repo

**Front-end design preview (Sessions 1-2):**
All page templates built with static placeholder content:

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

**Static HTML preview:** `preview/index.html`
- **Preview URL:** `https://jalookout7-eng.github.io/crown-estates-website/preview/`

**Specs & Plans:**

| File | Status |
|------|--------|
| `docs/superpowers/specs/2026-03-24-crowns-estates-website-design.md` | v1 — superseded |
| `docs/superpowers/specs/2026-04-02-crowns-estates-website-design-v2.md` | **v2 — current spec** |
| `docs/superpowers/plans/2026-03-24-crowns-estates-implementation.md` | v1 — needs rewrite to match v2 spec |

---

## What Changed in Session 3 (v1 → v2)

### Trigger: R2 Design Scope Document

R2 Design issued a "Scope Clarification & Project Agreement" to the client defining Phase 1 deliverables. The v2 spec aligns our build with R2's commitments while adding high-value bonus features.

### New Features Added (from R2 scope)

| Feature | Details |
|---------|---------|
| Developer reliability badges | Custom badge system (Verified / Track Record / Premium Partner) on property cards |
| Gated brochure downloads | Per-property toggle — email capture before PDF download (lead gen) |
| GA4 + Google Search Console | Full setup with GTM, custom event tracking |
| WhatsApp click tracking | GA4 custom events on widget clicks + form submissions |
| Figma design system | Component-based, client-extensible — maps 1:1 to WordPress template parts |
| Image compression + ALT tags | Explicit Core Web Vitals commitment |
| XML sitemap generation | Via Yoast, submitted to Search Console |

### New Bonus Features Added (beyond R2 scope)

| Feature | Details |
|---------|---------|
| 3D immersive experience | Full Three.js + WebGL + GSAP ScrollTrigger across all pages (inspired by districtio.com) |
| AI Assistant chatbot | Claude API (Haiku 4.5) — RAG over site content + documents + enquiry collection |
| Custom admin dashboard | Branded backend with stat cards, clean sidebar (griyakita reference) |

### Features Dropped (from v1)

| Feature | Reason |
|---------|--------|
| Newsletter subscribers system | Not in R2 scope, Phase 2 |
| Tour requests management page | Enquiry form covers this |
| Branded login page | Nice-to-have, not committed |

---

## What Has NOT Been Done Yet

### Must Build (R2 committed scope)

- [ ] Figma design system (component-based, Home + Projects first for client approval)
- [ ] Custom Post Types (`ce_property`, `ce_testimonial`)
- [ ] Custom Taxonomy (`ce_city`)
- [ ] ACF field groups (property fields, testimonial fields, site options)
- [ ] Developer reliability badges (custom badge system)
- [ ] Gated brochure downloads (email capture toggle per property)
- [ ] Property filters (city, developer, completion status)
- [ ] Template parts (reusable components — currently inline in page templates)
- [ ] Enquiry handler (form → DB storage + email + auto-responder)
- [ ] JavaScript modules (calculator, currency toggle, modal, city filter, FAQ)
- [ ] Blog templates (`archive.php`, `single.php`, `sidebar.php`)
- [ ] Blog framework + 5 initial articles (client provides content, we format)
- [ ] GA4 + GTM setup with custom event tracking
- [ ] Google Search Console setup + XML sitemap submission
- [ ] WhatsApp click tracking (GA4 custom events)
- [ ] Schema.org JSON-LD markup
- [ ] Image compression + ALT tags
- [ ] Core Web Vitals optimisation
- [ ] Screenshot.png for theme
- [ ] Server deployment

### Must Build (Bonus features)

- [ ] 3D immersive front-end (Three.js + GSAP ScrollTrigger + Lenis)
  - [ ] Homepage 3D skyline/property hero scene
  - [ ] Projects page 3D city map with pin markers
  - [ ] Single property interactive 3D model viewer
  - [ ] How It Works scroll-driven 3D journey path
  - [ ] About Us parallax depth layers
  - [ ] Gold particle system
  - [ ] WebGL fallback (static images + CSS parallax)
  - [ ] 3D model pipeline (Spline/Blender → GLTF/GLB → compressed)
  - [ ] Mobile-optimised simplified scenes
  - [ ] Branded preloader animation
- [ ] AI Assistant chatbot (Claude API)
  - [ ] Chat widget (front-end, alongside WhatsApp button)
  - [ ] REST endpoint (`/wp-json/ce/v1/chat`)
  - [ ] RAG context: property data, How It Works, FAQ, About, brochures
  - [ ] Conversational enquiry capture (name, email, phone → DB)
  - [ ] Rate limiting + admin toggle
- [ ] Custom admin dashboard
  - [ ] Stat cards (properties, enquiries, users, property value)
  - [ ] Sparkline charts (Chart.js)
  - [ ] Reorganised admin sidebar
  - [ ] Admin colour scheme (dark sidebar, gold accents)
- [ ] Investment calculator (already designed in v1 front-end)
- [ ] Multi-currency toggle (GBP/SAR/USD, client-side, cache-safe)
- [ ] Rentals "Coming Soon" page

### Needs Rewrite

- [ ] Implementation plan — v1 plan (`2026-03-24-crowns-estates-implementation.md`) needs full rewrite to match v2 spec (new features, 3D layer, AI chatbot, dropped features, Figma workflow)

---

## Design System

| Token | Value |
|-------|-------|
| Background | `#FFFFFF` (white) |
| Text | `#0A0A0A` (near-black) |
| Gold accent | `#C4973A` |
| Gold light | `#D4AF5C` |
| Gold dark | `#A37E2C` |
| Grey light | `#F5F5F5` |
| Grey mid | `#E0E0E0` |
| Grey dark | `#666666` |
| Heading font | Playfair Display (serif) |
| Body font | Inter (sans-serif) |
| Spacing unit | 8px multiples |
| Max width | 1200px |

All values are CSS custom properties in `style.css` AND Figma variables — one change updates the entire site.

---

## Tech Stack

- **CMS:** WordPress 6.x
- **Theme:** Custom (built on Underscores `_s` starter)
- **Fields:** Advanced Custom Fields (ACF)
- **JS:** Vanilla JavaScript (no jQuery dependency)
- **3D:** Three.js + WebGL
- **Animation:** GSAP + ScrollTrigger
- **Smooth Scroll:** Lenis
- **3D Models:** GLTF/GLB (Spline/Blender, Draco compressed)
- **AI Chatbot:** Claude API (Haiku 4.5) via custom REST endpoint
- **Design:** Figma (component-based design system)
- **Analytics:** GA4 via GTM
- **SEO:** Yoast SEO
- **Caching:** WP Super Cache
- **Backups:** UpdraftPlus
- **GDPR:** Complianz or CookieYes
- **Hosting:** TBD — recommended: Cloudways + DigitalOcean (~£10-15/mo)

---

## Key Decisions Made

1. **Custom theme over page builder** — client only needs to update listings, not redesign pages
2. **Figma as living design system** — client can design new sections, dev team builds them
3. **Full immersive 3D via Three.js** — 3D is the primary visual language, not decorative (inspired by districtio.com)
4. **GSAP ScrollTrigger for scroll-driven animation** — camera movements, section reveals, transitions tied to scroll
5. **3D graceful fallback** — static images + CSS parallax if WebGL unsupported or low-power device
6. **3D assets lazy-loaded** — page content readable before 3D finishes loading, no LCP penalty
7. **Currency conversion is client-side only** — cache-compatible with WP Super Cache
8. **Testimonials are static grid, not carousel** — simpler, cache-friendly
9. **DB table for enquiries uses `after_switch_theme` hook** — not `register_activation_hook` (plugins only)
10. **Blog in Phase 1** — critical for SEO
11. **`ce_city` taxonomy shared** between properties AND testimonials
12. **Claude Haiku 4.5 for chatbot** — fast, cost-effective; built as demo/preview (may not ship)
13. **Gated brochures are per-property toggle** — not global
14. **Developer badges are admin-assigned** — no automated verification
15. **GA4 via GTM** — flexible event tracking without code changes
16. **R2 handles client comms** — dev team focuses on building

---

## 3D Immersive Approach

Inspired by [districtio.com](https://districtio.com/). Full immersive 3D across all pages.

| Page | 3D Treatment |
|------|-------------|
| Home | 3D Saudi skyline/property model in hero. Camera orbits on scroll. Gold particles. Sections emerge from depth. |
| Projects | 3D city map with pin markers. Click pin → zoom into city. Cards animate with depth/parallax. |
| Single Property | Interactive 3D building model (rotate/zoom). Scroll-driven walkthrough. Falls back to gallery if no model. |
| How It Works | 5-step journey as scroll-driven 3D path. Camera follows golden line through floating step cards. |
| About Us | Parallax depth layers. Stats animate with 3D counter reveals. Gold particle atmosphere. |
| Contact | Minimal 3D — floating form card with depth, 3D globe/map pin. |

**3D model pipeline:** Client renders → Spline/Blender → GLTF/GLB → Draco compressed → lazy-loaded in browser.

**Performance:** Lazy loading, Draco compression, simplified mobile scenes, branded preloader, content readable before 3D loads.

---

## GitHub Setup

- **Username:** jalookout7-eng
- **Email:** jalookout7@gmail.com
- **Repo visibility:** Public (for GitHub Pages)
- **GitHub Pages:** Enabled, deploying from `main` branch, root folder

---

## Figma MCP Integration

Figma is connected to Claude Code via the official MCP server for bidirectional design/code workflow:

```bash
claude mcp add --transport http --scope user figma https://mcp.figma.com/mcp
```

Authenticate via `/mcp` in Claude Code session. Enables:
- Read/write Figma files directly from Claude Code
- Push code to Figma canvas (Code to Canvas)
- Generate code from Figma designs
- Maintain component-based design system in sync with WordPress template parts

**Not yet set up** — scheduled for next session.

---

## To Resume on Another Machine

```bash
git clone https://github.com/jalookout7-eng/crown-estates-website.git
cd crown-estates-website
git config user.email "jalookout7@gmail.com"
git config user.name "jalookout7-eng"
```

Then read:
1. `docs/superpowers/specs/2026-04-02-crowns-estates-website-design-v2.md` — the **current** approved spec
2. This file — for session context and what's pending
3. `docs/superpowers/plans/` — implementation plan (needs rewrite to match v2 spec)

**Next step:** Write new implementation plan based on v2 spec, then begin building.

---

## Client Brief (Original Questionnaire Summary)

- **Business:** Crowns Estates — British expats, 20 years in Saudi Arabia
- **USP:** Local expertise, honest advice, end-to-end investor support
- **Target:** UK/global investors entering Saudi property market
- **Pain point:** Investors lack confidence in Saudi systems/developers/processes
- **Design vibe:** Easy / Informative / Minimalist
- **Competitors referenced:** saudipropertyinvestment.com, makan360.co.uk, labreezere.com
- **3D reference:** districtio.com (full immersive 3D experience)
- **Features requested:** WhatsApp button, Register Interest form, city-based filtering
- **Phase 2:** Rentals, visa services
- **Contact email:** info@crownsestates.co.uk
- **Domain:** www.crownsestates.co.uk (registered)

---

## Reference Material

### Admin Dashboard Reference
7 screenshots of griyakita.digision.id/admin (Laravel + Filament PHP admin panel) saved in project root (`Screenshot 2026-03-25 14465*.png`). These are the reference design for the custom WordPress admin dashboard.

### R2 Design Scope Document
"Scope Clarification & Project Agreement" received 2026-04-02. Defines R2's commitments to the client for Phase 1. The v2 spec incorporates all R2 commitments plus bonus features.

---

## Session Log

### Session 1 — 2026-03-25
- Created project from client questionnaire answers
- Wrote design spec v1, reviewed by Opus (12 fixes applied)
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
- Added Task 19 to implementation plan: Custom Admin Dashboard & Branded Backend
- Updated implementation plan: now 19 tasks, 86 steps (was 18/79)
- Discussed VPS/hosting options (recommended: Cloudways with DigitalOcean, ~£10-15/mo)
- **Status:** Still awaiting client confirmation before proceeding with backend

### Session 3 — 2026-04-02
- Received R2 Design "Scope Clarification & Project Agreement" from client
- Clarified delivery model: R2 Design is client-facing agency, we build everything and hand over
- Chose Approach B: R2 scope as baseline + keep high-value bonus features
- Wrote v2 design spec (`2026-04-02-crowns-estates-website-design-v2.md`) incorporating:
  - All R2 committed features (developer badges, gated brochures, GA4, WhatsApp tracking, Figma)
  - Bonus: investment calculator, multi-currency, testimonials, custom admin dashboard, AI chatbot (Claude API)
  - Full immersive 3D experience across all pages (Three.js + GSAP + Lenis, inspired by districtio.com)
- Dropped: newsletter subscribers, tour requests management, branded login page
- Researched Figma MCP server — official integration available (setup pending)
- Cloned repo to new machine (`/Users/farhanraiss/crown-estates-website`)
- **Status:** v2 spec approved. Next: write v2 implementation plan, set up Figma MCP, begin building.
