# Crowns Estates WordPress Website — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a presentable WordPress website for Crowns Estates — a UK real estate agency specialising in Saudi Arabian property investment — using a custom theme with flexible, client-adjustable design tokens.

**Architecture:** Custom WordPress theme built on the Underscores (`_s`) starter. Two custom post types (`ce_property`, `ce_testimonial`), one custom taxonomy (`ce_city`), ACF for all custom fields and site options. Vanilla JS for interactive features (calculator, currency toggle, modals). No page builder, no jQuery dependency beyond WordPress core.

**Tech Stack:** WordPress 6.x, PHP 8.x, Advanced Custom Fields (ACF), vanilla JavaScript, CSS custom properties, Schema.org JSON-LD.

**Spec:** `docs/superpowers/specs/2026-03-24-crowns-estates-website-design.md`

---

## File Structure

```
crown-estates-website/
├── .gitignore
├── README.md
├── docs/superpowers/specs/...
├── docs/superpowers/plans/...
└── wp-content/themes/crowns-estates/
    ├── style.css                         # Theme header + CSS variables + full styles
    ├── functions.php                     # Theme setup, includes, menus, widget areas
    ├── header.php                        # <head>, site header, nav, currency toggle
    ├── footer.php                        # Footer, disclaimer, WhatsApp, modal, scripts
    ├── index.php                         # Fallback template
    ├── page.php                          # Default page template (legal pages, etc.)
    ├── front-page.php                    # Homepage
    ├── page-projects.php                 # Projects listing page
    ├── page-how-it-works.php             # How It Works + investment calculator
    ├── page-about.php                    # About Us
    ├── page-contact.php                  # Contact page
    ├── page-rentals.php                  # Coming Soon placeholder
    ├── single-ce_property.php            # Single property detail
    ├── archive-ce_property.php           # Property archive fallback
    ├── single.php                        # Single blog post
    ├── archive.php                       # Blog archive
    ├── sidebar.php                       # Blog sidebar
    ├── 404.php                           # Branded 404 page
    ├── screenshot.png                    # Theme preview (1200x900)
    ├── inc/
    │   ├── cpt-property.php              # ce_property CPT registration
    │   ├── cpt-testimonial.php           # ce_testimonial CPT registration
    │   ├── taxonomy-city.php             # ce_city taxonomy
    │   ├── acf-fields-property.php       # ACF field group: Property fields
    │   ├── acf-fields-testimonial.php    # ACF field group: Testimonial fields
    │   ├── acf-options.php               # ACF options page (rates, WhatsApp, etc.)
    │   ├── enqueue.php                   # Script and style enqueuing
    │   ├── currency-helpers.php          # Multi-currency conversion + formatting
    │   ├── enquiry-handler.php           # Form submission → DB + email + auto-reply
    │   ├── schema-markup.php             # JSON-LD structured data output
    │   └── admin-dashboard.php           # Custom admin dashboard, sidebar, branding
    ├── template-parts/
    │   ├── property-card.php             # Property listing card
    │   ├── testimonial-card.php          # Testimonial display card
    │   ├── hero.php                      # Hero section (reusable)
    │   ├── trust-bar.php                 # Trust signals bar
    │   ├── cta-banner.php                # CTA banner section
    │   ├── modal-register-interest.php   # Register Interest modal overlay
    │   └── whatsapp-button.php           # Sticky WhatsApp FAB
    ├── js/
    │   ├── calculator.js                 # Investment cost calculator
    │   ├── currency-toggle.js            # Front-end currency switching
    │   ├── modal.js                      # Register Interest modal open/close
    │   ├── city-filter.js                # Projects page AJAX/JS city filter
    │   ├── faq-accordion.js              # FAQ expand/collapse
    │   └── admin-dashboard.js            # Admin dashboard charts (Chart.js)
    ├── img/
    │   └── placeholder-property.jpg      # Fallback property image
    └── sample-content/                   # Sample blog posts for WordPress import
        ├── 5-things-uk-investors.md
        ├── golden-visa-guide.md
        ├── neom-investment-guide.md
        ├── understanding-off-plan.md
        └── riyadh-vs-jeddah.md
```

---

## Task 1: Project Scaffold & Git Setup

**Files:**
- Create: `.gitignore`
- Create: `README.md`
- Create: `wp-content/themes/crowns-estates/style.css` (header only)
- Create: `wp-content/themes/crowns-estates/index.php` (minimal)

- [ ] **Step 1: Create `.gitignore`**

```gitignore
# WordPress core (not tracked — installed on server)
wp-admin/
wp-includes/
wp-*.php
xmlrpc.php
license.txt
readme.html

# Uploads (media managed on server)
wp-content/uploads/

# Plugins (installed via admin, not tracked here)
wp-content/plugins/

# Other themes
wp-content/themes/twenty*/

# Environment
.env
.htaccess
wp-config.php

# OS
.DS_Store
Thumbs.db

# IDE
.vscode/
.idea/

# Node (if added later)
node_modules/
```

- [ ] **Step 2: Create `README.md`**

```markdown
# Crowns Estates Website

Custom WordPress theme for [Crowns Estates](https://www.crownsestates.co.uk) — a UK-registered real estate agency specialising in Saudi Arabian property investment.

## Structure

- `wp-content/themes/crowns-estates/` — Custom theme
- `docs/` — Design specs and implementation plans

## Setup

1. Install WordPress on your server
2. Clone this repo into the WordPress root (or symlink the theme)
3. Install required plugins: ACF, Contact Form 7, Yoast SEO, WP Super Cache, UpdraftPlus, Complianz
4. Activate the "Crowns Estates" theme
5. Import ACF field groups (auto-registered via theme)
6. Create required WordPress pages with these exact slugs:
   - `projects`, `how-it-works`, `about`, `contact`, `rentals`, `blog`
   - `privacy-policy`, `terms`, `disclaimer`, `cookie-policy`
7. Set "Home" page as the static front page in Settings > Reading

## Requirements

- WordPress 6.x
- PHP 8.0+
- Advanced Custom Fields (ACF) plugin
```

