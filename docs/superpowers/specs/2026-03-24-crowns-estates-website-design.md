# Crowns Estates Website — Design Spec
**Date:** 2026-03-24
**Status:** Draft — For Client Presentation
**Repo:** https://github.com/jalookout7-eng/crown-estates-website
**Domain:** www.crownsestates.co.uk

---

## 1. Project Overview

A WordPress website for **Crowns Estates**, a UK-registered real estate agency specialising in Saudi Arabian property investment opportunities. The site targets UK and global investors seeking entry into the Saudi property market.

**Tagline:** "Connecting Investors with Quality Property Opportunities."

**Core values to convey:** Trust, Local Knowledge, Clarity, Confidence.

---

## 2. Target Audience

- UK-based and global investors (not Saudi-local)
- First-time entrants to the Saudi property market
- Investors interested in the Saudi Premium Residency (Golden Visa) program
- High-net-worth individuals seeking off-plan and under-construction investment opportunities

**Key pain point:** Lack of confidence in Saudi property systems, developers, and processes. The site must simplify, guide, and reassure.

---

## 3. Design System

All values are placeholders — easily updated once client provides final brand assets.

| Token | Value | Notes |
|---|---|---|
| Background | `#FFFFFF` | White |
| Primary text | `#0A0A0A` | Near-black |
| Gold accent | `#C4973A` | Borders, CTAs, highlights |
| Heading font | Playfair Display | Serif — luxury feel |
| Body font | Inter | Clean sans-serif |
| Base spacing unit | 8px | Multiples of 8 throughout |

**Vibe:** Easy / Informative / Minimalist. No clutter. Generous whitespace. Gold used sparingly.

> Note: Colours can be derived from the Crowns Estates logo once provided. All CSS variables are centralised in `style.css` for instant global changes.

---

## 4. Site Structure — Phase 1

| Page | Slug | Purpose |
|---|---|---|
| Home | `/` | Hero, tagline, featured properties, trust signals, about snippet |
| Projects | `/projects` | All property listings, filterable by city |
| How It Works | `/how-it-works` | Saudi investment process explainer, residency info |
| About Us | `/about` | Who we are, 20 years in KSA, values |
| Contact | `/contact` | Enquiry form + WhatsApp |
| Blog / Insights | `/blog` | Market guides, investment tips, area profiles |
| Rentals | `/rentals` | "Coming Soon" placeholder (Phase 2) |

---

## 5. WordPress Architecture

### Theme
- **Base:** Custom theme built on `_s` (Underscores) starter
- **No page builder** — clean, fast, maintainable
- All design tokens in `style.css` as CSS custom properties for easy client-led changes
- Fully responsive (mobile-first)

### Custom Post Type: `ce_property`
Registered via the theme's `functions.php`. Admin sees "Properties" in the dashboard sidebar.

### Custom Taxonomy: `ce_city`
Allows filtering properties by city (Riyadh, Jeddah, NEOM, AlUla, etc.). Cities are managed by admin — no hardcoding.

### Property Fields (ACF — Advanced Custom Fields)
Each property listing has the following editable fields:

| Field Label | Field Type | Notes |
|---|---|---|
| City | Taxonomy | Links to `ce_city` |
| Developer | Text | e.g. "ROSHN", "Dar Global" |
| Price From | Number | Starting price in SAR or GBP |
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
| Brochure PDF | File | Downloadable PDF |
| Featured | True/False | Appears on homepage |
| Map Embed | Text | Google Maps embed URL |

> All fields are optional — listings can be published with partial data during early setup.

---

## 6. Page Designs

### Home
- Full-width hero with background image (Saudi skyline / luxury property render), overlay, headline, tagline, CTA button ("View Opportunities")
- Trust bar: "20 Years in Saudi Arabia | British Expat Expertise | End-to-End Investor Support"
- Featured Properties grid (3 cards, pulled from properties marked `Featured = true`)
- "Why Invest in Saudi Arabia" — 3 icon blocks (Market Growth, Golden Visa, Freehold Zones)
- About snippet with link to About Us page
- CTA banner: "Ready to Invest? Talk to Our Team" → Register Interest form

