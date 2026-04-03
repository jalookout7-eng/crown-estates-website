# Crowns Estates Website — Design Spec v2
**Date:** 2026-04-02
**Status:** Approved — Ready for Implementation Planning
**Supersedes:** `2026-03-24-crowns-estates-website-design.md`
**Repo:** https://github.com/jalookout7-eng/crown-estates-website
**Domain:** www.crownsestates.co.uk
**Agency:** R2 Design (client-facing) — jalookout7-eng (production)

---

## 1. Project Overview

A WordPress website for **Crowns Estates**, a UK-registered real estate agency specialising in Saudi Arabian property investment opportunities. The site targets UK and global investors seeking entry into the Saudi property market.

**Tagline:** "Connecting Investors with Quality Property Opportunities."

**Core values to convey:** Trust, Local Knowledge, Clarity, Confidence.

### Delivery Model

- **R2 Design** is the client-facing agency. They handle client communication, weekly updates, revision rounds, and content collection.
- **jalookout7-eng** builds everything — Figma designs, WordPress theme, backend, AI chatbot — and hands over all deliverables to R2.
- **Client receives:** Figma source files (upon final payment), full WordPress codebase, admin access, 30-day post-launch warranty.

### Timeline

8-week build. Content deadlines:
- **End of Week 2:** All core branding + initial property data from client
- **End of Week 4:** All final written copy from client
- **Weekly Friday updates** to client via R2 (email/WhatsApp/Slack)

---

## 2. Target Audience

- UK-based and global investors (not Saudi-local)
- First-time entrants to the Saudi property market
- Investors interested in the Saudi Premium Residency (Golden Visa) program
- High-net-worth individuals seeking off-plan and under-construction investment opportunities

**Key pain point:** Lack of confidence in Saudi property systems, developers, and processes. The site must simplify, guide, and reassure.

---

## 3. Design System

### Figma Design System

All designs are produced as a **component-based Figma design system** before development. This is a living file — the client can use it to design new sections/pages by duplicating and rearranging components, then hand back to the dev team to build.

**Figma workflow:**
1. Home + Projects pages designed first to lock visual style
2. Two (2) rounds of comprehensive revisions
3. Once style is locked, roll out to remaining pages
4. Figma source files delivered to client upon final payment

**Figma components map 1:1 to WordPress template parts:**

| Figma Component | WordPress Template Part |
|-----------------|------------------------|
| Hero Section | `template-parts/hero.php` |
| Property Card | `template-parts/property-card.php` |
| Testimonial Card | `template-parts/testimonial-card.php` |
| Trust Bar | `template-parts/trust-bar.php` |
| CTA Banner | `template-parts/cta-banner.php` |
| Register Interest Modal | `template-parts/modal-register-interest.php` |
| WhatsApp Button | `template-parts/whatsapp-button.php` |
| Developer Badge | `template-parts/developer-badge.php` |
| Brochure Gate | `template-parts/brochure-gate.php` |

### Design Tokens

All values are CSS custom properties — one change updates the entire site. Tokens are also mirrored as Figma variables for design/code sync.

| Token | Value | Notes |
|---|---|---|
| Background | `#FFFFFF` | White |
| Primary text | `#0A0A0A` | Near-black |
| Gold accent | `#C4973A` | Borders, CTAs, highlights |
| Gold light | `#D4AF5C` | Hover states |
| Gold dark | `#A37E2C` | Active states |
| Grey light | `#F5F5F5` | Section backgrounds |
| Grey mid | `#E0E0E0` | Borders, dividers |
| Grey dark | `#666666` | Secondary text |
| Heading font | Playfair Display | Serif — luxury feel |
| Body font | Inter | Clean sans-serif |
| Base spacing unit | 8px | Multiples of 8 throughout |
| Max width | 1200px | Content container |
| Border radius | 4px | Cards, buttons, inputs |
| Transition | 0.2s ease | Hover/focus states |

**Vibe:** Easy / Informative / Minimalist. No clutter. Generous whitespace. Gold used sparingly.

### 3D Immersive Design Language