- [ ] **Step 3: Create minimal `style.css` with theme header**

```css
/*
Theme Name: Crowns Estates
Theme URI: https://www.crownsestates.co.uk
Author: JAL Development
Description: Custom theme for Crowns Estates — Saudi Arabian property investment agency.
Version: 1.0.0
Requires at least: 6.0
Requires PHP: 8.0
License: Private
Text Domain: crowns-estates
*/
```

- [ ] **Step 4: Create minimal `index.php`**

```php
<?php get_header(); ?>
<main class="ce-main">
    <p>Crowns Estates theme is active.</p>
</main>
<?php get_footer(); ?>
```

- [ ] **Step 5: Initial commit**

```bash
git add .gitignore README.md docs/ wp-content/themes/crowns-estates/style.css wp-content/themes/crowns-estates/index.php
git commit -m "feat: project scaffold with theme boilerplate and spec docs"
```

---

## Task 2: Design System — CSS Variables & Base Styles

**Files:**
- Modify: `wp-content/themes/crowns-estates/style.css`

- [ ] **Step 1: Add CSS custom properties and base styles to `style.css`**

After the theme header comment, add all design tokens as CSS variables plus base reset, typography, layout utilities, and component styles. Key tokens:

```css
:root {
    --ce-white: #FFFFFF;
    --ce-black: #0A0A0A;
    --ce-gold: #C4973A;
    --ce-gold-light: #D4AF5C;
    --ce-gold-dark: #A37E2C;
    --ce-grey-light: #F5F5F5;
    --ce-grey-mid: #E0E0E0;
    --ce-grey-dark: #666666;

    --ce-font-heading: 'Playfair Display', Georgia, serif;
    --ce-font-body: 'Inter', -apple-system, sans-serif;

    --ce-space-xs: 4px;
    --ce-space-sm: 8px;
    --ce-space-md: 16px;
    --ce-space-lg: 32px;
    --ce-space-xl: 64px;
    --ce-space-2xl: 128px;

    --ce-max-width: 1200px;
    --ce-border-radius: 4px;
    --ce-transition: 0.2s ease;
}
```

Include styles for:
- Reset / box-sizing
- Typography (h1–h6, p, a, lists)
- Layout utilities (`.ce-container`, `.ce-grid`, `.ce-grid-2`, `.ce-grid-3`)
- Buttons (`.ce-btn`, `.ce-btn--gold`, `.ce-btn--outline`)
- Cards (`.ce-card`)
- Forms (`.ce-form`, `.ce-form__input`, `.ce-form__label`)
- Section spacing (`.ce-section`)
- Responsive breakpoints via media queries (mobile-first: 768px, 1024px)

- [ ] **Step 2: Commit**

```bash
git add wp-content/themes/crowns-estates/style.css
git commit -m "feat: design system with CSS variables, typography, layout utilities"
```

---

## Task 3: Theme Core — functions.php, Header, Footer

**Files:**
- Create: `wp-content/themes/crowns-estates/functions.php`
- Create: `wp-content/themes/crowns-estates/inc/enqueue.php`
- Create: `wp-content/themes/crowns-estates/header.php`
- Create: `wp-content/themes/crowns-estates/footer.php`
- Create: `wp-content/themes/crowns-estates/page.php`

- [ ] **Step 1: Create `functions.php`**

Theme setup function registering:
- `after_setup_theme`: title tag support, post thumbnails, HTML5 support, custom logo, nav menus (`primary`, `footer`)
- Include all files from `inc/` directory
- Register widget area: `blog-sidebar`

```php
<?php
// Theme setup
function ce_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption']);
    add_theme_support('custom-logo');
    register_nav_menus([
        'primary' => __('Primary Menu', 'crowns-estates'),
        'footer'  => __('Footer Menu', 'crowns-estates'),
    ]);
}
add_action('after_setup_theme', 'ce_theme_setup');

// Widget areas
function ce_widgets_init() {
    register_sidebar([
        'name'          => __('Blog Sidebar', 'crowns-estates'),
        'id'            => 'blog-sidebar',
        'before_widget' => '<div class="ce-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="ce-widget__title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'ce_widgets_init');

// Includes
require get_template_directory() . '/inc/enqueue.php';
// Further includes added as files are created
```

- [ ] **Step 2: Create `inc/enqueue.php`**

Enqueue Google Fonts (Playfair Display + Inter), theme stylesheet, and JS files.

```php
<?php
function ce_enqueue_assets() {
    // Google Fonts
    wp_enqueue_style('ce-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@400;600;700&display=swap',
        [], null
    );

    // Theme stylesheet
    wp_enqueue_style('ce-style', get_stylesheet_uri(), ['ce-google-fonts'], wp_get_theme()->get('Version'));

    // JS files — enqueued as they are created
}
add_action('wp_enqueue_scripts', 'ce_enqueue_assets');
```

- [ ] **Step 3: Create `header.php`**

Standard WordPress header with:
- `<!DOCTYPE html>`, `<html <?php language_attributes(); ?>>`, `<head>` with `wp_head()`
- Site header bar: logo (left), primary nav menu (centre), currency toggle (right)
- Mobile hamburger menu toggle
- Body class: `<?php body_class(); ?>`

- [ ] **Step 4: Create `footer.php`**

Footer with:
- 4-column grid: About snippet, Quick Links, Contact Info, Legal Links
- Regulatory disclaimer line (from spec section 10)
- Copyright line
- `wp_footer()` hook
- Include for WhatsApp button and Register Interest modal template parts

- [ ] **Step 5: Create `page.php`**

Default page template for legal pages (Privacy Policy, Terms, Disclaimer, Cookie Policy) and any other standard pages. Layout:
- Page title via `the_title()`
- Content via `the_content()`
- Wrapped in `.ce-container` with standard section padding
- No sidebar