### Projects
- Page header with intro text
- City filter tabs (dynamic, pulled from `ce_city` taxonomy)
- Property card grid — each card shows: image, city badge, property name, type, status, price from, bedrooms, CTA button
- Single property page: full gallery, all fields displayed, downloadable brochure, enquiry form

### How It Works
- Step-by-step investor journey (numbered sections):
  1. Discover the Opportunity
  2. Speak to Our Team
  3. Property Selection
  4. Reservation & Legal Process
  5. Completion & Handover
- Saudi Premium Residency (Golden Visa) explainer section
- FAQ accordion
- CTA to Contact

### About Us
- Team intro — British expats, 20 years in KSA
- Core values: Trust, Local Knowledge, Investor-First
- Timeline or stats bar (optional, can be added after client input)

### Contact
- Enquiry form (Name, Email, Phone, Message) → sends to `info@crownsestates.co.uk`
- WhatsApp direct link
- Office address / social links (to be provided by client)

### Blog / Insights
- Standard WordPress posts, categorised (Market Updates, Investment Guides, Area Profiles, News)
- Card grid layout with featured image, title, excerpt, date
- Single post template matching site design
- Sidebar with categories, recent posts, and Register Interest CTA

**Sample launch posts (to be created with placeholder content):**
1. "5 Things UK Investors Need to Know About Saudi Property in 2026"
2. "Golden Visa Through Real Estate — A Step-by-Step Guide"
3. "NEOM Investment Guide — What's Available and What to Expect"
4. "Understanding Off-Plan Property in Saudi Arabia"
5. "Riyadh vs Jeddah — Where Should You Invest?"

### Rentals (Phase 2 placeholder)
- Simple "Coming Soon" page with brief description and email capture

---

## 7. Investment Calculator

A simple, interactive cost estimator on the How It Works page (also linkable from homepage).

**Inputs:**
| Field | Type | Notes |
|---|---|---|
| Property Price | Number | User enters amount |
| Currency | Toggle | GBP / SAR / USD |
| Property Type | Select | Apartment, Villa, Commercial |

**Outputs (estimated):**
| Line Item | Calculation |
|---|---|
| Property Price | As entered |
| Registration Fee (2.5%) | price * 0.025 |
| VAT (5%) | price * 0.05 |
| Agency Fee (2%) | price * 0.02 |
| **Estimated Total** | Sum |

> Rates are configurable via ACF options page so the client can update percentages without code changes. A disclaimer is displayed: "This calculator provides estimates only and does not constitute financial advice."

**Front-end:** Clean card layout, instant calculation (vanilla JS, no page reload), gold accent CTA: "Speak to Our Team About This Investment"

---

## 8. Multi-Currency Display

All property prices support multi-currency display on the front end.

- **Default display:** The currency set per listing (SAR, GBP, or USD)
- **Currency toggle:** A small toggle in the site header and on the Projects page allows visitors to switch displayed currency
- **Exchange rates:** Stored in an ACF options page, manually updated by admin (avoids API dependency and keeps it simple)
- **Fallback:** If no rate is set, prices display in the listing's native currency only

---

## 9. Testimonials & Google Reviews

### Custom Post Type: `ce_testimonial`
Allows admin to curate which reviews appear on the site.

| Field | Type | Notes |
|---|---|---|
| Client Name | Text | Can be anonymised (e.g. "UK Investor") |
| Location | Text | e.g. "London, UK" |
| Quote | Textarea | The testimonial text |
| Rating | Number (1-5) | Displayed as stars |
| Google Review Link | URL | Links back to original Google review |
| Property / City | Taxonomy | Optional — links to a project or city |
| Featured | True/False | Appears on homepage |
| Date | Date Picker | When the review was left |

