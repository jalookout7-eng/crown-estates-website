# Crowns Estates Website — Backend Spec
**Date:** 2026-04-04
**Status:** Approved — Ready for Implementation Planning
**Relates to:** `2026-04-02-crowns-estates-website-design-v2.md`
**Repo:** https://github.com/jalookout7-eng/crown-estates-website
**Scope:** WordPress backend only — no 3D, no frontend visual layer

---

## 1. Overview

This spec covers all server-side backend code for the Crowns Estates WordPress website. It is a companion to the v2 design spec and defines the data layer, REST API, enquiry system, email system, admin dashboard, multi-currency, analytics integration, schema markup, and GDPR compliance.

**Hosting:** Managed WordPress hosting — Cloudways + DigitalOcean (~£10–15/mo).
**WordPress version:** 6.x
**PHP version:** 8.x

---

## 2. Architecture — Must-Use Plugin + Theme Split

All backend logic is split into two layers:

### Must-Use Plugin — `wp-content/mu-plugins/crowns-estates-core/`

Owns all data and business logic. Self-contained — does not require theme files. Auto-loads on every WordPress request. Cannot be deactivated by the client.

Responsibilities:
- CPT registration (`ce_property`, `ce_testimonial`)
- Taxonomy registration (`ce_city`)
- Custom DB table (`wp_ce_enquiries`) — created via `dbDelta()` on load
- REST API endpoints (`/wp-json/ce/v1/`)
- Currency helper functions (`ce_display_price()`, rates endpoint)
- Enquiry handler (form → DB → email)
- Email system (templates, transport, digest cron)

### Theme — `wp-content/themes/crowns-estates/`

Owns all presentation and UI logic. Depends on the mu-plugin for CPT/taxonomy/DB.

Responsibilities:
- Page templates and template parts
- ACF field group definitions (property, testimonial, options pages)
- Admin dashboard UI (custom branded WP admin)
- Asset enqueuing (JS/CSS)
- GA4/GTM data layer integration
- Schema markup output (JSON-LD via `wp_head`)
- GDPR/cookie consent output

**Principle:** If the theme changes, all property data, enquiries, and API endpoints survive untouched.

---

## 3. Data Layer

### 3.1 Custom Post Type: `ce_property`

Registered in mu-plugin. Admin sidebar label: "Properties". Supports: title, editor, thumbnail, revisions.

**ACF Fields:**

| Field Label | Field Name | Type | Notes |
|---|---|---|---|
| City | `ce_city` | Taxonomy (`ce_city`) | Filterable |
| Developer | `ce_developer` | Text | e.g. "ROSHN", "Dar Global" |
| Developer Badge | `ce_developer_badge` | Select | none / verified / track_record / premium_partner |
| Price From | `ce_price_from` | Number | Starting price |
| Currency | `ce_currency` | Select | SAR / GBP / USD |
| Property Type | `ce_property_type` | Select | Apartment / Villa / Commercial |
| Status | `ce_status` | Select | Off-Plan / Under Construction / Ready |
| Completion Date | `ce_completion_date` | Date Picker | Estimated handover |
| Bedrooms | `ce_bedrooms` | Number | |
| Size (Sq.M.) | `ce_size_sqm` | Number | |
| Is Freehold | `ce_is_freehold` | True/False | |
| Short Description | `ce_short_description` | Textarea | Used on listing cards |
| Full Description | `ce_full_description` | WYSIWYG | Used on single property page |
| Gallery | `ce_gallery` | Gallery | Multiple images |
| Brochure PDF | `ce_brochure_pdf` | File | Downloadable |
| Brochure Gated | `ce_brochure_gated` | True/False | If true, requires email before download |
| Featured | `ce_featured` | True/False | Appears on homepage |
| Map Embed | `ce_map_embed` | Text | Google Maps embed URL |

### 3.2 Custom Post Type: `ce_testimonial`

Registered in mu-plugin. Admin sidebar label: "Testimonials". Supports: title.

**ACF Fields:**