```php
<?php get_header(); ?>
<main class="ce-main">
    <section class="ce-section">
        <div class="ce-container">
            <h1 class="ce-page-title"><?php the_title(); ?></h1>
            <div class="ce-page-content">
                <?php the_content(); ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
```

- [ ] **Step 6: Commit**

```bash
git add wp-content/themes/crowns-estates/functions.php wp-content/themes/crowns-estates/inc/enqueue.php wp-content/themes/crowns-estates/header.php wp-content/themes/crowns-estates/footer.php wp-content/themes/crowns-estates/page.php
git commit -m "feat: theme core — functions.php, header, footer, page template, asset enqueue"
```

---

## Task 4: Custom Post Types & Taxonomy

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/cpt-property.php`
- Create: `wp-content/themes/crowns-estates/inc/cpt-testimonial.php`
- Create: `wp-content/themes/crowns-estates/inc/taxonomy-city.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (add includes)

- [ ] **Step 1: Create `inc/cpt-property.php`**

Register `ce_property` CPT with:
- Labels: "Properties" / "Property"
- `public => true`, `has_archive => true`, `rewrite => ['slug' => 'properties']`
- Supports: title, editor, thumbnail
- Menu icon: `dashicons-building`
- `show_in_rest => true` for Gutenberg compatibility

- [ ] **Step 2: Create `inc/cpt-testimonial.php`**

Register `ce_testimonial` CPT with:
- Labels: "Testimonials" / "Testimonial"
- `public => true`, `has_archive => false`
- Supports: title
- Menu icon: `dashicons-format-quote`

- [ ] **Step 3: Create `inc/taxonomy-city.php`**

Register `ce_city` taxonomy attached to both `ce_property` and `ce_testimonial` with:
- Labels: "Cities" / "City"
- `hierarchical => true` (behaves like categories)
- `rewrite => ['slug' => 'city']`
- `show_in_rest => true`

- [ ] **Step 4: Add includes to `functions.php`**

Add after existing includes:
```php
require get_template_directory() . '/inc/cpt-property.php';
require get_template_directory() . '/inc/cpt-testimonial.php';
require get_template_directory() . '/inc/taxonomy-city.php';
```

- [ ] **Step 5: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/cpt-property.php wp-content/themes/crowns-estates/inc/cpt-testimonial.php wp-content/themes/crowns-estates/inc/taxonomy-city.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: register ce_property, ce_testimonial CPTs and ce_city taxonomy"
```

---

## Task 5: ACF Field Groups & Options Pages

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/acf-fields-property.php`
- Create: `wp-content/themes/crowns-estates/inc/acf-fields-testimonial.php`
- Create: `wp-content/themes/crowns-estates/inc/acf-options.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (add includes)

- [ ] **Step 1: Create `inc/acf-fields-property.php`**

Register ACF field group "Property Details" using `acf_add_local_field_group()`. Location rule: post type == `ce_property`. Fields as per spec section 5:

- `ce_developer` (text)
- `ce_price_from` (number)
- `ce_currency` (select: SAR / GBP / USD)
- `ce_property_type` (select: Apartment / Villa / Commercial)
- `ce_status` (select: Off-Plan / Under Construction / Ready)
- `ce_completion_date` (date_picker)
- `ce_bedrooms` (number)
- `ce_size_sqm` (number)
- `ce_is_freehold` (true_false)
- `ce_short_description` (textarea)
- `ce_full_description` (wysiwyg)
- `ce_gallery` (gallery)
- `ce_brochure_pdf` (file)
- `ce_featured` (true_false)
- `ce_map_embed` (text)

- [ ] **Step 2: Create `inc/acf-fields-testimonial.php`**

Register ACF field group "Testimonial Details" on `ce_testimonial`:

- `ce_client_name` (text)
- `ce_client_location` (text)
- `ce_quote` (textarea)
- `ce_rating` (number, min 1, max 5)
- `ce_google_review_link` (url)
- `ce_testimonial_city` (taxonomy: ce_city — links testimonial to a city for "related testimonials" on property pages)
- `ce_testimonial_featured` (true_false)
- `ce_testimonial_date` (date_picker)

- [ ] **Step 3: Create `inc/acf-options.php`**

Register ACF options page "Site Settings" with sub-pages:
- **Exchange Rates:** `ce_rate_gbp_to_sar`, `ce_rate_usd_to_sar`, `ce_rate_gbp_to_usd`
- **Calculator Rates:** `ce_calc_registration_fee` (default 2.5), `ce_calc_vat` (default 5), `ce_calc_agency_fee` (default 2)
- **WhatsApp:** `ce_whatsapp_number`
- **Contact Email:** `ce_contact_email` (default: info@crownsestates.co.uk)

```php
<?php
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'Site Settings',
        'menu_title' => 'Site Settings',
        'menu_slug'  => 'ce-site-settings',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-admin-generic',
    ]);
    acf_add_options_sub_page([
        'page_title'  => 'Exchange Rates',
        'menu_title'  => 'Exchange Rates',
        'parent_slug' => 'ce-site-settings',
    ]);
    acf_add_options_sub_page([
        'page_title'  => 'Calculator Settings',
        'menu_title'  => 'Calculator',
        'parent_slug' => 'ce-site-settings',
    ]);
}
```

- [ ] **Step 4: Add includes to `functions.php`**

```php
require get_template_directory() . '/inc/acf-fields-property.php';
require get_template_directory() . '/inc/acf-fields-testimonial.php';
require get_template_directory() . '/inc/acf-options.php';
```

- [ ] **Step 5: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/acf-fields-property.php wp-content/themes/crowns-estates/inc/acf-fields-testimonial.php wp-content/themes/crowns-estates/inc/acf-options.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: ACF field groups for properties, testimonials, and site options"
```

---

## Task 6: Multi-Currency Helper Functions

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/currency-helpers.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (add include)

- [ ] **Step 1: Create `inc/currency-helpers.php`**

Helper functions:

**Important cache-compatibility note:** PHP must NOT read cookies for currency display. Server-side rendering always outputs the listing's native currency. Currency conversion is handled entirely client-side by `currency-toggle.js` which reads a cookie and recalculates `data-*` attributes. This ensures compatibility with WP Super Cache.