The site uses a **full immersive 3D experience** across multiple pages, inspired by [districtio.com](https://districtio.com/). 3D elements are not decorative — they are the primary visual storytelling medium.

**Technology:**
- **Three.js + WebGL** — browser-based 3D rendering
- **GSAP + ScrollTrigger** — scroll-driven animations bound to camera/scene
- **Lenis** — smooth scrolling library for buttery scroll feel
- **GLTF/GLB models** — 3D building/property models (created in Spline or Blender)

**3D elements per page:**

| Page | 3D Treatment |
|------|-------------|
| **Home** | Hero: 3D Saudi skyline/luxury property model. Camera orbits on scroll, transitions from aerial to ground level. Gold particle effects. Scroll-driven reveal of sections (trust bar, properties, testimonials emerge from depth). |
| **Projects** | 3D city map with pin markers for property locations. Click a pin → zooms into that city cluster. Property cards animate in with depth/parallax as user scrolls the grid. |
| **Single Property** | Interactive 3D building model (if available) — user can rotate/zoom. Scroll-driven walkthrough: exterior → floor plan → unit detail. Falls back to gallery carousel if no 3D model. |
| **How It Works** | 5-step journey as a scroll-driven 3D path. Camera follows a golden line through floating step cards in 3D space. Calculator card materialises with depth animation. |
| **About Us** | Parallax depth layers: team story text floats at different Z-depths. Stats bar animates with 3D counter reveals. Subtle gold particle atmosphere. |
| **Contact** | Minimal 3D — floating form card with subtle depth, 3D globe or map pin for location. Focus is on the form, not distraction. |

**3D Model Pipeline:**
1. Architectural renders/property images provided by client
2. 3D models created in **Spline** (rapid prototyping, exports GLTF) or **Blender** (complex models)
3. Optimised for web (compressed GLTF/GLB, LOD where needed)
4. Loaded asynchronously — page content is readable before 3D assets finish loading
5. Graceful fallback: if WebGL is unsupported or device is low-power, static images with CSS parallax replace 3D scenes

**Performance considerations:**
- 3D assets loaded lazily (only when section enters viewport)
- Model compression via Draco/meshopt
- Texture atlas where possible to reduce draw calls
- Mobile: simplified scenes (fewer particles, lower-poly models, reduced draw distance)
- Preloader with branded loading animation while 3D scene initialises
- Core Web Vitals: 3D canvas does NOT block LCP — text/images render first, 3D layers in after

---

## 4. Site Structure — Phase 1

### R2 Committed Scope

| Page | Slug | Purpose |
|---|---|---|
| Home | `/` | High-impact landing, featured projects, trust markers |
| Projects | `/projects` | All property listings, filterable by city/developer/status |
| Single Project | `/properties/{slug}` | Dynamic layout for individual developments |
| How It Works | `/how-it-works` | Step-by-step investment process, calculator, Golden Visa, FAQ |
| About Us | `/about` | Story, expat advantage, team, core values |
| Contact | `/contact` | Enquiry form, contact details, WhatsApp |
| Blog / Insights | `/blog` | Market guides, investment tips, area profiles |

### Bonus Features (beyond R2 scope)

| Page/Feature | Purpose |
|---|---|
| Rentals | `/rentals` — "Coming Soon" placeholder with email capture |
| Investment Calculator | Interactive cost estimator on How It Works |
| Multi-Currency Toggle | GBP/SAR/USD price switching (client-side, cache-safe) |
| Testimonials | Curated client reviews with Google Reviews links |
| Custom Admin Dashboard | Branded backend with stat cards, clean sidebar |

---

## 5. WordPress Architecture

### Theme
- **Base:** Custom theme built on Underscores (`_s`) starter
- **No page builder** — clean, fast, maintainable
- All design tokens in `style.css` as CSS custom properties
- Fully responsive (mobile-first)
- Optimised for Google's Core Web Vitals

### Custom Post Type: `ce_property`
Registered via the theme's `functions.php`. Admin sees "Properties" in the dashboard sidebar.

**Property Fields (ACF):**

| Field Label | Field Type | Notes |
|---|---|---|
| City | Taxonomy (`ce_city`) | Filterable |
| Developer | Text | e.g. "ROSHN", "Dar Global" |
| Developer Badge | Select | Reliability badge tier (see section 6) |
| Price From | Number | Starting price |
| Currency | Select | SAR / GBP / USD |
| Property Type | Select | Apartment, Villa, Commercial |
| Status | Select | Off-Plan, Under Construction, Ready |
| Completion Date | Date Picker | Estimated handover |
| Bedrooms | Number | |
| Size (Sq.M.) | Number | |
| Is Freehold | True/False | |
| Short Description | Textarea | Used on listing cards |
| Full Description | WYSIWYG | Used on single property page |
| Gallery | Gallery | Multiple images |
| Brochure PDF | File | Downloadable or gated behind email capture |
| Brochure Gated | True/False | If true, requires email before download |
| Featured | True/False | Appears on homepage |
| Map Embed | Text | Google Maps embed URL |

### Custom Post Type: `ce_testimonial`

| Field | Type | Notes |
|---|---|---|
| Client Name | Text | Can be anonymised |
| Location | Text | e.g. "London, UK" |
| Quote | Textarea | The testimonial text |
| Rating | Number (1-5) | Displayed as stars |
| Google Review Link | URL | Links to original Google review |
| City | Taxonomy (`ce_city`) | For "related testimonials" on property pages |
| Featured | True/False | Appears on homepage |
| Date | Date Picker | When the review was left |

### Custom Taxonomy: `ce_city`
Attached to both `ce_property` and `ce_testimonial`. Allows filtering by city (Riyadh, Jeddah, NEOM, AlUla, etc.). Managed by admin — no hardcoding.

---

## 6. Developer Reliability Badges

A custom badge system displayed on property cards and single property pages to signal developer trustworthiness. Badges are assigned per-property by the admin.

**Badge Tiers:**

| Badge | Display | Criteria (admin-determined) |
|---|---|---|
| Verified Developer | Gold shield icon | Developer identity confirmed by Crowns Estates |
| Track Record | Gold shield + star | Developer has completed previous projects on time |
| Premium Partner | Gold shield + crown | Long-standing partnership with Crowns Estates |
| None | No badge | New or unvetted developer |

Badge tier is stored as an ACF select field on `ce_property`. Visual design: small badge icon + label displayed next to the developer name on property cards and detail pages.

---

## 7. Gated Brochure Downloads

Per-property option to gate PDF brochure downloads behind an email capture form.

**Behaviour:**
- If `ce_brochure_gated` is `false` → direct download link
- If `ce_brochure_gated` is `true` → clicking "Download Brochure" opens a modal with: Name, Email, GDPR consent checkbox
- On submit: stores lead in `ce_enquiries` table (source: "brochure download"), sends brochure link via email, triggers GA4 custom event
- User receives email with direct download link + auto-responder text

This creates a lead generation funnel from brochure interest.

---

## 8. Page Designs

### Home
- Full-width hero with background image, overlay, headline, tagline, CTA ("View Opportunities" → /projects)
- Trust bar: "20 Years in Saudi Arabia | British Expat Expertise | End-to-End Investor Support"
- Featured Properties grid (3 cards, `ce_featured == true`)
- "Why Invest in Saudi Arabia" — 3 icon blocks (Market Growth, Golden Visa, Freehold Zones)
- Testimonials section — 3 featured testimonials, static grid
- About snippet with link to About Us page
- CTA banner: "Ready to Invest? Talk to Our Team" → Register Interest modal

### Projects
- Page header with intro text
- **Filter bar:** City tabs (from `ce_city`), developer filter, completion status filter
- Property card grid — image, city badge, developer badge, title, type, status pill, price, bedrooms, CTA
- Currency toggle visible in page header

### Single Property
- Full-width image gallery (hero image + thumbnails)
- Property info grid: all ACF fields in clean two-column layout
- Developer name + reliability badge
- Price via `ce_display_price()` with currency toggle
- Status badge (colour-coded: Off-Plan = blue, Under Construction = amber, Ready = green)
- Freehold badge
- Brochure download button (gated or direct per `ce_brochure_gated`)
- Google Maps embed
- Related testimonials (filtered by matching `ce_city`)
- Inline Register Interest form
- Property disclaimer text

### How It Works
- 5-step investor journey (numbered sections with icons)
- Investment Calculator card (see section 9)
- Saudi Premium Residency (Golden Visa) explainer
- FAQ accordion (6-8 questions)
- CTA banner → Contact page

### About Us
- Hero with "About Crowns Estates"
- Two-column: team intro text (left), image placeholder (right)
- Core values: Trust, Local Knowledge, Investor-First
- Stats bar: "200+ Properties Sourced", "20 Years in KSA", "50+ Happy Investors"
- CTA banner

### Contact
- Hero with "Get in Touch"
- Two-column: enquiry form (left), contact info + WhatsApp link (right)
- Form: Name, Email, Phone, Message, GDPR checkbox → REST endpoint
- Google Maps embed placeholder

### Blog / Insights
- Card grid: featured image, title, excerpt, date, category badge
- Single post: two-column (content + sidebar), related posts, CTA banner
- Sidebar: search, categories, recent posts, Register Interest CTA card
- 5 initial articles uploaded (content provided by client, formatted by dev team)

### Rentals (Bonus)
- "Coming Soon" placeholder
- Brief description of upcoming rental services
- Email capture for notifications
- CTA: "Browse Investment Properties" → /projects

---

## 9. Investment Calculator (Bonus)

Interactive cost estimator on the How It Works page.

**Inputs:**

| Field | Type |
|---|---|
| Property Price | Number input |
| Currency | Toggle (GBP / SAR / USD) |
| Property Type | Select (Apartment, Villa, Commercial) |

**Outputs:**

| Line Item | Calculation |
|---|---|
| Property Price | As entered |
| Registration Fee (2.5%) | price × 0.025 |
| VAT (5%) | price × 0.05 |
| Agency Fee (2%) | price × 0.02 |
| **Estimated Total** | Sum |

Rates configurable via ACF options page. Disclaimer: "This calculator provides estimates only and does not constitute financial advice."

Vanilla JS, instant calculation, no page reload. CTA: "Speak to Our Team About This Investment" → opens Register Interest modal.

---

## 10. Multi-Currency Display (Bonus)

All property prices support multi-currency display.

- **Server-side:** Renders native currency (SAR/GBP/USD as set per listing) with `data-price` and `data-currency` attributes
- **Client-side:** JS reads cookie `ce_currency`, fetches rates from `/wp-json/ce/v1/rates`, recalculates all `[data-price]` elements without reload
- **Exchange rates:** Stored in ACF options page, manually updated by admin
- **Cache-safe:** No cookies read server-side — fully compatible with WP Super Cache

---

## 11. Custom Admin Dashboard (Bonus)

Branded WordPress admin panel matching the griyakita reference design. Clean, modern, client-friendly.

### Custom Dashboard Page
Replaces default WordPress dashboard with stat cards:
- **Row 1:** Total Properties, Active, Sold/Rented, Pending Review
- **Row 2:** Tour/Enquiry Requests (pending count), Total Users, Total Property Value
- Sparkline charts via Chart.js

### Reorganised Admin Sidebar

```
Dashboard
──────────
Properties (ce_property)
Blog Posts
Categories / Tags
──────────
Enquiries (from DB table)
──────────
Users
──────────
Site Settings (ACF options)
──────────
Analytics (link to GA4)
```

Remove clutter: Comments, Tools, and unused default menu items hidden.

### Admin Branding
- Custom admin colour scheme (dark sidebar, gold accents)
- Custom admin footer text: "Crowns Estates Admin Panel"
- Renamed menu labels ("Posts" → "Blog Posts")

---

## 12. Analytics & Tracking

### Google Analytics 4 (GA4)
- Full GA4 property setup
- Connected via Google Tag Manager (GTM)
- Configured for Core Web Vitals monitoring

### Custom Event Tracking

| Event | Trigger | GA4 Event Name |
|---|---|---|
| WhatsApp click | User clicks WhatsApp widget | `whatsapp_click` |
| Form submission | Register Interest form submitted | `enquiry_submit` |
| Contact form | Contact page form submitted | `contact_submit` |
| Brochure download | Brochure downloaded (gated or direct) | `brochure_download` |
### Google Search Console
- Site verified and submitted
- XML sitemap generated (via Yoast SEO) and submitted
- Initial indexing requested for all Phase 1 pages

---

## 13. SEO Foundation

| Element | Implementation |
|---|---|
| Semantic HTML | Proper H1 → H6 hierarchy on every page |
| Dynamic Meta | Yoast SEO — custom title/description per property and page |
| Image Optimisation | Compressed uploads, meaningful ALT tags on all images |
| XML Sitemap | Auto-generated via Yoast, submitted to Search Console |
| Schema Markup | JSON-LD: `RealEstateListing` (properties), `Review` (testimonials), `RealEstateAgent` (sitewide), `FAQPage` (How It Works) |
| Performance | Core Web Vitals targets: LCP < 2.5s, FID < 100ms, CLS < 0.1 |

---

## 14. Persistent Enquiry Features

- **Sticky WhatsApp button** — bottom-right, visible on all pages, GA4 tracked
- **Register Interest modal** — triggered by CTAs. Fields: Name, Email, Phone, Property of Interest (optional), Message, GDPR consent. Submits to REST endpoint, stored in DB, GA4 tracked.
- **Auto-responder email** — "Thank you for your interest. Our team will be in touch within 24 hours."
- **Gated brochure download** — optional per-property email capture before PDF delivery

---

## 15. Regulatory & Legal

### Required Pages
| Page | Slug |
|---|---|
| Privacy Policy | `/privacy-policy` |
| Terms of Service | `/terms` |
| Disclaimer | `/disclaimer` |
| Cookie Policy | `/cookie-policy` |

Content entered by admin via WordPress editor using default `page.php` template.

### Sitewide Elements
- **Cookie consent banner** — GDPR-compliant (Complianz or CookieYes plugin)
- **Footer disclaimer:** "Crowns Estates is not regulated by the FCA. Information on this website does not constitute financial advice. Please seek independent advice before making investment decisions."
- **Property disclaimer** (per listing): "Prices, specifications, and completion dates are indicative and subject to change."

### GDPR Compliance
- All forms display consent checkbox with link to Privacy Policy
- Form submissions stored in WordPress database for data access/deletion requests
- Data retention policy documented in Privacy Policy

---

## 16. Tech Stack

| Layer | Technology |
|---|---|
| CMS | WordPress 6.x |
| Theme | Custom (Underscores `_s` starter) |
| Fields | Advanced Custom Fields (ACF) |
| Front-end JS | Vanilla JavaScript (no jQuery dependency) |
| 3D Rendering | Three.js + WebGL |
| 3D Animation | GSAP + ScrollTrigger |
| Smooth Scroll | Lenis |
| 3D Models | GLTF/GLB (created in Spline/Blender, compressed via Draco) |
| Design | Figma (component-based design system) |
| Analytics | GA4 via GTM |
| SEO | Yoast SEO |
| Caching | WP Super Cache |
| Backups | UpdraftPlus |
| GDPR | Complianz or CookieYes |
| Hosting | TBD — recommended: Cloudways + DigitalOcean (~£10-15/mo) |

---

## 17. Plugins Required

| Plugin | Purpose |
|---|---|
| Advanced Custom Fields (ACF) | Property/testimonial fields, site options |
| Yoast SEO | On-page SEO, XML sitemap, meta management |
| WP Super Cache | Performance / page caching |
| UpdraftPlus | Automated backups |
| Complianz or CookieYes | GDPR cookie consent |
| Google Site Kit | GA4 + Search Console integration |

Minimal plugin footprint. No page builders, no bloat.

---

## 18. Content Requirements (from Client)

R2 Design collects from client, dev team formats for web:

| Content | Deadline | Notes |
|---|---|---|
| High-res property renders/images | End of Week 2 | Per-property gallery images |
| Property data (prices, specs, location, dates) | End of Week 2 | Populates ACF fields |
| About Us copy | End of Week 4 | Team story, values, stats |
| How It Works copy | End of Week 4 | Process steps, Golden Visa info |
| PDF brochures | End of Week 4 | Per-property downloadable files |
| Logo + brand assets | End of Week 2 | For Figma + WordPress theme |

Dev team will:
- Format, structure, and lightly edit all copy for web readability and conversions
- Build the blog framework and upload up to 5 initial articles (content from client)

---

## 19. Flexibility & Phase 2

This spec is built for extensibility:

- **Colours/fonts** — CSS variables + Figma variables, one change updates everything
- **ACF fields** — addable/removable without touching theme code
- **Pages** — any page can be added via Figma components → WordPress templates
- **Cities** — taxonomy, no hardcoding
- **Calculator rates** — admin-editable via ACF options

### Phase 2 (out of scope)
- Live rental listings
- Visa application forms
- User accounts / investor portal
- Payment processing
- Arabic language version
- Automated Google Reviews API pull
- AI Assistant chatbot (Claude API — deferred, build when site is complete)
- Newsletter subscriber system
- Tour requests management

---

## 20. Key Decisions

1. **Custom theme over page builder** — client only needs to update listings, not redesign pages
2. **Figma as living design system** — client can design new sections, dev team builds them
3. **Currency conversion is client-side only** — cache-compatible with WP Super Cache
4. **Testimonials are static grid, not carousel** — simpler, cache-friendly
5. **DB table for enquiries uses `after_switch_theme` hook** — not `register_activation_hook`
6. **`ce_city` taxonomy shared** between properties AND testimonials
7. **Blog in Phase 1** — critical for SEO, every competitor has one
8. **Gated brochures are per-property toggle** — not a global setting
10. **Developer badges are admin-assigned** — no automated verification system
11. **GA4 via GTM** — allows flexible event tracking without code changes
12. **R2 handles client comms** — dev team focuses on building, R2 manages expectations
13. **Full immersive 3D via Three.js** — not decorative; 3D is the primary visual language across all pages
14. **GSAP ScrollTrigger for scroll-driven animation** — camera movements, section reveals, and transitions tied to scroll
15. **3D graceful fallback** — static images + CSS parallax if WebGL unsupported or low-power device
16. **3D assets lazy-loaded** — page content readable before 3D finishes loading, no LCP penalty

---

## Appendix: File Structure

```
crown-estates-website/
├── .gitignore
├── README.md
├── HANDOVER.md
├── docs/superpowers/specs/...
├── docs/superpowers/plans/...
├── preview/index.html
└── wp-content/themes/crowns-estates/
    ├── style.css
    ├── functions.php
    ├── header.php
    ├── footer.php
    ├── index.php
    ├── page.php
    ├── front-page.php
    ├── page-projects.php
    ├── page-how-it-works.php
    ├── page-about.php
    ├── page-contact.php
    ├── page-rentals.php
    ├── single-ce_property.php
    ├── archive-ce_property.php
    ├── single.php
    ├── archive.php
    ├── sidebar.php
    ├── 404.php
    ├── screenshot.png
    ├── inc/
    │   ├── cpt-property.php
    │   ├── cpt-testimonial.php
    │   ├── taxonomy-city.php
    │   ├── acf-fields-property.php
    │   ├── acf-fields-testimonial.php
    │   ├── acf-options.php
    │   ├── enqueue.php
    │   ├── currency-helpers.php
    │   ├── enquiry-handler.php
    │   ├── schema-markup.php
    │   └── admin-dashboard.php
    ├── template-parts/
    │   ├── property-card.php
    │   ├── testimonial-card.php
    │   ├── hero.php
    │   ├── trust-bar.php
    │   ├── cta-banner.php
    │   ├── modal-register-interest.php
    │   ├── whatsapp-button.php
    │   ├── developer-badge.php
    │   └── brochure-gate.php
    ├── js/
    │   ├── calculator.js
    │   ├── currency-toggle.js
    │   ├── modal.js
    │   ├── city-filter.js
    │   ├── faq-accordion.js
    │   ├── admin-dashboard.js
    │   └── 3d/
    │       ├── scene-manager.js          # Three.js scene setup, renderer, camera, resize
    │       ├── scroll-controller.js      # GSAP ScrollTrigger + Lenis integration
    │       ├── hero-scene.js             # Homepage 3D skyline/property scene
    │       ├── projects-map.js           # Projects page 3D city map with pins
    │       ├── property-viewer.js        # Single property interactive 3D model viewer
    │       ├── journey-scene.js          # How It Works scroll-driven 3D path
    │       ├── particles.js              # Gold particle system (reusable)
    │       └── fallback.js               # WebGL detection + static fallback loader
    ├── models/
    │   ├── skyline.glb                   # Homepage hero 3D model
    │   ├── city-map.glb                  # Projects page city map
    │   └── properties/                   # Per-property 3D models (optional)
    ├── img/
    │   └── placeholder-property.jpg
    └── sample-content/
        ├── 5-things-uk-investors.md
        ├── golden-visa-guide.md
        ├── neom-investment-guide.md
        ├── understanding-off-plan.md
        └── riyadh-vs-jeddah.md
```