| Field Label | Field Name | Type | Notes |
|---|---|---|---|
| Client Name | `ce_client_name` | Text | Can be anonymised |
| Location | `ce_location` | Text | e.g. "London, UK" |
| Quote | `ce_quote` | Textarea | The testimonial text |
| Rating | `ce_rating` | Number (1-5) | Displayed as stars |
| Google Review Link | `ce_google_review_link` | URL | Links to original Google review |
| City | `ce_city` | Taxonomy (`ce_city`) | For related testimonials on property pages |
| Featured | `ce_featured` | True/False | Appears on homepage |
| Date | `ce_date` | Date Picker | When the review was left |

### 3.3 Custom Taxonomy: `ce_city`

Attached to both `ce_property` and `ce_testimonial`. Hierarchical: false. Managed by admin — no hardcoding. Initial terms: Riyadh, Jeddah, NEOM, AlUla, Mecca, Medina.

### 3.4 Custom Database Table: `wp_ce_enquiries`

Created via `dbDelta()` on mu-plugin load. Indexed on `email`, `status`, `created_at`.

```sql
CREATE TABLE wp_ce_enquiries (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name         VARCHAR(255) NOT NULL,
  email        VARCHAR(255) NOT NULL,
  phone        VARCHAR(50) DEFAULT NULL,
  message      TEXT DEFAULT NULL,
  property_id  BIGINT UNSIGNED DEFAULT NULL,
  source       VARCHAR(50) NOT NULL,
  gdpr_consent TINYINT(1) NOT NULL DEFAULT 0,
  ip_address   VARCHAR(45) DEFAULT NULL,
  status       VARCHAR(20) NOT NULL DEFAULT 'new',
  created_at   DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY email (email),
  KEY status (status),
  KEY created_at (created_at)
);
```

**Source values:** `register_interest`, `contact_form`, `brochure_download`
**Status values:** `new`, `read`, `replied`, `archived`

---

## 4. REST API Endpoints

All endpoints registered under `/wp-json/ce/v1/`.

### 4.1 Public Endpoints

#### `POST /ce/v1/enquiry`
Handles Register Interest and Contact form submissions.

**Request body:**
```json
{
  "name": "string (required)",
  "email": "string (required, valid email)",
  "phone": "string (optional)",
  "message": "string (optional)",
  "property_id": "integer (optional)",
  "source": "register_interest | contact_form",
  "gdpr_consent": "boolean (required, must be true)",
  "nonce": "string (required)"
}
```

**Flow:**
1. Verify nonce (`wp_verify_nonce`)
2. Sanitise all fields (`sanitize_text_field`, `sanitize_email`, `wp_kses_post`)
3. Validate: GDPR must be true, email must be valid
4. Insert row into `wp_ce_enquiries` with `status = 'new'`
5. Send auto-responder email to submitter
6. Send notification email to admin address (from ACF options)
7. Return `{"success": true}` — JS pushes `enquiry_submit` to `dataLayer`

**Error responses:** `400` for validation failures, `403` for nonce failure.

---

#### `POST /ce/v1/brochure-gate`
Gated brochure email capture.

**Request body:**
```json
{
  "name": "string (required)",
  "email": "string (required)",
  "property_id": "integer (required)",
  "gdpr_consent": "boolean (required, must be true)",
  "nonce": "string (required)"
}
```

**Flow:**
1. Verify nonce
2. Sanitise + validate
3. Verify property has a brochure PDF (`ce_brochure_pdf` field not empty)
4. Insert into `wp_ce_enquiries` with `source = 'brochure_download'`
5. Generate signed 24-hour download URL: `add_query_arg(['ce_brochure' => $property_id, 'token' => $token, 'expires' => time() + 86400], home_url())`
6. Email user the signed URL + property name
7. Return `{"success": true}` — JS pushes `brochure_download` to `dataLayer`

**Signed URL validation:** On `template_redirect`, check `ce_brochure` param → verify token + expiry → serve file or return 403.

---

#### `GET /ce/v1/rates`
Returns current exchange rates from ACF options. No authentication required.

**Response:**
```json
{
  "GBP": 1,
  "SAR": 4.72,
  "USD": 1.26,
  "updated": "2026-04-01"
}
```