```php
<?php
/**
 * Get exchange rates from ACF options.
 */
function ce_get_exchange_rates(): array {
    return [
        'GBP_SAR' => (float) get_field('ce_rate_gbp_to_sar', 'option') ?: 4.68,
        'USD_SAR'  => (float) get_field('ce_rate_usd_to_sar', 'option') ?: 3.75,
        'GBP_USD'  => (float) get_field('ce_rate_gbp_to_usd', 'option') ?: 1.27,
    ];
}

/**
 * Format a price with currency symbol.
 */
function ce_format_price(float $amount, string $currency): string {
    $symbols = ['GBP' => '£', 'SAR' => 'SAR ', 'USD' => '$'];
    $symbol = $symbols[$currency] ?? $currency . ' ';
    return $symbol . number_format($amount, 0);
}

/**
 * Display a property price in its NATIVE currency.
 * Outputs data attributes for JS client-side currency conversion.
 * This is cache-safe — no cookies read server-side.
 */
function ce_display_price(int $post_id = 0): string {
    $post_id = $post_id ?: get_the_ID();
    $price = (float) get_field('ce_price_from', $post_id);
    $native = get_field('ce_currency', $post_id) ?: 'SAR';

    if (!$price) return '<span class="ce-price" data-price="0" data-currency="">Price on request</span>';

    $formatted = ce_format_price($price, $native);
    return sprintf(
        '<span class="ce-price" data-price="%s" data-currency="%s">%s</span>',
        esc_attr($price),
        esc_attr($native),
        esc_html($formatted)
    );
}
```

- [ ] **Step 2: Add REST endpoint for currency rates (for JS toggle)**

Add to the same file. The JS `currency-toggle.js` fetches these rates and does all conversion client-side:

```php
function ce_rest_exchange_rates() {
    register_rest_route('ce/v1', '/rates', [
        'methods'  => 'GET',
        'callback' => function () {
            return ce_get_exchange_rates();
        },
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'ce_rest_exchange_rates');
```

- [ ] **Step 3: Add include to `functions.php`**

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/currency-helpers.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: multi-currency helper functions and REST endpoint"
```

---

## Task 7: Template Parts — Reusable Components

**Files:**
- Create: `wp-content/themes/crowns-estates/template-parts/property-card.php`
- Create: `wp-content/themes/crowns-estates/template-parts/testimonial-card.php`
- Create: `wp-content/themes/crowns-estates/template-parts/hero.php`
- Create: `wp-content/themes/crowns-estates/template-parts/trust-bar.php`
- Create: `wp-content/themes/crowns-estates/template-parts/cta-banner.php`
- Create: `wp-content/themes/crowns-estates/template-parts/modal-register-interest.php`
- Create: `wp-content/themes/crowns-estates/template-parts/whatsapp-button.php`

- [ ] **Step 1: Create `template-parts/property-card.php`**

Card displaying: thumbnail (with fallback placeholder), city badge, title, property type, status pill, price (via `ce_display_price()`), bedrooms, size, CTA button. All wrapped in `.ce-property-card` with `data-price`, `data-currency` attributes for JS currency switching.

- [ ] **Step 2: Create `template-parts/testimonial-card.php`**

Card displaying: star rating (1-5, filled/empty), quote text, client name, location, link to Google review. Schema.org `Review` microdata attributes included.

- [ ] **Step 3: Create `template-parts/hero.php`**

Full-width hero section accepting args via `set_query_var()`:
- `$args['title']`, `$args['subtitle']`, `$args['cta_text']`, `$args['cta_url']`
- Background image via CSS (customisable per page)
- Dark overlay for text readability

- [ ] **Step 4: Create `template-parts/trust-bar.php`**

Horizontal bar with 3 items: "20 Years in Saudi Arabia", "British Expat Expertise", "End-to-End Investor Support". Gold accent borders.

- [ ] **Step 5: Create `template-parts/cta-banner.php`**

Full-width gold-accented banner with heading, subtext, and CTA button that triggers the Register Interest modal.

- [ ] **Step 6: Create `template-parts/modal-register-interest.php`**

Hidden modal overlay with form fields: Name, Email, Phone, Property of Interest (optional select), Message, GDPR consent checkbox. Form submits via `fetch()` POST to a custom REST endpoint (`/wp-json/ce/v1/enquiry` — created in Task 8). Success/error states handled in JS. During development, the form will not work until Task 8 is complete.

- [ ] **Step 7: Create `template-parts/whatsapp-button.php`**

Fixed-position floating button (bottom-right). Links to `https://wa.me/{number}` where number is pulled from ACF options `ce_whatsapp_number`. Includes WhatsApp SVG icon. Hidden if no number is set.

- [ ] **Step 8: Commit**

```bash
git add wp-content/themes/crowns-estates/template-parts/
git commit -m "feat: reusable template parts — cards, hero, trust bar, CTA, modal, WhatsApp"
```

---

## Task 8: Enquiry Handler — Form Submission, DB Storage, Auto-Reply

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/enquiry-handler.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (add include)

- [ ] **Step 1: Create custom database table on theme switch**

Use `after_switch_theme` action (not `register_activation_hook` — that only works for plugins). Additionally, add an `init` check using a version option so the table is created/updated even if the theme is already active. Creates `{prefix}ce_enquiries` table with columns: `id`, `name`, `email`, `phone`, `property_interest`, `message`, `gdpr_consent`, `source_url`, `created_at`.

```php
function ce_create_enquiries_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_enquiries';
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        property_interest VARCHAR(255),
        message TEXT,
        gdpr_consent TINYINT(1) NOT NULL DEFAULT 0,
        source_url VARCHAR(500),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('ce_enquiries_db_version', '1.0');
}
add_action('after_switch_theme', 'ce_create_enquiries_table');
// Also check on init for existing installs
add_action('init', function() {
    if (get_option('ce_enquiries_db_version') !== '1.0') {
        ce_create_enquiries_table();
    }
});
```