**Display locations:**
- Homepage: 3 featured testimonials in a carousel/slider
- Single property pages: related testimonials (filtered by city)
- Dedicated `/testimonials` page (optional, can be enabled later)

**Google Business Profile integration:**
- Admin copies selected reviews from Google Business Profile into the CPT
- Each testimonial links back to the Google review for authenticity
- Schema.org `Review` markup added for SEO rich snippets
- Future enhancement: automated Google Reviews API pull (Phase 2)

---

## 10. Regulatory & Legal

### Required Pages
| Page | Slug | Content |
|---|---|---|
| Privacy Policy | `/privacy-policy` | GDPR-compliant data processing disclosure, cookie usage, third-party services |
| Terms of Service | `/terms` | Website usage terms, limitation of liability |
| Disclaimer | `/disclaimer` | "Crowns Estates does not provide financial or legal advice. All property information is provided for general guidance only." |
| Cookie Policy | `/cookie-policy` | Cookie categories and consent management |

### Sitewide Elements
- **Cookie consent banner** — displayed on first visit, GDPR-compliant (accept/reject/manage preferences). Plugin: Complianz or CookieYes.
- **Footer disclaimer line** — persistent on every page: "Crowns Estates is not regulated by the FCA. Information on this website does not constitute financial advice. Please seek independent advice before making investment decisions."
- **Property listing disclaimer** — shown on every single property page: "Prices, specifications, and completion dates are indicative and subject to change. Please contact us for the latest information."

### GDPR Compliance
- All forms display consent checkbox: "I agree to the Privacy Policy and consent to Crowns Estates processing my data to respond to my enquiry"
- Form submissions stored in WordPress database (not just emailed) for data access/deletion requests
- Data retention policy documented in Privacy Policy

---

## 11. Persistent Enquiry Features

- **Sticky WhatsApp button** — bottom-right corner, visible on all pages, links to WhatsApp (number to be provided by client)
- **Register Interest modal** — triggered by primary CTAs across the site. Fields: Name, Email, Phone, Property of Interest (optional), Message, GDPR consent checkbox. Submits via `wp_mail()` to `info@crownsestates.co.uk` AND stored in WordPress database.
- **Auto-responder email** — on form submission, enquirer receives a branded confirmation: "Thank you for your interest. Our team will be in touch within 24 hours."

---

## 12. Plugins Required

| Plugin | Purpose |
|---|---|
| Advanced Custom Fields (ACF) | Property meta fields |
| Contact Form 7 or WPForms Lite | Enquiry / Register Interest forms |
| Yoast SEO | On-page SEO |
| WP Super Cache | Performance |
| UpdraftPlus | Backups |
| Complianz or CookieYes | GDPR cookie consent |
| Google Analytics (via GTM) | Traffic and conversion tracking |

> Plugin list is minimal and swappable — all choices can be changed before build starts.

---

## 13. Flexibility Notes

This spec is intentionally loose to allow client input:

- **Colours** — all defined as CSS variables; one change updates the entire site
- **Fonts** — swappable in one line per font
- **Fields** — ACF fields can be added/removed without touching theme code
- **Pages** — any page can be added, renamed, or restructured
- **Cities** — managed via taxonomy, no hardcoding
- **Phase 2** — Rentals and Visa Services pages are stubbed, ready to build out
- **AI Agent** — placeholder section in How It Works / Contact for future AI chat integration

---

## 14. Hosting & Deployment

- Domain: `www.crownsestates.co.uk` (already registered)
- Recommended: VPS (e.g. DigitalOcean, Hetzner) running LAMP/LEMP stack, or managed WordPress host (e.g. Kinsta, WP Engine)
- Code managed via GitHub: `https://github.com/jalookout7-eng/crown-estates-website`
- Deployment: Git pull to server or CI/CD pipeline (to be decided with client)

---

## 15. Out of Scope — Phase 1

- User accounts / investor portal
- Payment processing
- Live rental listings
- Visa application forms
- Arabic language version