Response cached via WordPress transients for 1 hour. `Cache-Control: max-age=3600` header set.

---

### 4.2 Admin-Only Endpoints (require `manage_options` capability + nonce)

#### `GET /ce/v1/enquiries`
Paginated enquiry list for admin UI.

**Query params:** `page`, `per_page` (default 20), `status` filter, `search`

**Response:** Array of enquiry objects + total count header.

---

#### `GET /ce/v1/enquiries/export`
Downloads all enquiries (or filtered by status/date) as CSV.

**Response:** `Content-Type: text/csv`, `Content-Disposition: attachment; filename="enquiries-{date}.csv"`

**CSV columns:** ID, Name, Email, Phone, Message, Source, Property, Status, GDPR Consent, IP Address, Date

---

#### `PATCH /ce/v1/enquiries/{id}`
Updates enquiry status.

**Request body:** `{"status": "read | replied | archived"}`

---

## 5. Enquiry System

### 5.1 Email Transport

**Recommended:** Brevo (free tier: 300 emails/day) or Mailgun via **WP Mail SMTP** plugin.
- All emails flow through WordPress `wp_mail()` — WP Mail SMTP intercepts and reroutes via Brevo API
- DKIM/SPF authentication configured at domain level
- Swapping providers requires only WP Mail SMTP plugin config change — no code changes

**Fallback:** If no SMTP plugin is configured, falls back to server `wp_mail()`. Documented risk: higher spam rate, no delivery visibility.

### 5.2 Email Templates

PHP templates in `inc/email-templates/` with inline CSS. Variables injected via `str_replace` tokens.

| Template File | Recipient | Trigger | Subject |
|---|---|---|---|
| `auto-responder.php` | Enquirer | Any enquiry | "Thank you for your enquiry — Crowns Estates" |
| `admin-notification.php` | Admin inbox | Any enquiry | "New enquiry: {name} — {source}" |
| `brochure-delivery.php` | Enquirer | Brochure gate | "Your Crowns Estates brochure — {property name}" |
| `daily-digest.php` | Admin digest address | WP cron 8am UTC | "Crowns Estates — {n} new enquiries today" |

From name, from address, and reply-to address configured in ACF options (Email Settings group).

### 5.3 Daily Digest Cron

Registered via `wp_schedule_event` on mu-plugin load. Runs daily at 8am UTC. Checks for enquiries with `status = 'new'` and `created_at >= NOW() - INTERVAL 24 HOUR`. Only sends if at least one new enquiry exists. Toggle on/off and recipient address editable in ACF options.

---

## 6. Multi-Currency System

### 6.1 Server-Side

Helper function `ce_display_price($post_id)` renders the property's native currency with `data-*` attributes:

```html
<span class="ce-price" data-price="250000" data-currency="GBP">£250,000</span>
```

No cookie reads server-side — page is fully cacheable by WP Super Cache.

### 6.2 Client-Side (`currency-toggle.js`)

1. On toggle click: set `ce_currency` cookie (1-year expiry)
2. Fetch `/wp-json/ce/v1/rates`
3. Convert all `[data-price]` elements using rate from response
4. Update currency symbol display
5. Persists across pages via cookie

### 6.3 Cache Compatibility

The `/ce/v1/rates` endpoint is excluded from WP Super Cache page caching. Response cached via WordPress transients for 1 hour. `ce_currency` cookie is not read server-side — WP Super Cache never sees it.

---

## 7. Admin Dashboard

### 7.1 User Roles

| Role | Based On | Assigned To | Capabilities |
|---|---|---|---|
| **Admin** | WP Administrator (restricted) | Client (Crowns Estates) | Manage properties, testimonials, blog, view + export enquiries, edit site settings. Cannot install plugins or edit theme files. |
| **Editor** | WP Editor | R2 Design | Edit all content. View enquiries (read-only). Cannot export leads, change settings, or manage users. |
| **Developer** | WP Administrator (full) | jalookout7-eng | Full access. |

Admin role restrictions applied via `user_has_cap` filter — removes `install_plugins`, `edit_themes`, `update_core` from client admin accounts.

### 7.2 Custom Dashboard Page