- [ ] **Step 2: Create REST endpoint for form submission**

`POST /wp-json/ce/v1/enquiry` — validates input, sanitises, stores in DB table, sends email to admin via `wp_mail()`, sends auto-responder email to enquirer, returns JSON success/error.

Input validation:
- `name`: required, sanitize_text_field
- `email`: required, is_email()
- `phone`: optional, sanitize_text_field
- `message`: optional, sanitize_textarea_field
- `gdpr_consent`: required, must be `true`

Auto-responder email (plain text):
```
Subject: Thank you for your enquiry — Crowns Estates

Dear {name},

Thank you for your interest in investing with Crowns Estates. Our team will be in touch within 24 hours.

In the meantime, explore our latest opportunities at https://www.crownsestates.co.uk/projects

Best regards,
The Crowns Estates Team
```

- [ ] **Step 3: Add admin page to view enquiries**

Register a submenu page under "Site Settings" that displays a WP_List_Table of all enquiries (name, email, phone, property, date). Exportable to CSV.

- [ ] **Step 4: Add include to `functions.php`**

- [ ] **Step 5: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/enquiry-handler.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: enquiry handler — REST endpoint, DB storage, auto-responder, admin view"
```

---

## Task 9: JavaScript — Calculator, Currency Toggle, Modal, Filters, FAQ

**Files:**
- Create: `wp-content/themes/crowns-estates/js/calculator.js`
- Create: `wp-content/themes/crowns-estates/js/currency-toggle.js`
- Create: `wp-content/themes/crowns-estates/js/modal.js`
- Create: `wp-content/themes/crowns-estates/js/city-filter.js`
- Create: `wp-content/themes/crowns-estates/js/faq-accordion.js`
- Modify: `wp-content/themes/crowns-estates/inc/enqueue.php` (register scripts)

- [ ] **Step 1: Create `js/calculator.js`**

Vanilla JS. Reads rates from `ceData.calculatorRates` (localised via `wp_localize_script`). On input change, calculates registration fee, VAT, agency fee, total. Updates DOM instantly. Includes disclaimer text. "Speak to Our Team" CTA button opens Register Interest modal.

- [ ] **Step 2: Create `js/currency-toggle.js`**

Reads current currency from cookie `ce_currency`. On toggle click, sets cookie, fetches rates from `/wp-json/ce/v1/rates`, recalculates all `[data-price]` elements on the page without reload.

- [ ] **Step 3: Create `js/modal.js`**

Opens/closes Register Interest modal. Handles form submission via `fetch()` to `/wp-json/ce/v1/enquiry`. Shows loading state, success message, or validation errors. Closes on overlay click or Escape key.

- [ ] **Step 4: Create `js/city-filter.js`**

On Projects page: clicking a city tab filters the `.ce-property-card` elements. Uses CSS classes to show/hide — no page reload. "All" tab shows all. Active tab gets gold underline.

- [ ] **Step 5: Create `js/faq-accordion.js`**

Click to expand/collapse FAQ items. Only one open at a time. Uses `aria-expanded` for accessibility.

- [ ] **Step 6: Update `inc/enqueue.php`**

Conditionally enqueue scripts per page:
- `calculator.js` → only on How It Works page
- `currency-toggle.js` → all pages
- `modal.js` → all pages
- `city-filter.js` → only on Projects page
- `faq-accordion.js` → only on How It Works page

Use `wp_localize_script` to pass `ceData` object with REST URL, nonce, calculator rates, exchange rates.

**Note:** All JS files must use defensive checks (early-return if target DOM elements are absent) since scripts may load on pages where their elements don't exist yet during incremental development.

- [ ] **Step 7: Commit**

```bash
git add wp-content/themes/crowns-estates/js/ wp-content/themes/crowns-estates/inc/enqueue.php
git commit -m "feat: JS modules — calculator, currency toggle, modal, city filter, FAQ accordion"
```

---

## Task 10: Homepage — front-page.php

**Files:**
- Create: `wp-content/themes/crowns-estates/front-page.php`

- [ ] **Step 1: Create `front-page.php`**

Sections in order:
1. Hero (via `get_template_part('template-parts/hero')`) — title: "Connecting Investors with Quality Property Opportunities", CTA: "View Opportunities" → /projects
2. Trust bar (via `get_template_part('template-parts/trust-bar')`)
3. Featured Properties — WP_Query for `ce_property` where `ce_featured == true`, limit 3, display via `get_template_part('template-parts/property-card')`. "View All Properties" link.
4. "Why Invest in Saudi Arabia" — 3-column grid with icon placeholders: Market Growth, Golden Visa, Freehold Zones. Brief text per block.
5. Testimonials — WP_Query for `ce_testimonial` where `ce_testimonial_featured == true`, limit 3, display as a static 3-column grid via `get_template_part('template-parts/testimonial-card')` (no carousel JS — keep it simple and cache-friendly)
6. About snippet — short paragraph + "Learn More" link to /about
7. CTA banner (via `get_template_part('template-parts/cta-banner')`)

- [ ] **Step 2: Commit**

```bash
git add wp-content/themes/crowns-estates/front-page.php
git commit -m "feat: homepage with hero, featured properties, testimonials, trust signals"
```

---

## Task 11: Projects Page & Single Property

**Files:**
- Create: `wp-content/themes/crowns-estates/page-projects.php`
- Create: `wp-content/themes/crowns-estates/single-ce_property.php`
- Create: `wp-content/themes/crowns-estates/archive-ce_property.php`

- [ ] **Step 1: Create `page-projects.php`**

- Page header with title + intro text
- City filter tabs: loop `get_terms('ce_city')`, output as filter buttons + "All" default
- Property grid: WP_Query all `ce_property`, ordered by date. Each card via `get_template_part('template-parts/property-card')` with `data-city` attribute for JS filtering
- Currency toggle visible in page header

- [ ] **Step 2: Create `single-ce_property.php`**

Single property page layout:
- Full-width image gallery (first image hero, thumbnails below)
- Property info grid: all ACF fields displayed in clean two-column table
- Price displayed via `ce_display_price()` with currency toggle
- Status badge (colour-coded: Off-Plan = blue, Under Construction = amber, Ready = green)
- Freehold badge
- Download brochure button (if PDF uploaded)
- Google Maps embed (if `ce_map_embed` set)
- Related testimonials: query `ce_testimonial` filtered by matching city taxonomy
- Inline Register Interest form (same fields as modal)
- Property disclaimer text from spec section 10

- [ ] **Step 3: Create `archive-ce_property.php`**

Fallback archive — redirects to Projects page or mirrors its layout.

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/page-projects.php wp-content/themes/crowns-estates/single-ce_property.php wp-content/themes/crowns-estates/archive-ce_property.php
git commit -m "feat: Projects listing page and single property detail page"
```