Replaces default WordPress dashboard. Registered via `wp_dashboard_setup`. Removes all default widgets.

**Row 1 — Stat cards:**
- Total Properties (count of `ce_property` posts, any status)
- Active Listings (count where `post_status = 'publish'`)
- Total Enquiries (count of rows in `wp_ce_enquiries`)
- New Enquiries (count where `status = 'new'`) — highlighted if > 0

**Row 2 — Charts + Quick actions:**
- Enquiries over last 30 days — sparkline via Chart.js
- Enquiries by source — breakdown table (register_interest / contact_form / brochure_download)
- Quick actions: + Add Property, + Add Blog Post, ↓ Export Enquiries CSV

All counts fetched via admin AJAX on dashboard load — not cached.

### 7.3 Enquiries Admin Page

Custom admin menu page under Dashboard sidebar. Accessible to Admin and Editor roles.

**Features:**
- Status tab filters: All / New / Read / Replied / Archived
- Search by name or email
- Table: Name, Email, Source, Property (linked), Status badge, Date
- Click row → inline detail panel (full message, phone, IP, GDPR consent)
- Status update via PATCH `/ce/v1/enquiries/{id}` — no page reload
- "Export CSV" button → GET `/ce/v1/enquiries/export` (Admin only, hidden from Editor)

### 7.4 Admin Branding

- Custom colour scheme: dark sidebar (`#1a1a1a`), gold accents (`#C4973A`)
- Custom admin footer: "Crowns Estates Admin Panel — Built by R2 Design"
- Renamed menu labels: "Posts" → "Blog Posts"
- Hidden default menu items: Comments, Tools, unused defaults
- Admin sidebar order: Dashboard → Properties → Blog Posts → Testimonials → Enquiries → Users → Site Settings → Analytics (external link to GA4)

---

## 8. ACF Options Pages

Accessible from WP admin sidebar as "Site Settings". Three sub-pages.

### Site Settings → General
| Field | Type | Notes |
|---|---|---|
| WhatsApp Number | Text | International format e.g. +447700900000 |
| Admin Notification Email | Email | Receives all enquiry notifications |
| Digest Recipient Email | Email | Daily digest destination |
| Digest Enabled | True/False | Toggle on/off |
| Digest Time | Text | e.g. "08:00" UTC — triggers cron reschedule on save |
| Office Address | Textarea | Used in schema markup + contact page |

### Site Settings → Financial
| Field | Type | Notes |
|---|---|---|
| GBP → SAR Rate | Number | e.g. 4.72 |
| GBP → USD Rate | Number | e.g. 1.26 |
| Rates Last Updated | Date | Manual — admin updates when rates change |
| Registration Fee % | Number | Default: 2.5 |
| VAT % | Number | Default: 5 |
| Agency Fee % | Number | Default: 2 |

### Site Settings → Content & Legal
| Field | Type | Notes |
|---|---|---|
| From Name | Text | Email from name e.g. "Crowns Estates" |
| From Address | Email | Email from address |
| Reply-To Address | Email | |
| GTM Container ID | Text | e.g. GTM-XXXXXXX |
| GA4 Measurement ID | Text | e.g. G-XXXXXXXXXX |
| Trust Bar Text 1/2/3 | Text | Three trust signal strings |
| Footer Disclaimer | Textarea | FCA disclaimer text |
| Property Disclaimer | Textarea | Per-listing disclaimer |

---

## 9. GA4 / GTM Integration

### 9.1 GTM Snippet

GTM container ID read from ACF options. PHP outputs standard GTM `<head>` snippet and `<noscript>` body snippet via `wp_head` and `wp_body_open` hooks. If GTM container ID is empty, snippet is suppressed.

### 9.2 Server-Side dataLayer

On every page load, PHP pre-populates `window.dataLayer` with page context before GTM fires:

```js
window.dataLayer = window.dataLayer || [];
dataLayer.push({
  'page_type': 'property',       // home|projects|property|how-it-works|about|contact|blog|blog-post
  'property_id': 42,             // present on single property pages only
  'property_name': 'NEOM Residences',
  'property_city': 'NEOM',
  'property_status': 'Off-Plan'
});
```

### 9.3 Custom Events (`ga4-events.js`)

All events pushed via `dataLayer.push()`. GTM forwards to GA4 — no GA4 measurement ID in JS.

| Event Name | Trigger | Parameters |
|---|---|---|
| `enquiry_submit` | Register Interest form success | `source`, `property_id`, `property_name` |
| `contact_submit` | Contact form success | — |
| `brochure_download` | Brochure gate success | `property_id`, `property_name`, `gated: true` |
| `whatsapp_click` | WhatsApp button click | `page_type`, `property_id` (if on property page) |
| `currency_change` | Currency toggle | `from_currency`, `to_currency` |
| `calculator_use` | Calculator interaction | `property_price`, `currency` |

### 9.4 Cookie Consent Gating

GTM fires analytics tags only after Complianz/CookieYes grants analytics consent. No GA4 events fire before consent. `ce_currency` cookie is classified as functional — exempt from consent requirement.

---

## 10. Schema Markup (JSON-LD)

Output via `wp_head` hook in `inc/schema-markup.php`. Conditionally rendered per page using template conditionals.

| Schema Type | Page | Key Properties |
|---|---|---|
| `RealEstateAgent` | Sitewide (all pages) | name, url, telephone, address, areaServed |
| `RealEstateListing` | Single property | name, description, price, address, floorSize, numberOfRooms, url |
| `Review` | Pages with testimonials | author, reviewBody, reviewRating, itemReviewed |
| `FAQPage` | How It Works | mainEntity — Q&A pairs pulled from ACF FAQ repeater field |
| `Article` | Single blog post | headline, datePublished, dateModified, author, image, url |

---

## 11. SEO — Backend Responsibilities

Frontend SEO (meta titles, descriptions) is handled by Yoast SEO plugin. Backend responsibilities:

- Semantic HTML enforced in all template parts (correct heading hierarchy)
- `alt` attributes on all `<img>` outputs from ACF gallery fields
- XML sitemap: auto-generated by Yoast, includes `ce_property` CPT
- All `ce_property` and `ce_testimonial` CPTs registered with `'public' => true` and `'rewrite' => ['slug' => 'properties'/'testimonials']` so Yoast can manage their SEO
- Canonical URLs: WordPress default (no custom logic needed)
- Schema markup as above

---

## 12. Performance & Caching

| Concern | Approach |
|---|---|
| Page caching | WP Super Cache — full page caching for anonymous visitors |
| Currency endpoint | WordPress transients, 1-hour TTL, excluded from page cache |
| Admin AJAX | Excluded from page cache by WordPress default |
| DB queries | `WP_Query` with `no_found_rows => true` on listing pages where pagination is not needed |
| Asset loading | `wp_enqueue_scripts` with `defer` attribute on non-critical JS |
| Image optimisation | WP core image compression + meaningful ALT tags — no additional plugin at launch |

---

## 13. Security

| Layer | Measure |
|---|---|
| REST API forms | WordPress nonce verification on all POST endpoints |
| Input sanitisation | `sanitize_text_field`, `sanitize_email`, `wp_kses_post` on all user input |
| Output escaping | `esc_html`, `esc_attr`, `esc_url` on all template output |
| Brochure URLs | Signed tokens with 24-hour expiry — prevents direct hotlinking |
| Admin access | Client admin role restricted — cannot install plugins or edit theme/core files |
| SQL queries | All DB writes use `$wpdb->prepare()` — no raw SQL interpolation |
| File uploads | Brochure PDFs uploaded via WordPress media library — standard WP upload validation applies |

---

## 14. GDPR Compliance

- All enquiry forms include a required GDPR consent checkbox with link to Privacy Policy
- Consent status (`gdpr_consent = 1`) and timestamp stored per enquiry row
- IP address stored for audit purposes — documented in Privacy Policy
- Admin can manually delete individual enquiry rows from the admin panel
- WordPress native personal data export/erase tools available (`wp-admin/tools.php`)
- Data retention period documented in Privacy Policy (content provided by client)
- Four required legal pages: `/privacy-policy`, `/terms`, `/disclaimer`, `/cookie-policy` — content entered by admin via default `page.php` template
- Footer FCA disclaimer stored in ACF options, output on every page
- Per-property disclaimer stored in ACF options, output on single property pages