---

## Task 12: How It Works Page + Investment Calculator

**Files:**
- Create: `wp-content/themes/crowns-estates/page-how-it-works.php`

- [ ] **Step 1: Create `page-how-it-works.php`**

Sections:
1. Hero — title: "How It Works", subtitle: "Your step-by-step guide to investing in Saudi property"
2. 5-step investor journey — numbered sections with icons, heading, description per step
3. Investment Calculator card — inputs: price, currency toggle, property type. Outputs: fee breakdown table. Disclaimer text. CTA button opens modal.
4. Saudi Premium Residency (Golden Visa) explainer — key points, eligibility, benefits
5. FAQ accordion — 6-8 common questions (placeholder content)
6. AI Agent placeholder section: "Need instant answers? Our AI assistant is coming soon." (hidden by default, ready for Phase 2)
7. CTA banner → Contact page

- [ ] **Step 2: Commit**

```bash
git add wp-content/themes/crowns-estates/page-how-it-works.php
git commit -m "feat: How It Works page with investment calculator and FAQ"
```

---

## Task 13: About Us, Contact, Rentals Pages

**Files:**
- Create: `wp-content/themes/crowns-estates/page-about.php`
- Create: `wp-content/themes/crowns-estates/page-contact.php`
- Create: `wp-content/themes/crowns-estates/page-rentals.php`

- [ ] **Step 1: Create `page-about.php`**

- Hero with "About Crowns Estates"
- Two-column layout: text left (team intro, 20 years story, values), image placeholder right
- Core values section: 3 blocks — Trust, Local Knowledge, Investor-First
- Stats/timeline bar (placeholder: "200+ Properties Sourced", "20 Years in KSA", "50+ Happy Investors")
- CTA banner

- [ ] **Step 2: Create `page-contact.php`**

- Hero with "Get in Touch"
- Two-column layout: enquiry form (left), contact info + WhatsApp link + social links placeholders (right)
- Form fields: Name, Email, Phone, Message, GDPR checkbox. Submits to same REST endpoint as modal.
- Google Maps embed placeholder
- AI Agent placeholder section (commented out, ready for Phase 2 integration)

- [ ] **Step 3: Create `page-rentals.php`**

- Minimal "Coming Soon" page
- Heading: "Rental Properties — Coming Soon"
- Brief paragraph about upcoming rental services
- Email capture form (name + email only) for notifications
- CTA: "Browse Investment Properties" → /projects

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/page-about.php wp-content/themes/crowns-estates/page-contact.php wp-content/themes/crowns-estates/page-rentals.php
git commit -m "feat: About Us, Contact, and Rentals (Coming Soon) pages"
```

---

## Task 14: Blog Templates

**Files:**
- Create: `wp-content/themes/crowns-estates/archive.php`
- Create: `wp-content/themes/crowns-estates/single.php`
- Create: `wp-content/themes/crowns-estates/sidebar.php`

- [ ] **Step 1: Create `archive.php`**

Blog listing page:
- Page header: "Insights & Guides"
- Two-column layout: main content (left, 2/3 width) + sidebar (right, 1/3 width)
- Post cards in grid: featured image, title, excerpt, date, category badge, "Read More" link
- WordPress pagination

- [ ] **Step 2: Create `single.php`**

Single blog post:
- Two-column layout matching archive
- Post header: title, date, category, estimated read time
- Post content: `the_content()` with styled typography
- Author/team attribution
- Related posts (same category, limit 3)
- CTA banner at bottom

- [ ] **Step 3: Create `sidebar.php`**

- Search box
- Categories list
- Recent posts (5)
- Register Interest CTA card (gold-bordered card with "Ready to invest?" + button)

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/archive.php wp-content/themes/crowns-estates/single.php wp-content/themes/crowns-estates/sidebar.php
git commit -m "feat: blog archive, single post, and sidebar templates"
```

---

## Task 15: Schema Markup & SEO

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/schema-markup.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (add include)

- [ ] **Step 1: Create `inc/schema-markup.php`**

Output JSON-LD in `<head>` via `wp_head` action:

- **On single property pages:** `RealEstateListing` schema with name, description, price, currency, location, image, datePosted
- **On testimonial displays:** `Review` schema with author, reviewBody, ratingValue, itemReviewed (Organization: Crowns Estates)
- **Sitewide:** `RealEstateAgent` Organization schema with name, url, logo, contact info

- [ ] **Step 2: Add include to `functions.php`**

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/schema-markup.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: Schema.org JSON-LD markup for properties, reviews, and organization"
```

---

## Task 16: Legal Pages & 404

**Files:**
- Create: `wp-content/themes/crowns-estates/404.php`

- [ ] **Step 1: Create `404.php`**

Branded 404 page:
- Heading: "Page Not Found"
- Subtext: "The page you're looking for doesn't exist or has been moved."
- Two CTA buttons: "Browse Properties" → /projects, "Contact Us" → /contact
- Search form

- [ ] **Step 2: Verify legal elements**

Ensure footer.php includes:
- Regulatory disclaimer line
- Links to: Privacy Policy, Terms, Disclaimer, Cookie Policy
- These pages will use default `page.php` (WordPress page template) — content entered by admin via the editor

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/404.php
git commit -m "feat: branded 404 page and legal footer links"
```

---

## Task 17: Sample Content & Placeholder Images

**Files:**
- Create: `wp-content/themes/crowns-estates/img/placeholder-property.jpg`
- Create: `wp-content/themes/crowns-estates/sample-content/sample-posts.xml` (optional WXR import)

- [ ] **Step 1: Create placeholder image**

Generate or source a minimal 1200x800 placeholder image for property cards (solid grey with "Crowns Estates" watermark, or a simple geometric pattern in the brand colours).

- [ ] **Step 2: Create `screenshot.png`**

Generate a 1200x900 theme preview image. Can be a simple branded placeholder with the Crowns Estates name, gold/black/white colour scheme.

- [ ] **Step 3: Document blog categories to create**

Add a `sample-content/setup-notes.md` file listing the WordPress categories to create in admin:
- Market Updates
- Investment Guides
- Area Profiles
- News

- [ ] **Step 4: Create sample blog post content**

Write 5 sample blog posts as markdown files under `sample-content/` for reference during WordPress setup:
1. `5-things-uk-investors.md`
2. `golden-visa-guide.md`
3. `neom-investment-guide.md`
4. `understanding-off-plan.md`
5. `riyadh-vs-jeddah.md`

Each post: ~300-500 words of placeholder content covering the topic. These will be manually entered into WordPress or imported.

- [ ] **Step 5: Commit**

```bash
git add wp-content/themes/crowns-estates/img/ wp-content/themes/crowns-estates/sample-content/ wp-content/themes/crowns-estates/screenshot.png
git commit -m "feat: placeholder images, screenshot, and sample blog post content"
```

---

## Task 18: Final Integration & Push to GitHub

**Files:**
- All files created in previous tasks

- [ ] **Step 1: Review all includes in `functions.php`**

Verify all `require` statements are present and in correct order:
```php
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/cpt-property.php';
require get_template_directory() . '/inc/cpt-testimonial.php';
require get_template_directory() . '/inc/taxonomy-city.php';
require get_template_directory() . '/inc/acf-fields-property.php';
require get_template_directory() . '/inc/acf-fields-testimonial.php';
require get_template_directory() . '/inc/acf-options.php';
require get_template_directory() . '/inc/currency-helpers.php';
require get_template_directory() . '/inc/enquiry-handler.php';
require get_template_directory() . '/inc/schema-markup.php';
```

- [ ] **Step 2: Verify all template parts are referenced correctly**

Check that `get_template_part()` calls in all page templates match the file paths in `template-parts/`.

- [ ] **Step 3: Verify footer includes WhatsApp button and modal**

```php
<?php get_template_part('template-parts/whatsapp-button'); ?>
<?php get_template_part('template-parts/modal-register-interest'); ?>
<?php wp_footer(); ?>
```

- [ ] **Step 4: Push to GitHub**

```bash
git push -u origin main
```

- [ ] **Step 5: Verify on GitHub**

Check that `https://github.com/jalookout7-eng/crown-estates-website` contains all files and the README renders correctly.

---

## Task 19: Custom Admin Dashboard & Branded Backend

**Context:** The client expects a clean, branded admin panel (similar to the Filament/Laravel dashboard in the griyakita reference screenshots). This task customises the WordPress admin to deliver that experience — branded dashboard with stat cards, clean sidebar navigation, and property/blog management views.

**Reference:** 7 screenshots saved in project root (`Screenshot 2026-03-25 14465*.png`) showing: Dashboard with stat cards + sparklines, Visitor Analytics, Posts management, Categories table, Newsletter Subscribers, Properties list, and Visitor Analytics detail.

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/admin-dashboard.php`
- Create: `wp-content/themes/crowns-estates/js/admin-dashboard.js`
- Modify: `wp-content/themes/crowns-estates/style.css` (add admin styles)
- Modify: `wp-content/themes/crowns-estates/functions.php` (add include)
- Modify: `wp-content/themes/crowns-estates/inc/enqueue.php` (admin scripts)

- [ ] **Step 1: Create `inc/admin-dashboard.php` — Custom dashboard page**

Replace the default WordPress dashboard with a branded Crowns Estates dashboard. Register a custom admin page as the default landing page after login.

Dashboard stat cards (matching griyakita layout):
- **Row 1:** Total Properties, Active Properties, Sold/Rented, Pending Review (with sparkline trends)
- **Row 2:** Tour Requests (pending count), Total Users, Total Property Value (formatted)
- **Row 3:** Total Visitors, Today's Visitors (% change), Unique Visitors
- **Row 4:** This Week visitors, This Month visitors

```php
<?php
// Remove default WordPress dashboard widgets
function ce_remove_dashboard_widgets() {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'ce_remove_dashboard_widgets');

// Add custom dashboard widget
function ce_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'ce_dashboard_overview',
        'Crowns Estates Overview',
        'ce_dashboard_overview_render'
    );
}
add_action('wp_dashboard_setup', 'ce_add_dashboard_widgets');

function ce_dashboard_overview_render() {
    $total_properties = wp_count_posts('ce_property')->publish ?? 0;
    $total_enquiries = $GLOBALS['wpdb']->get_var("SELECT COUNT(*) FROM {$GLOBALS['wpdb']->prefix}ce_enquiries");
    $total_users = count_users()['total_users'];
    // Render stat cards grid
    // ... (full implementation in plan execution)
}
```

- [ ] **Step 2: Customise admin sidebar navigation**

Reorganise the WordPress admin menu to match griyakita's clean sidebar structure:

```
Dashboard
─────────────
Filament Shield (mapped to)
  → Roles (Users > Roles via Members plugin or custom)
─────────────
User Management
  → Users
─────────────
Blog Management
  → Posts
  → Categories
  → Tags
  → Newsletter Subscribers (custom admin page)