---

## 15. Plugins Required (Backend)

| Plugin | Purpose |
|---|---|
| Advanced Custom Fields (ACF) | Property/testimonial fields, site options pages |
| WP Mail SMTP | Reroutes `wp_mail()` via Brevo/Mailgun for reliable delivery |
| WP Super Cache | Full page caching |
| UpdraftPlus | Automated daily backups to cloud storage |
| Complianz or CookieYes | GDPR cookie consent — gates GTM analytics tags |
| Google Site Kit | GA4 + Search Console integration |
| Yoast SEO | Meta management, XML sitemap, Yoast schema (supplemented by custom JSON-LD) |

---

## 16. File Structure — Backend Files

```
wp-content/
├── mu-plugins/
│   └── crowns-estates-core/
│       ├── crowns-estates-core.php      # Entry point — requires all modules below
│       ├── cpt-property.php             # ce_property CPT registration
│       ├── cpt-testimonial.php          # ce_testimonial CPT registration
│       ├── taxonomy-city.php            # ce_city taxonomy
│       ├── db-table.php                 # wp_ce_enquiries table via dbDelta()
│       ├── currency-helpers.php         # ce_display_price() + /ce/v1/rates endpoint
│       ├── enquiry-handler.php          # REST endpoints: /enquiry + /brochure-gate
│       ├── enquiry-admin.php            # REST endpoints: /enquiries (list, export, patch)
│       ├── email-handler.php            # wp_mail() wrapper, cron scheduler
│       └── email-templates/
│           ├── auto-responder.php
│           ├── admin-notification.php
│           ├── brochure-delivery.php
│           └── daily-digest.php
│
└── themes/crowns-estates/
    ├── inc/
    │   ├── acf-fields-property.php      # ACF field group: property fields
    │   ├── acf-fields-testimonial.php   # ACF field group: testimonial fields
    │   ├── acf-options.php              # ACF options pages (3 sub-pages)
    │   ├── enqueue.php                  # All wp_enqueue_scripts / wp_enqueue_style
    │   ├── schema-markup.php            # JSON-LD structured data output (template-conditional)
    │   ├── admin-dashboard.php          # Custom dashboard, sidebar, branding, roles
    │   └── ga4-tracking.php             # GTM snippet + server-side dataLayer output
    └── js/
        ├── currency-toggle.js           # Client-side currency switching
        ├── modal.js                     # Modal open/close + form submission + dataLayer push
        ├── calculator.js                # Investment calculator
        ├── city-filter.js               # Projects page AJAX filter
        ├── faq-accordion.js             # FAQ expand/collapse
        ├── admin-dashboard.js           # Stat card AJAX fetch + Chart.js sparklines
        └── ga4-events.js               # Custom event dataLayer pushes
```

---

## 17. Key Decisions

1. **Must-use plugin over theme-only** — CPTs and data layer survive theme changes; cannot be accidentally deactivated by client
2. **Brevo via WP Mail SMTP** over raw `wp_mail()` — reliable delivery, DKIM/SPF, free tier sufficient for launch volumes; SMTP plugin means provider swap needs no code change
3. **Nonce-only REST security** — rate limiting and reCAPTCHA deferred to Phase 2 if spam becomes an issue
4. **Client-side currency conversion only** — WP Super Cache compatibility; no server-side cookie reads
5. **dbDelta() on mu-plugin load** — safe for repeated loads; creates table if missing, updates schema if changed, never destroys data
6. **Signed 24-hour brochure URLs** — prevents direct hotlinking without going through the gate; token stored as WordPress transient
7. **Admin role restrictions via `user_has_cap` filter** — client cannot install plugins or edit theme files; developer retains full access
8. **CSV export via REST endpoint** — streams file directly; no temp file written to disk
9. **Daily digest via WP cron** — only fires if new enquiries exist; toggle + recipient editable in ACF options without code changes
10. **GTM container ID in ACF options** — switching GTM containers or disabling analytics requires no code deployment