─────────────
Property Management
  → Properties (ce_property CPT)
  → Tour Requests (custom admin page or CPT)
─────────────
Analytics
  → Visitor Analytics (custom page pulling from GA or simple built-in tracker)
─────────────
Master Data
  → Cities (ce_city taxonomy)
  → Property Types
─────────────
Site Settings (ACF options)
```

```php
// Reorder and rename admin menu items
function ce_custom_admin_menu() {
    // Remove items we don't need visible
    remove_menu_page('edit-comments.php');
    remove_menu_page('tools.php');

    // Rename "Posts" to "Blog Posts"
    global $menu;
    foreach ($menu as $key => $item) {
        if ($item[2] === 'edit.php') {
            $menu[$key][0] = 'Blog Posts';
        }
    }

    // Add Newsletter Subscribers page
    add_menu_page(
        'Newsletter Subscribers',
        'Subscribers',
        'manage_options',
        'ce-subscribers',
        'ce_subscribers_page_render',
        'dashicons-email-alt',
        26
    );

    // Add Tour Requests page (if using custom DB table instead of CPT)
    add_menu_page(
        'Tour Requests',
        'Tour Requests',
        'manage_options',
        'ce-tour-requests',
        'ce_tour_requests_page_render',
        'dashicons-calendar-alt',
        27
    );
}
add_action('admin_menu', 'ce_custom_admin_menu');
```

- [ ] **Step 3: Brand the admin — login page, colours, header**

Custom login page with Crowns Estates branding (logo, gold/black colours):

```php
// Custom login logo
function ce_login_logo() {
    $logo_url = get_template_directory_uri() . '/img/logo.png';
    echo '<style>
        body.login { background: #0A0A0A; }
        #login h1 a {
            background-image: url(' . esc_url($logo_url) . ');
            width: 200px; height: 80px;
            background-size: contain; background-repeat: no-repeat;
        }
        .login form { border-radius: 8px; }
        .wp-core-ui .button-primary {
            background: #C4973A !important; border-color: #A37E2C !important;
        }
    </style>';
}
add_action('login_enqueue_scripts', 'ce_login_logo');

// Custom admin colour scheme
function ce_admin_styles() {
    echo '<style>
        #adminmenuback, #adminmenuwrap { background: #1a1a1a; }
        #adminmenu .wp-has-current-submenu .wp-submenu-head,
        #adminmenu a.wp-has-current-submenu { background: #C4973A !important; }
        #wpadminbar { background: #0A0A0A; }
    </style>';
}
add_action('admin_head', 'ce_admin_styles');

// Custom admin footer
function ce_admin_footer_text() {
    return 'Crowns Estates Admin Panel &mdash; Powered by WordPress';
}
add_filter('admin_footer_text', 'ce_admin_footer_text');
```

- [ ] **Step 4: Create Newsletter Subscribers admin page**

Admin page with WP_List_Table showing all newsletter subscribers (from rentals page email capture and any other signup forms). Columns: Name, Email, Date Subscribed, Source. Add/Delete functionality. CSV export button.

Requires a `{prefix}ce_subscribers` table (add creation alongside the enquiries table in `enquiry-handler.php`):

```php
function ce_create_subscribers_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_subscribers';
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255),
        email VARCHAR(255) NOT NULL UNIQUE,
        source VARCHAR(100) DEFAULT 'website',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
```

- [ ] **Step 5: Create `js/admin-dashboard.js` — Chart.js sparklines**

Enqueue Chart.js from CDN on admin dashboard page only. Render small sparkline charts in each stat card (matching griyakita's visual style). Data passed via `wp_localize_script`.

- [ ] **Step 6: Add admin asset enqueuing to `inc/enqueue.php`**

```php
function ce_admin_enqueue($hook) {
    // Only on dashboard
    if ($hook === 'index.php') {
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js', [], '4.0', true);
        wp_enqueue_script('ce-admin-dashboard', get_template_directory_uri() . '/js/admin-dashboard.js', ['chartjs'], '1.0', true);
        wp_localize_script('ce-admin-dashboard', 'ceDashData', [
            'properties' => wp_count_posts('ce_property'),
            'enquiries'  => $GLOBALS['wpdb']->get_var("SELECT COUNT(*) FROM {$GLOBALS['wpdb']->prefix}ce_enquiries"),
        ]);
    }
}
add_action('admin_enqueue_scripts', 'ce_admin_enqueue');
```

- [ ] **Step 7: Add include to `functions.php` and commit**

```bash
git add wp-content/themes/crowns-estates/inc/admin-dashboard.php wp-content/themes/crowns-estates/js/admin-dashboard.js wp-content/themes/crowns-estates/inc/enqueue.php wp-content/themes/crowns-estates/functions.php wp-content/themes/crowns-estates/style.css
git commit -m "feat: custom admin dashboard with stat cards, branded sidebar, login page, subscribers"
```

---

## Summary

| Task | Description | Est. Steps |
|------|-------------|-----------|
| 1 | Project scaffold & git setup | 5 |
| 2 | Design system — CSS variables & base styles | 2 |
| 3 | Theme core — functions.php, header, footer, page.php | 6 |
| 4 | Custom post types & taxonomy | 5 |
| 5 | ACF field groups & options pages | 5 |
| 6 | Multi-currency helper functions (cache-safe) | 4 |
| 7 | Template parts — reusable components | 8 |
| 8 | Enquiry handler — form, DB, auto-reply | 5 |
| 9 | JavaScript modules | 7 |
| 10 | Homepage — front-page.php | 2 |
| 11 | Projects page & single property | 4 |
| 12 | How It Works + calculator + AI placeholder | 2 |
| 13 | About, Contact, Rentals pages | 4 |
| 14 | Blog templates | 4 |
| 15 | Schema markup & SEO | 3 |
| 16 | Legal pages & 404 | 3 |
| 17 | Sample content, placeholders & screenshot | 5 |
| 18 | Final integration & push | 5 |
| 19 | Custom admin dashboard & branded backend | 7 |
| **Total** | | **86 steps** |
