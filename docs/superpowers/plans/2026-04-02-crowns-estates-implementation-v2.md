# Crowns Estates Website — Implementation Plan v2

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a fully functional, immersive 3D WordPress website for Crowns Estates — a UK real estate agency specialising in Saudi Arabian property investment — with custom backend, branded admin dashboard, and GA4 analytics.

**Architecture:** Custom WordPress theme built on Underscores (`_s`). Two CPTs (`ce_property`, `ce_testimonial`), one taxonomy (`ce_city`), ACF for all custom fields and site options. Three.js + GSAP + Lenis for immersive 3D scroll-driven experiences across all pages. Vanilla JS for interactive features (calculator, currency toggle, modals, filters). GA4 via GTM with custom event tracking.

**Tech Stack:** WordPress 6.x, PHP 8.x, ACF, Three.js, GSAP + ScrollTrigger, Lenis, Chart.js (admin), vanilla JavaScript, CSS custom properties, Schema.org JSON-LD, GA4 + GTM.

**Spec:** `docs/superpowers/specs/2026-04-02-crowns-estates-website-design-v2.md`

**Existing state:** Front-end page templates exist with static placeholder content. No backend code, no `inc/` directory, no `template-parts/`, no `js/` directory, no blog templates. The existing templates will be refactored to use dynamic WordPress queries and template parts as backend tasks are completed.

---

## File Structure

```
wp-content/themes/crowns-estates/
├── style.css                              # EXISTING — design tokens + component styles (will be extended)
├── functions.php                          # EXISTING — theme setup (will add includes)
├── header.php                             # EXISTING — will refactor to use wp_nav_menu + dynamic currency toggle
├── footer.php                             # EXISTING — will refactor to use template parts
├── index.php                              # EXISTING — fallback
├── page.php                               # EXISTING — default page template
├── front-page.php                         # EXISTING — will refactor to use dynamic queries + template parts
├── page-projects.php                      # EXISTING — will refactor for dynamic property queries + filters
├── page-how-it-works.php                  # EXISTING — will refactor for dynamic calculator + FAQ
├── page-about.php                         # EXISTING — will keep mostly static
├── page-contact.php                       # EXISTING — will refactor form to use REST endpoint
├── page-rentals.php                       # EXISTING — will refactor email capture to use REST endpoint
├── 404.php                                # EXISTING — branded 404
├── single-ce_property.php                 # NEW — single property detail page
├── archive-ce_property.php                # NEW — property archive fallback
├── single.php                             # NEW — single blog post
├── archive.php                            # NEW — blog archive
├── sidebar.php                            # NEW — blog sidebar
├── screenshot.png                         # NEW — theme preview image
├── inc/
│   ├── cpt-property.php                   # NEW — ce_property CPT registration
│   ├── cpt-testimonial.php                # NEW — ce_testimonial CPT registration
│   ├── taxonomy-city.php                  # NEW — ce_city taxonomy
│   ├── acf-fields-property.php            # NEW — ACF field group: Property fields
│   ├── acf-fields-testimonial.php         # NEW — ACF field group: Testimonial fields
│   ├── acf-options.php                    # NEW — ACF options pages (rates, WhatsApp, etc.)
│   ├── enqueue.php                        # NEW — all script and style enqueuing (replaces inline in functions.php)
│   ├── currency-helpers.php               # NEW — multi-currency formatting + REST endpoint
│   ├── enquiry-handler.php                # NEW — form → DB + email + auto-reply + brochure gate
│   ├── schema-markup.php                  # NEW — JSON-LD structured data
│   ├── admin-dashboard.php                # NEW — custom dashboard, sidebar, branding
│   └── ga4-tracking.php                   # NEW — GTM container + custom event data layer
├── template-parts/
│   ├── property-card.php                  # NEW — reusable property listing card
│   ├── testimonial-card.php               # NEW — reusable testimonial card
│   ├── hero.php                           # NEW — configurable hero section
│   ├── trust-bar.php                      # NEW — trust signals bar
│   ├── cta-banner.php                     # NEW — CTA banner section
│   ├── modal-register-interest.php        # NEW — register interest modal overlay
│   ├── modal-brochure-gate.php            # NEW — brochure email capture modal
│   ├── whatsapp-button.php                # NEW — sticky WhatsApp FAB
│   └── developer-badge.php                # NEW — developer reliability badge
├── js/
│   ├── calculator.js                      # NEW — investment cost calculator
│   ├── currency-toggle.js                 # NEW — front-end currency switching
│   ├── modal.js                           # NEW — modal open/close + form submission
│   ├── city-filter.js                     # NEW — projects page filtering
│   ├── faq-accordion.js                   # NEW — FAQ expand/collapse
│   ├── admin-dashboard.js                 # NEW — admin sparkline charts
│   ├── ga4-events.js                      # NEW — custom GA4 event pushes
│   └── 3d/
│       ├── scene-manager.js               # NEW — Three.js scene setup, renderer, camera, resize
│       ├── scroll-controller.js           # NEW — GSAP ScrollTrigger + Lenis integration
│       ├── hero-scene.js                  # NEW — homepage 3D skyline scene
│       ├── projects-map.js                # NEW — projects page 3D city map
│       ├── property-viewer.js             # NEW — single property 3D model viewer
│       ├── journey-scene.js               # NEW — How It Works 3D path
│       ├── particles.js                   # NEW — gold particle system
│       └── fallback.js                    # NEW — WebGL detection + static fallback
├── models/
│   ├── skyline.glb                        # NEW — homepage hero 3D model
│   ├── city-map.glb                       # NEW — projects page city map
│   └── properties/                        # NEW — per-property 3D models (optional)
├── img/
│   └── placeholder-property.jpg           # NEW — fallback property image
└── sample-content/
    ├── 5-things-uk-investors.md           # NEW — sample blog post
    ├── golden-visa-guide.md               # NEW — sample blog post
    ├── neom-investment-guide.md            # NEW — sample blog post
    ├── understanding-off-plan.md           # NEW — sample blog post
    └── riyadh-vs-jeddah.md                # NEW — sample blog post
```

---

## Task 1: Refactor functions.php & Create Enqueue Module

**Why first:** All subsequent tasks depend on a clean functions.php with modular includes and proper asset enqueuing.

**Files:**
- Modify: `wp-content/themes/crowns-estates/functions.php`
- Create: `wp-content/themes/crowns-estates/inc/enqueue.php`

- [ ] **Step 1: Create `inc/enqueue.php`**

Move the existing font/style enqueuing out of `functions.php` into this dedicated file. Add placeholder hooks for JS files that will be created in later tasks.

```php
<?php
/**
 * Enqueue scripts and styles.
 */
function ce_enqueue_assets() {
    // Google Fonts
    wp_enqueue_style('ce-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap',
        [], null
    );

    // Theme stylesheet
    wp_enqueue_style('ce-style', get_stylesheet_uri(), ['ce-google-fonts'], wp_get_theme()->get('Version'));

    // Currency toggle — all pages
    wp_enqueue_script('ce-currency-toggle', get_template_directory_uri() . '/js/currency-toggle.js', [], '1.0', true);
    wp_localize_script('ce-currency-toggle', 'ceData', [
        'restUrl' => esc_url_raw(rest_url('ce/v1/')),
        'nonce'   => wp_create_nonce('wp_rest'),
    ]);

    // Modal — all pages
    wp_enqueue_script('ce-modal', get_template_directory_uri() . '/js/modal.js', [], '1.0', true);

    // GA4 custom events — all pages
    wp_enqueue_script('ce-ga4-events', get_template_directory_uri() . '/js/ga4-events.js', [], '1.0', true);

    // Calculator — How It Works page only
    if (is_page('how-it-works')) {
        wp_enqueue_script('ce-calculator', get_template_directory_uri() . '/js/calculator.js', [], '1.0', true);
        $calc_rates = [
            'registration_fee' => function_exists('get_field') ? (float) get_field('ce_calc_registration_fee', 'option') ?: 2.5 : 2.5,
            'vat'              => function_exists('get_field') ? (float) get_field('ce_calc_vat', 'option') ?: 5 : 5,
            'agency_fee'       => function_exists('get_field') ? (float) get_field('ce_calc_agency_fee', 'option') ?: 2 : 2,
        ];
        wp_localize_script('ce-calculator', 'ceCalcRates', $calc_rates);

        wp_enqueue_script('ce-faq-accordion', get_template_directory_uri() . '/js/faq-accordion.js', [], '1.0', true);
    }

    // City filter — Projects page only
    if (is_page('projects')) {
        wp_enqueue_script('ce-city-filter', get_template_directory_uri() . '/js/city-filter.js', [], '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'ce_enqueue_assets');
```

- [ ] **Step 2: Refactor `functions.php` — replace inline enqueue with include**

Replace the entire `ce_enqueue_assets` function block in `functions.php` with a require for the new file. Add commented-out includes for files that will be created in later tasks.

```php
<?php
/**
 * Crowns Estates Theme Functions
 */

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
// Uncomment as files are created:
// require get_template_directory() . '/inc/cpt-property.php';
// require get_template_directory() . '/inc/cpt-testimonial.php';
// require get_template_directory() . '/inc/taxonomy-city.php';
// require get_template_directory() . '/inc/acf-fields-property.php';
// require get_template_directory() . '/inc/acf-fields-testimonial.php';
// require get_template_directory() . '/inc/acf-options.php';
// require get_template_directory() . '/inc/currency-helpers.php';
// require get_template_directory() . '/inc/enquiry-handler.php';
// require get_template_directory() . '/inc/schema-markup.php';
// require get_template_directory() . '/inc/admin-dashboard.php';
// require get_template_directory() . '/inc/ga4-tracking.php';
```

- [ ] **Step 3: Verify the theme still loads**

Open the existing preview at `https://jalookout7-eng.github.io/crown-estates-website/preview/` and confirm no regressions. (The preview is static HTML, so this is a visual sanity check only. Full WordPress testing happens at deployment.)

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/functions.php wp-content/themes/crowns-estates/inc/enqueue.php
git commit -m "refactor: extract enqueue module from functions.php, add modular include structure"
```

---

## Task 2: Custom Post Types & Taxonomy

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/cpt-property.php`
- Create: `wp-content/themes/crowns-estates/inc/cpt-testimonial.php`
- Create: `wp-content/themes/crowns-estates/inc/taxonomy-city.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (uncomment includes)

- [ ] **Step 1: Create `inc/cpt-property.php`**

```php
<?php
/**
 * Register ce_property Custom Post Type.
 */
function ce_register_property_cpt() {
    $labels = [
        'name'               => 'Properties',
        'singular_name'      => 'Property',
        'menu_name'          => 'Properties',
        'add_new'            => 'Add New Property',
        'add_new_item'       => 'Add New Property',
        'edit_item'          => 'Edit Property',
        'new_item'           => 'New Property',
        'view_item'          => 'View Property',
        'search_items'       => 'Search Properties',
        'not_found'          => 'No properties found',
        'not_found_in_trash' => 'No properties found in trash',
    ];

    $args = [
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'properties'],
        'supports'      => ['title', 'editor', 'thumbnail'],
        'menu_icon'     => 'dashicons-building',
        'show_in_rest'  => true,
        'menu_position' => 5,
    ];

    register_post_type('ce_property', $args);
}
add_action('init', 'ce_register_property_cpt');
```

- [ ] **Step 2: Create `inc/cpt-testimonial.php`**

```php
<?php
/**
 * Register ce_testimonial Custom Post Type.
 */
function ce_register_testimonial_cpt() {
    $labels = [
        'name'               => 'Testimonials',
        'singular_name'      => 'Testimonial',
        'menu_name'          => 'Testimonials',
        'add_new'            => 'Add New Testimonial',
        'add_new_item'       => 'Add New Testimonial',
        'edit_item'          => 'Edit Testimonial',
        'new_item'           => 'New Testimonial',
        'view_item'          => 'View Testimonial',
        'search_items'       => 'Search Testimonials',
        'not_found'          => 'No testimonials found',
        'not_found_in_trash' => 'No testimonials found in trash',
    ];

    $args = [
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => false,
        'supports'      => ['title'],
        'menu_icon'     => 'dashicons-format-quote',
        'show_in_rest'  => true,
        'menu_position' => 6,
    ];

    register_post_type('ce_testimonial', $args);
}
add_action('init', 'ce_register_testimonial_cpt');
```

- [ ] **Step 3: Create `inc/taxonomy-city.php`**

```php
<?php
/**
 * Register ce_city taxonomy.
 * Attached to both ce_property and ce_testimonial.
 */
function ce_register_city_taxonomy() {
    $labels = [
        'name'              => 'Cities',
        'singular_name'     => 'City',
        'search_items'      => 'Search Cities',
        'all_items'         => 'All Cities',
        'edit_item'         => 'Edit City',
        'update_item'       => 'Update City',
        'add_new_item'      => 'Add New City',
        'new_item_name'     => 'New City Name',
        'menu_name'         => 'Cities',
    ];

    $args = [
        'labels'            => $labels,
        'hierarchical'      => true,
        'rewrite'           => ['slug' => 'city'],
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ];

    register_taxonomy('ce_city', ['ce_property', 'ce_testimonial'], $args);
}
add_action('init', 'ce_register_city_taxonomy');
```

- [ ] **Step 4: Uncomment includes in `functions.php`**

Uncomment the three new includes:

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

## Task 3: ACF Field Groups & Options Pages

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/acf-fields-property.php`
- Create: `wp-content/themes/crowns-estates/inc/acf-fields-testimonial.php`
- Create: `wp-content/themes/crowns-estates/inc/acf-options.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (uncomment includes)

- [ ] **Step 1: Create `inc/acf-fields-property.php`**

```php
<?php
/**
 * ACF field group: Property Details.
 * Location: ce_property post type.
 */
function ce_register_property_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'      => 'group_ce_property',
        'title'    => 'Property Details',
        'fields'   => [
            [
                'key'   => 'field_ce_developer',
                'label' => 'Developer',
                'name'  => 'ce_developer',
                'type'  => 'text',
                'instructions' => 'e.g. ROSHN, Dar Global',
            ],
            [
                'key'     => 'field_ce_developer_badge',
                'label'   => 'Developer Badge',
                'name'    => 'ce_developer_badge',
                'type'    => 'select',
                'choices' => [
                    'none'            => 'None',
                    'verified'        => 'Verified Developer',
                    'track_record'    => 'Track Record',
                    'premium_partner' => 'Premium Partner',
                ],
                'default_value' => 'none',
            ],
            [
                'key'   => 'field_ce_price_from',
                'label' => 'Price From',
                'name'  => 'ce_price_from',
                'type'  => 'number',
            ],
            [
                'key'     => 'field_ce_currency',
                'label'   => 'Currency',
                'name'    => 'ce_currency',
                'type'    => 'select',
                'choices' => [
                    'SAR' => 'SAR',
                    'GBP' => 'GBP',
                    'USD' => 'USD',
                ],
                'default_value' => 'SAR',
            ],
            [
                'key'     => 'field_ce_property_type',
                'label'   => 'Property Type',
                'name'    => 'ce_property_type',
                'type'    => 'select',
                'choices' => [
                    'apartment'  => 'Apartment',
                    'villa'      => 'Villa',
                    'commercial' => 'Commercial',
                ],
            ],
            [
                'key'     => 'field_ce_status',
                'label'   => 'Status',
                'name'    => 'ce_status',
                'type'    => 'select',
                'choices' => [
                    'off-plan'          => 'Off-Plan',
                    'under-construction' => 'Under Construction',
                    'ready'             => 'Ready',
                ],
            ],
            [
                'key'          => 'field_ce_completion_date',
                'label'        => 'Completion Date',
                'name'         => 'ce_completion_date',
                'type'         => 'date_picker',
                'display_format' => 'F Y',
                'return_format'  => 'Y-m-d',
            ],
            [
                'key'   => 'field_ce_bedrooms',
                'label' => 'Bedrooms',
                'name'  => 'ce_bedrooms',
                'type'  => 'text',
                'instructions' => 'e.g. "2-3" or "4"',
            ],
            [
                'key'   => 'field_ce_size_sqm',
                'label' => 'Size (Sq.M.)',
                'name'  => 'ce_size_sqm',
                'type'  => 'number',
            ],
            [
                'key'   => 'field_ce_is_freehold',
                'label' => 'Is Freehold',
                'name'  => 'ce_is_freehold',
                'type'  => 'true_false',
                'ui'    => 1,
            ],
            [
                'key'   => 'field_ce_short_description',
                'label' => 'Short Description',
                'name'  => 'ce_short_description',
                'type'  => 'textarea',
                'rows'  => 3,
                'instructions' => 'Used on listing cards.',
            ],
            [
                'key'   => 'field_ce_full_description',
                'label' => 'Full Description',
                'name'  => 'ce_full_description',
                'type'  => 'wysiwyg',
                'instructions' => 'Used on single property page.',
            ],
            [
                'key'   => 'field_ce_gallery',
                'label' => 'Gallery',
                'name'  => 'ce_gallery',
                'type'  => 'gallery',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ],
            [
                'key'   => 'field_ce_brochure_pdf',
                'label' => 'Brochure PDF',
                'name'  => 'ce_brochure_pdf',
                'type'  => 'file',
                'return_format' => 'url',
                'mime_types'    => 'pdf',
            ],
            [
                'key'   => 'field_ce_brochure_gated',
                'label' => 'Gate Brochure Download',
                'name'  => 'ce_brochure_gated',
                'type'  => 'true_false',
                'ui'    => 1,
                'instructions' => 'If enabled, users must enter their email to download the brochure.',
            ],
            [
                'key'   => 'field_ce_featured',
                'label' => 'Featured',
                'name'  => 'ce_featured',
                'type'  => 'true_false',
                'ui'    => 1,
                'instructions' => 'Show on homepage.',
            ],
            [
                'key'   => 'field_ce_map_embed',
                'label' => 'Map Embed URL',
                'name'  => 'ce_map_embed',
                'type'  => 'url',
            ],
        ],
        'location' => [
            [
                ['param' => 'post_type', 'operator' => '==', 'value' => 'ce_property'],
            ],
        ],
        'position' => 'normal',
        'style'    => 'default',
    ]);
}
add_action('acf/init', 'ce_register_property_fields');
```

- [ ] **Step 2: Create `inc/acf-fields-testimonial.php`**

```php
<?php
/**
 * ACF field group: Testimonial Details.
 * Location: ce_testimonial post type.
 */
function ce_register_testimonial_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'      => 'group_ce_testimonial',
        'title'    => 'Testimonial Details',
        'fields'   => [
            [
                'key'   => 'field_ce_client_name',
                'label' => 'Client Name',
                'name'  => 'ce_client_name',
                'type'  => 'text',
                'instructions' => 'Can be anonymised, e.g. "UK Investor"',
            ],
            [
                'key'   => 'field_ce_client_location',
                'label' => 'Location',
                'name'  => 'ce_client_location',
                'type'  => 'text',
                'instructions' => 'e.g. "London, UK"',
            ],
            [
                'key'   => 'field_ce_quote',
                'label' => 'Quote',
                'name'  => 'ce_quote',
                'type'  => 'textarea',
                'rows'  => 4,
            ],
            [
                'key'           => 'field_ce_rating',
                'label'         => 'Rating',
                'name'          => 'ce_rating',
                'type'          => 'number',
                'min'           => 1,
                'max'           => 5,
                'default_value' => 5,
            ],
            [
                'key'   => 'field_ce_google_review_link',
                'label' => 'Google Review Link',
                'name'  => 'ce_google_review_link',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_ce_testimonial_featured',
                'label' => 'Featured',
                'name'  => 'ce_testimonial_featured',
                'type'  => 'true_false',
                'ui'    => 1,
                'instructions' => 'Show on homepage.',
            ],
            [
                'key'            => 'field_ce_testimonial_date',
                'label'          => 'Review Date',
                'name'           => 'ce_testimonial_date',
                'type'           => 'date_picker',
                'display_format' => 'd/m/Y',
                'return_format'  => 'Y-m-d',
            ],
        ],
        'location' => [
            [
                ['param' => 'post_type', 'operator' => '==', 'value' => 'ce_testimonial'],
            ],
        ],
        'position' => 'normal',
        'style'    => 'default',
    ]);
}
add_action('acf/init', 'ce_register_testimonial_fields');
```

- [ ] **Step 3: Create `inc/acf-options.php`**

```php
<?php
/**
 * ACF Options Pages: Site Settings, Exchange Rates, Calculator.
 */
function ce_register_options_pages() {
    if (!function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page([
        'page_title' => 'Site Settings',
        'menu_title' => 'Site Settings',
        'menu_slug'  => 'ce-site-settings',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-admin-generic',
        'position'   => 30,
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
add_action('acf/init', 'ce_register_options_pages');

/**
 * Register ACF fields for options pages.
 */
function ce_register_options_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // Exchange Rates
    acf_add_local_field_group([
        'key'    => 'group_ce_exchange_rates',
        'title'  => 'Exchange Rates',
        'fields' => [
            [
                'key'           => 'field_ce_rate_gbp_to_sar',
                'label'         => 'GBP to SAR',
                'name'          => 'ce_rate_gbp_to_sar',
                'type'          => 'number',
                'step'          => 0.01,
                'default_value' => 4.68,
            ],
            [
                'key'           => 'field_ce_rate_usd_to_sar',
                'label'         => 'USD to SAR',
                'name'          => 'ce_rate_usd_to_sar',
                'type'          => 'number',
                'step'          => 0.01,
                'default_value' => 3.75,
            ],
            [
                'key'           => 'field_ce_rate_gbp_to_usd',
                'label'         => 'GBP to USD',
                'name'          => 'ce_rate_gbp_to_usd',
                'type'          => 'number',
                'step'          => 0.01,
                'default_value' => 1.27,
            ],
        ],
        'location' => [
            [
                ['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-exchange-rates'],
            ],
        ],
    ]);

    // Calculator Rates
    acf_add_local_field_group([
        'key'    => 'group_ce_calculator',
        'title'  => 'Calculator Settings',
        'fields' => [
            [
                'key'           => 'field_ce_calc_registration_fee',
                'label'         => 'Registration Fee (%)',
                'name'          => 'ce_calc_registration_fee',
                'type'          => 'number',
                'step'          => 0.1,
                'default_value' => 2.5,
            ],
            [
                'key'           => 'field_ce_calc_vat',
                'label'         => 'VAT (%)',
                'name'          => 'ce_calc_vat',
                'type'          => 'number',
                'step'          => 0.1,
                'default_value' => 5,
            ],
            [
                'key'           => 'field_ce_calc_agency_fee',
                'label'         => 'Agency Fee (%)',
                'name'          => 'ce_calc_agency_fee',
                'type'          => 'number',
                'step'          => 0.1,
                'default_value' => 2,
            ],
        ],
        'location' => [
            [
                ['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-calculator-settings'],
            ],
        ],
    ]);

    // General Site Settings
    acf_add_local_field_group([
        'key'    => 'group_ce_site_settings',
        'title'  => 'General Settings',
        'fields' => [
            [
                'key'   => 'field_ce_whatsapp_number',
                'label' => 'WhatsApp Number',
                'name'  => 'ce_whatsapp_number',
                'type'  => 'text',
                'instructions' => 'International format, e.g. 447000000000',
            ],
            [
                'key'           => 'field_ce_contact_email',
                'label'         => 'Contact Email',
                'name'          => 'ce_contact_email',
                'type'          => 'email',
                'default_value' => 'info@crownsestates.co.uk',
            ],
        ],
        'location' => [
            [
                ['param' => 'options_page', 'operator' => '==', 'value' => 'ce-site-settings'],
            ],
        ],
    ]);
}
add_action('acf/init', 'ce_register_options_fields');
```

- [ ] **Step 4: Uncomment includes in `functions.php`**

Uncomment:
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

## Task 4: Multi-Currency Helper Functions

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/currency-helpers.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (uncomment include)

- [ ] **Step 1: Create `inc/currency-helpers.php`**

```php
<?php
/**
 * Multi-currency helper functions.
 *
 * IMPORTANT: Server-side rendering ALWAYS outputs the listing's native currency.
 * Currency conversion is handled entirely client-side by currency-toggle.js.
 * This ensures compatibility with WP Super Cache (no cookies read server-side).
 */

/**
 * Get exchange rates from ACF options.
 */
function ce_get_exchange_rates(): array {
    return [
        'GBP_SAR' => (float) (function_exists('get_field') ? get_field('ce_rate_gbp_to_sar', 'option') : 0) ?: 4.68,
        'USD_SAR'  => (float) (function_exists('get_field') ? get_field('ce_rate_usd_to_sar', 'option') : 0) ?: 3.75,
        'GBP_USD'  => (float) (function_exists('get_field') ? get_field('ce_rate_gbp_to_usd', 'option') : 0) ?: 1.27,
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
 */
function ce_display_price(int $post_id = 0): string {
    $post_id = $post_id ?: get_the_ID();
    $price = (float) get_field('ce_price_from', $post_id);
    $native = get_field('ce_currency', $post_id) ?: 'SAR';

    if (!$price) {
        return '<span class="ce-price" data-price="0" data-currency="">Price on request</span>';
    }

    $formatted = ce_format_price($price, $native);
    return sprintf(
        '<span class="ce-price" data-price="%s" data-currency="%s">%s</span>',
        esc_attr($price),
        esc_attr($native),
        esc_html($formatted)
    );
}

/**
 * REST endpoint for currency rates.
 * JS currency-toggle.js fetches these and does all conversion client-side.
 */
function ce_rest_exchange_rates() {
    register_rest_route('ce/v1', '/rates', [
        'methods'             => 'GET',
        'callback'            => function () {
            return ce_get_exchange_rates();
        },
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'ce_rest_exchange_rates');
```

- [ ] **Step 2: Uncomment include in `functions.php`**

```php
require get_template_directory() . '/inc/currency-helpers.php';
```

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/currency-helpers.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: multi-currency helper functions and REST endpoint"
```

---

## Task 5: Enquiry Handler — Form Submission, DB Storage, Auto-Reply, Brochure Gate

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/enquiry-handler.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (uncomment include)

- [ ] **Step 1: Create `inc/enquiry-handler.php`**

```php
<?php
/**
 * Enquiry handler: DB table creation, REST endpoint for form submissions,
 * auto-responder email, and gated brochure download.
 */

/**
 * Create custom database table on theme activation.
 * Uses after_switch_theme (not register_activation_hook — that's plugins only).
 * Also checks on init for existing installs via a version option.
 */
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
        source VARCHAR(100) DEFAULT 'website',
        source_url VARCHAR(500),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('ce_enquiries_db_version', '1.0');
}
add_action('after_switch_theme', 'ce_create_enquiries_table');

add_action('init', function () {
    if (get_option('ce_enquiries_db_version') !== '1.0') {
        ce_create_enquiries_table();
    }
});

/**
 * REST endpoint: POST /wp-json/ce/v1/enquiry
 * Handles Register Interest form, Contact form, and Brochure Gate submissions.
 */
function ce_register_enquiry_endpoint() {
    register_rest_route('ce/v1', '/enquiry', [
        'methods'             => 'POST',
        'callback'            => 'ce_handle_enquiry',
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'ce_register_enquiry_endpoint');

function ce_handle_enquiry(\WP_REST_Request $request): \WP_REST_Response {
    $name    = sanitize_text_field($request->get_param('name') ?? '');
    $email   = sanitize_email($request->get_param('email') ?? '');
    $phone   = sanitize_text_field($request->get_param('phone') ?? '');
    $property = sanitize_text_field($request->get_param('property_interest') ?? '');
    $message = sanitize_textarea_field($request->get_param('message') ?? '');
    $consent = (bool) $request->get_param('gdpr_consent');
    $source  = sanitize_text_field($request->get_param('source') ?? 'website');
    $source_url = esc_url_raw($request->get_param('source_url') ?? '');
    $brochure_url = esc_url_raw($request->get_param('brochure_url') ?? '');

    // Validation
    if (empty($name)) {
        return new \WP_REST_Response(['success' => false, 'error' => 'Name is required.'], 400);
    }
    if (!is_email($email)) {
        return new \WP_REST_Response(['success' => false, 'error' => 'Valid email is required.'], 400);
    }
    if (!$consent) {
        return new \WP_REST_Response(['success' => false, 'error' => 'GDPR consent is required.'], 400);
    }

    // Store in DB
    global $wpdb;
    $table = $wpdb->prefix . 'ce_enquiries';
    $wpdb->insert($table, [
        'name'              => $name,
        'email'             => $email,
        'phone'             => $phone,
        'property_interest' => $property,
        'message'           => $message,
        'gdpr_consent'      => 1,
        'source'            => $source,
        'source_url'        => $source_url,
    ]);

    // Send admin notification
    $admin_email = function_exists('get_field') ? get_field('ce_contact_email', 'option') : '';
    $admin_email = $admin_email ?: 'info@crownsestates.co.uk';

    $admin_subject = 'New Enquiry — Crowns Estates';
    $admin_body = "New enquiry received:\n\n";
    $admin_body .= "Name: {$name}\n";
    $admin_body .= "Email: {$email}\n";
    $admin_body .= "Phone: {$phone}\n";
    $admin_body .= "Property: {$property}\n";
    $admin_body .= "Source: {$source}\n";
    $admin_body .= "Message:\n{$message}\n";
    wp_mail($admin_email, $admin_subject, $admin_body);

    // Auto-responder to enquirer
    $responder_subject = 'Thank you for your enquiry — Crowns Estates';
    $responder_body = "Dear {$name},\n\n";
    $responder_body .= "Thank you for your interest in investing with Crowns Estates. Our team will be in touch within 24 hours.\n\n";

    // If brochure gate, include download link
    if (!empty($brochure_url)) {
        $responder_body .= "Your brochure is ready to download:\n{$brochure_url}\n\n";
    }

    $responder_body .= "In the meantime, explore our latest opportunities at https://www.crownsestates.co.uk/projects\n\n";
    $responder_body .= "Best regards,\nThe Crowns Estates Team";
    wp_mail($email, $responder_subject, $responder_body);

    return new \WP_REST_Response(['success' => true, 'message' => 'Thank you! We\'ll be in touch within 24 hours.'], 200);
}

/**
 * Admin page: View Enquiries.
 * Registered as a submenu under Site Settings.
 */
function ce_register_enquiries_admin_page() {
    add_submenu_page(
        'ce-site-settings',
        'Enquiries',
        'Enquiries',
        'manage_options',
        'ce-enquiries',
        'ce_render_enquiries_page'
    );
}
add_action('admin_menu', 'ce_register_enquiries_admin_page');

function ce_render_enquiries_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_enquiries';
    $enquiries = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 100");

    echo '<div class="wrap">';
    echo '<h1>Enquiries</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Property</th><th>Source</th><th>Date</th></tr></thead>';
    echo '<tbody>';
    if ($enquiries) {
        foreach ($enquiries as $e) {
            printf(
                '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                esc_html($e->name),
                esc_html($e->email),
                esc_html($e->phone),
                esc_html($e->property_interest),
                esc_html($e->source),
                esc_html($e->created_at)
            );
        }
    } else {
        echo '<tr><td colspan="6">No enquiries yet.</td></tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}
```

- [ ] **Step 2: Uncomment include in `functions.php`**

```php
require get_template_directory() . '/inc/enquiry-handler.php';
```

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/enquiry-handler.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: enquiry handler — REST endpoint, DB storage, auto-responder, brochure gate, admin view"
```

---

## Task 6: Template Parts — Reusable Components

**Files:**
- Create: `wp-content/themes/crowns-estates/template-parts/property-card.php`
- Create: `wp-content/themes/crowns-estates/template-parts/testimonial-card.php`
- Create: `wp-content/themes/crowns-estates/template-parts/hero.php`
- Create: `wp-content/themes/crowns-estates/template-parts/trust-bar.php`
- Create: `wp-content/themes/crowns-estates/template-parts/cta-banner.php`
- Create: `wp-content/themes/crowns-estates/template-parts/modal-register-interest.php`
- Create: `wp-content/themes/crowns-estates/template-parts/modal-brochure-gate.php`
- Create: `wp-content/themes/crowns-estates/template-parts/whatsapp-button.php`
- Create: `wp-content/themes/crowns-estates/template-parts/developer-badge.php`

- [ ] **Step 1: Create `template-parts/property-card.php`**

Expects `$post` to be a `ce_property` post in the global loop or passed via `setup_postdata()`.

```php
<?php
/**
 * Template part: Property Card
 * Used on: front-page.php, page-projects.php, archive-ce_property.php
 */
$post_id     = get_the_ID();
$city_terms  = get_the_terms($post_id, 'ce_city');
$city_name   = $city_terms ? $city_terms[0]->name : '';
$city_slug   = $city_terms ? $city_terms[0]->slug : '';
$status      = get_field('ce_status', $post_id) ?: '';
$type        = get_field('ce_property_type', $post_id) ?: '';
$bedrooms    = get_field('ce_bedrooms', $post_id) ?: '';
$size        = get_field('ce_size_sqm', $post_id) ?: '';
$freehold    = get_field('ce_is_freehold', $post_id);
$developer   = get_field('ce_developer', $post_id) ?: '';
$badge       = get_field('ce_developer_badge', $post_id) ?: 'none';
$completion  = get_field('ce_completion_date', $post_id);
$comp_year   = $completion ? date('Y', strtotime($completion)) : '';
$thumbnail   = get_the_post_thumbnail_url($post_id, 'large') ?: get_template_directory_uri() . '/img/placeholder-property.jpg';

$status_class = '';
if ($status === 'off-plan') $status_class = 'ce-property-card__status--off-plan';
if ($status === 'under-construction') $status_class = 'ce-property-card__status--construction';
if ($status === 'ready') $status_class = 'ce-property-card__status--ready';

$status_labels = [
    'off-plan'           => 'Off-Plan',
    'under-construction' => 'Under Construction',
    'ready'              => 'Ready',
];
$type_labels = [
    'apartment'  => 'Apartment',
    'villa'      => 'Villa',
    'commercial' => 'Commercial',
];
?>
<div class="ce-property-card" data-city="<?php echo esc_attr($city_slug); ?>" data-developer="<?php echo esc_attr(sanitize_title($developer)); ?>" data-status="<?php echo esc_attr($status); ?>">
    <a href="<?php the_permalink(); ?>" class="ce-property-card__image" style="background-image: url('<?php echo esc_url($thumbnail); ?>');">
        <?php if ($city_name): ?>
            <span class="ce-property-card__badge"><?php echo esc_html($city_name); ?></span>
        <?php endif; ?>
        <?php if ($status): ?>
            <span class="ce-property-card__status <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_labels[$status] ?? $status); ?></span>
        <?php endif; ?>
    </a>
    <div class="ce-property-card__body">
        <h3 class="ce-property-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <div class="ce-property-card__developer">
            <?php echo esc_html($developer); ?>
            <?php if ($badge !== 'none'): ?>
                <?php get_template_part('template-parts/developer-badge', null, ['badge' => $badge]); ?>
            <?php endif; ?>
        </div>
        <div class="ce-property-card__type">
            <?php echo esc_html($type_labels[$type] ?? $type); ?>
            <?php if ($freehold): ?> &bull; Freehold<?php endif; ?>
        </div>
        <div class="ce-property-card__meta">
            <?php if ($bedrooms): ?>
                <div class="ce-property-card__meta-item"><strong><?php echo esc_html($bedrooms); ?></strong> Beds</div>
            <?php endif; ?>
            <?php if ($size): ?>
                <div class="ce-property-card__meta-item"><strong><?php echo esc_html($size); ?></strong> Sq.M.</div>
            <?php endif; ?>
            <?php if ($comp_year): ?>
                <div class="ce-property-card__meta-item"><strong><?php echo esc_html($comp_year); ?></strong> Completion</div>
            <?php endif; ?>
        </div>
        <div class="ce-property-card__footer">
            <div class="ce-property-card__price"><?php echo ce_display_price($post_id); ?> <small>from</small></div>
            <a href="<?php the_permalink(); ?>" class="ce-btn ce-btn--outline ce-btn--sm">View</a>
        </div>
    </div>
</div>
```

- [ ] **Step 2: Create `template-parts/testimonial-card.php`**

```php
<?php
/**
 * Template part: Testimonial Card
 * Used on: front-page.php, single-ce_property.php
 */
$post_id    = get_the_ID();
$name       = get_field('ce_client_name', $post_id) ?: 'Investor';
$location   = get_field('ce_client_location', $post_id) ?: '';
$quote      = get_field('ce_quote', $post_id) ?: '';
$rating     = (int) (get_field('ce_rating', $post_id) ?: 5);
$google_url = get_field('ce_google_review_link', $post_id) ?: '';
$initials   = implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), explode(' ', $name)));
?>
<div class="ce-testimonial-card" itemscope itemtype="https://schema.org/Review">
    <div class="ce-testimonial-card__stars">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <span class="ce-testimonial-card__star<?php echo $i <= $rating ? '' : ' ce-testimonial-card__star--empty'; ?>">&#9733;</span>
        <?php endfor; ?>
    </div>
    <p class="ce-testimonial-card__quote" itemprop="reviewBody">"<?php echo esc_html($quote); ?>"</p>
    <div class="ce-testimonial-card__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
        <div class="ce-testimonial-card__avatar"><?php echo esc_html($initials); ?></div>
        <div>
            <div class="ce-testimonial-card__name" itemprop="name"><?php echo esc_html($name); ?></div>
            <?php if ($location): ?>
                <div class="ce-testimonial-card__location"><?php echo esc_html($location); ?></div>
            <?php endif; ?>
            <?php if ($google_url): ?>
                <a href="<?php echo esc_url($google_url); ?>" class="ce-testimonial-card__google" target="_blank" rel="noopener">&#10003; Google Review</a>
            <?php endif; ?>
        </div>
    </div>
    <meta itemprop="ratingValue" content="<?php echo esc_attr($rating); ?>">
</div>
```

- [ ] **Step 3: Create `template-parts/hero.php`**

Accepts args via `get_template_part('template-parts/hero', null, $args)`.

```php
<?php
/**
 * Template part: Hero Section
 * Args: title, subtitle, label, cta_text, cta_url, bg_image
 */
$title    = $args['title'] ?? '';
$subtitle = $args['subtitle'] ?? '';
$label    = $args['label'] ?? '';
$cta_text = $args['cta_text'] ?? '';
$cta_url  = $args['cta_url'] ?? '#';
$bg_image = $args['bg_image'] ?? '';
?>
<div class="ce-hero"<?php if ($bg_image): ?> style="background-image: url('<?php echo esc_url($bg_image); ?>');"<?php endif; ?>>
    <div class="ce-hero__content">
        <?php if ($label): ?>
            <div class="ce-hero__label"><?php echo esc_html($label); ?></div>
        <?php endif; ?>
        <?php if ($title): ?>
            <h1><?php echo esc_html($title); ?></h1>
        <?php endif; ?>
        <?php if ($subtitle): ?>
            <p><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
        <?php if ($cta_text): ?>
            <a href="<?php echo esc_url($cta_url); ?>" class="ce-btn ce-btn--gold ce-btn--lg"><?php echo esc_html($cta_text); ?></a>
        <?php endif; ?>
    </div>
</div>
```

- [ ] **Step 4: Create `template-parts/trust-bar.php`**

```php
<?php
/**
 * Template part: Trust Bar
 */
?>
<div class="ce-trust-bar">
    <div class="ce-container">
        <div class="ce-trust-bar__inner">
            <div class="ce-trust-bar__item">
                <span class="ce-trust-bar__icon">&#9670;</span>
                20 Years in Saudi Arabia
            </div>
            <div class="ce-trust-bar__divider"></div>
            <div class="ce-trust-bar__item">
                <span class="ce-trust-bar__icon">&#9670;</span>
                British Expat Expertise
            </div>
            <div class="ce-trust-bar__divider"></div>
            <div class="ce-trust-bar__item">
                <span class="ce-trust-bar__icon">&#9670;</span>
                End-to-End Investor Support
            </div>
        </div>
    </div>
</div>
```

- [ ] **Step 5: Create `template-parts/cta-banner.php`**

```php
<?php
/**
 * Template part: CTA Banner
 * Args: title, subtitle, cta_text, cta_url, cta_modal (bool — opens register interest modal instead of link)
 */
$title     = $args['title'] ?? 'Ready to Invest?';
$subtitle  = $args['subtitle'] ?? 'Speak to our team about investment opportunities in Saudi Arabia.';
$cta_text  = $args['cta_text'] ?? 'Talk to Our Team';
$cta_url   = $args['cta_url'] ?? home_url('/contact');
$cta_modal = $args['cta_modal'] ?? false;
?>
<section class="ce-cta">
    <div class="ce-container">
        <h2><?php echo esc_html($title); ?></h2>
        <p><?php echo esc_html($subtitle); ?></p>
        <?php if ($cta_modal): ?>
            <button class="ce-btn ce-btn--gold ce-btn--lg" data-open-modal="register-interest"><?php echo esc_html($cta_text); ?></button>
        <?php else: ?>
            <a href="<?php echo esc_url($cta_url); ?>" class="ce-btn ce-btn--gold ce-btn--lg"><?php echo esc_html($cta_text); ?></a>
        <?php endif; ?>
    </div>
</section>
```

- [ ] **Step 6: Create `template-parts/modal-register-interest.php`**

```php
<?php
/**
 * Template part: Register Interest Modal
 * Included in footer.php. Hidden by default, opened via JS.
 */
?>
<div class="ce-modal" id="modal-register-interest" aria-hidden="true">
    <div class="ce-modal__overlay" data-close-modal></div>
    <div class="ce-modal__content">
        <button class="ce-modal__close" data-close-modal aria-label="Close">&times;</button>
        <h3>Register Your Interest</h3>
        <p>Complete the form below and our team will be in touch within 24 hours.</p>
        <form class="ce-form" id="form-register-interest" data-endpoint="<?php echo esc_url(rest_url('ce/v1/enquiry')); ?>">
            <input type="hidden" name="source" value="register_interest">
            <input type="hidden" name="source_url" value="">
            <div class="ce-form__group">
                <label class="ce-form__label" for="ri-name">Full Name *</label>
                <input class="ce-form__input" type="text" id="ri-name" name="name" required>
            </div>
            <div class="ce-form__group">
                <label class="ce-form__label" for="ri-email">Email *</label>
                <input class="ce-form__input" type="email" id="ri-email" name="email" required>
            </div>
            <div class="ce-form__group">
                <label class="ce-form__label" for="ri-phone">Phone</label>
                <input class="ce-form__input" type="tel" id="ri-phone" name="phone">
            </div>
            <div class="ce-form__group">
                <label class="ce-form__label" for="ri-message">Message</label>
                <textarea class="ce-form__input" id="ri-message" name="message" rows="3"></textarea>
            </div>
            <div class="ce-form__group ce-form__group--checkbox">
                <label>
                    <input type="checkbox" name="gdpr_consent" required>
                    I agree to the <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" target="_blank">Privacy Policy</a> and consent to Crowns Estates processing my data to respond to my enquiry.
                </label>
            </div>
            <button type="submit" class="ce-btn ce-btn--gold">Send Enquiry</button>
            <div class="ce-form__status" aria-live="polite"></div>
        </form>
    </div>
</div>
```

- [ ] **Step 7: Create `template-parts/modal-brochure-gate.php`**

```php
<?php
/**
 * Template part: Brochure Gate Modal
 * Included in single-ce_property.php for gated brochure downloads.
 */
?>
<div class="ce-modal" id="modal-brochure-gate" aria-hidden="true">
    <div class="ce-modal__overlay" data-close-modal></div>
    <div class="ce-modal__content">
        <button class="ce-modal__close" data-close-modal aria-label="Close">&times;</button>
        <h3>Download Brochure</h3>
        <p>Enter your details and we'll send the brochure to your email.</p>
        <form class="ce-form" id="form-brochure-gate" data-endpoint="<?php echo esc_url(rest_url('ce/v1/enquiry')); ?>">
            <input type="hidden" name="source" value="brochure_download">
            <input type="hidden" name="brochure_url" value="">
            <input type="hidden" name="property_interest" value="">
            <input type="hidden" name="source_url" value="">
            <div class="ce-form__group">
                <label class="ce-form__label" for="bg-name">Full Name *</label>
                <input class="ce-form__input" type="text" id="bg-name" name="name" required>
            </div>
            <div class="ce-form__group">
                <label class="ce-form__label" for="bg-email">Email *</label>
                <input class="ce-form__input" type="email" id="bg-email" name="email" required>
            </div>
            <div class="ce-form__group ce-form__group--checkbox">
                <label>
                    <input type="checkbox" name="gdpr_consent" required>
                    I agree to the <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" target="_blank">Privacy Policy</a>.
                </label>
            </div>
            <button type="submit" class="ce-btn ce-btn--gold">Get Brochure</button>
            <div class="ce-form__status" aria-live="polite"></div>
        </form>
    </div>
</div>
```

- [ ] **Step 8: Create `template-parts/whatsapp-button.php`**

```php
<?php
/**
 * Template part: WhatsApp Floating Button
 * Pulls number from ACF options. Hidden if no number set.
 */
$whatsapp = function_exists('get_field') ? get_field('ce_whatsapp_number', 'option') : '';
if (empty($whatsapp)) return;
?>
<a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" class="ce-whatsapp" target="_blank" rel="noopener" aria-label="Chat on WhatsApp" data-ga4-event="whatsapp_click">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>
```

- [ ] **Step 9: Create `template-parts/developer-badge.php`**

```php
<?php
/**
 * Template part: Developer Reliability Badge
 * Args: badge (string — 'verified', 'track_record', 'premium_partner', 'none')
 */
$badge = $args['badge'] ?? 'none';
if ($badge === 'none') return;

$badges = [
    'verified'        => ['label' => 'Verified Developer', 'icon' => '&#128737;'],
    'track_record'    => ['label' => 'Track Record',       'icon' => '&#128737;&#9733;'],
    'premium_partner' => ['label' => 'Premium Partner',    'icon' => '&#128737;&#9813;'],
];

$info = $badges[$badge] ?? null;
if (!$info) return;
?>
<span class="ce-developer-badge ce-developer-badge--<?php echo esc_attr($badge); ?>" title="<?php echo esc_attr($info['label']); ?>">
    <span class="ce-developer-badge__icon"><?php echo $info['icon']; ?></span>
    <span class="ce-developer-badge__label"><?php echo esc_html($info['label']); ?></span>
</span>
```

- [ ] **Step 10: Commit**

```bash
git add wp-content/themes/crowns-estates/template-parts/
git commit -m "feat: reusable template parts — property card, testimonial, hero, trust bar, CTA, modals, WhatsApp, developer badge"
```

---

## Task 7: JavaScript Modules — Calculator, Currency, Modal, Filter, FAQ, GA4

**Files:**
- Create: `wp-content/themes/crowns-estates/js/calculator.js`
- Create: `wp-content/themes/crowns-estates/js/currency-toggle.js`
- Create: `wp-content/themes/crowns-estates/js/modal.js`
- Create: `wp-content/themes/crowns-estates/js/city-filter.js`
- Create: `wp-content/themes/crowns-estates/js/faq-accordion.js`
- Create: `wp-content/themes/crowns-estates/js/ga4-events.js`

- [ ] **Step 1: Create `js/modal.js`**

```js
/**
 * Modal: open/close + form submission via fetch to REST endpoint.
 * Handles both Register Interest and Brochure Gate modals.
 */
(function () {
  'use strict';

  // Open modal
  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-open-modal]');
    if (!trigger) return;
    e.preventDefault();
    var modalId = 'modal-' + trigger.getAttribute('data-open-modal');
    var modal = document.getElementById(modalId);
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    // Set source URL
    var sourceInput = modal.querySelector('input[name="source_url"]');
    if (sourceInput) sourceInput.value = window.location.href;

    // Set property interest + brochure URL for brochure gate
    var propInput = modal.querySelector('input[name="property_interest"]');
    if (propInput && trigger.dataset.propertyName) {
      propInput.value = trigger.dataset.propertyName;
    }
    var brochureInput = modal.querySelector('input[name="brochure_url"]');
    if (brochureInput && trigger.dataset.brochureUrl) {
      brochureInput.value = trigger.dataset.brochureUrl;
    }
  });

  // Close modal
  document.addEventListener('click', function (e) {
    if (!e.target.closest('[data-close-modal]')) return;
    var modal = e.target.closest('.ce-modal');
    if (modal) closeModal(modal);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var open = document.querySelector('.ce-modal[aria-hidden="false"]');
    if (open) closeModal(open);
  });

  function closeModal(modal) {
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // Form submission
  document.addEventListener('submit', function (e) {
    var form = e.target.closest('.ce-form[data-endpoint]');
    if (!form) return;
    e.preventDefault();

    var btn = form.querySelector('button[type="submit"]');
    var status = form.querySelector('.ce-form__status');
    var endpoint = form.getAttribute('data-endpoint');

    btn.disabled = true;
    btn.textContent = 'Sending...';
    if (status) status.textContent = '';

    var data = {};
    new FormData(form).forEach(function (value, key) {
      data[key] = key === 'gdpr_consent' ? true : value;
    });

    fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (json.success) {
          if (status) status.textContent = json.message || 'Thank you!';
          status.classList.add('ce-form__status--success');
          form.reset();

          // Push GA4 event
          var source = data.source || 'enquiry';
          if (window.dataLayer) {
            window.dataLayer.push({
              event: source === 'brochure_download' ? 'brochure_download' : 'enquiry_submit',
            });
          }
        } else {
          if (status) status.textContent = json.error || 'Something went wrong.';
          status.classList.add('ce-form__status--error');
        }
      })
      .catch(function () {
        if (status) status.textContent = 'Network error. Please try again.';
        status.classList.add('ce-form__status--error');
      })
      .finally(function () {
        btn.disabled = false;
        btn.textContent = form.id === 'form-brochure-gate' ? 'Get Brochure' : 'Send Enquiry';
      });
  });
})();
```

- [ ] **Step 2: Create `js/currency-toggle.js`**

```js
/**
 * Currency toggle: reads cookie, fetches rates, recalculates all [data-price] elements.
 * Cache-safe — no server-side cookie reading.
 */
(function () {
  'use strict';

  var toggles = document.querySelectorAll('.ce-currency-toggle__option');
  if (!toggles.length) return;

  var rates = null;

  function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
  }

  function setCookie(name, value) {
    document.cookie = name + '=' + value + ';path=/;max-age=31536000;SameSite=Lax';
  }

  function formatPrice(amount, currency) {
    var symbols = { GBP: '£', SAR: 'SAR ', USD: '$' };
    var symbol = symbols[currency] || currency + ' ';
    return symbol + Math.round(amount).toLocaleString();
  }

  function convert(amount, from, to) {
    if (from === to || !rates) return amount;
    // Convert to SAR first, then to target
    var inSar = amount;
    if (from === 'GBP') inSar = amount * rates.GBP_SAR;
    if (from === 'USD') inSar = amount * rates.USD_SAR;

    if (to === 'SAR') return inSar;
    if (to === 'GBP') return inSar / rates.GBP_SAR;
    if (to === 'USD') return inSar / rates.USD_SAR;
    return amount;
  }

  function updatePrices(targetCurrency) {
    document.querySelectorAll('[data-price]').forEach(function (el) {
      var price = parseFloat(el.getAttribute('data-price'));
      var native = el.getAttribute('data-currency');
      if (!price || !native) return;
      var converted = convert(price, native, targetCurrency);
      el.textContent = formatPrice(converted, targetCurrency);
    });
  }

  function setActive(currency) {
    toggles.forEach(function (t) {
      t.classList.toggle('active', t.textContent.trim() === currency);
    });
  }

  // Fetch rates then apply saved preference
  var restUrl = (window.ceData && window.ceData.restUrl) || '/wp-json/ce/v1/';
  fetch(restUrl + 'rates')
    .then(function (r) { return r.json(); })
    .then(function (data) {
      rates = data;
      var saved = getCookie('ce_currency');
      if (saved && saved !== 'SAR') {
        setActive(saved);
        updatePrices(saved);
      }
    })
    .catch(function () { /* silently fail — prices stay in native currency */ });

  // Toggle click
  toggles.forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      var currency = this.textContent.trim();
      setCookie('ce_currency', currency);
      setActive(currency);
      if (rates) updatePrices(currency);
    });
  });
})();
```

- [ ] **Step 3: Create `js/calculator.js`**

```js
/**
 * Investment cost calculator.
 * Reads rates from ceCalcRates (localized via wp_localize_script).
 */
(function () {
  'use strict';

  var form = document.getElementById('ce-calculator');
  if (!form) return;

  var priceInput = form.querySelector('[name="calc-price"]');
  var output = document.getElementById('ce-calculator-output');
  if (!priceInput || !output) return;

  var rates = window.ceCalcRates || { registration_fee: 2.5, vat: 5, agency_fee: 2 };

  function calculate() {
    var price = parseFloat(priceInput.value) || 0;
    var regFee = price * (rates.registration_fee / 100);
    var vat = price * (rates.vat / 100);
    var agencyFee = price * (rates.agency_fee / 100);
    var total = price + regFee + vat + agencyFee;

    output.querySelector('[data-calc="price"]').textContent = Math.round(price).toLocaleString();
    output.querySelector('[data-calc="reg-fee"]').textContent = Math.round(regFee).toLocaleString();
    output.querySelector('[data-calc="vat"]').textContent = Math.round(vat).toLocaleString();
    output.querySelector('[data-calc="agency-fee"]').textContent = Math.round(agencyFee).toLocaleString();
    output.querySelector('[data-calc="total"]').textContent = Math.round(total).toLocaleString();
  }

  priceInput.addEventListener('input', calculate);
  form.addEventListener('change', calculate);
})();
```

- [ ] **Step 4: Create `js/city-filter.js`**

```js
/**
 * City filter: show/hide property cards by city, developer, and status.
 * No page reload — uses CSS classes.
 */
(function () {
  'use strict';

  var filterBar = document.querySelector('.ce-filter-bar');
  if (!filterBar) return;

  var cards = document.querySelectorAll('.ce-property-card');

  filterBar.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-filter]');
    if (!btn) return;

    var filterType = btn.getAttribute('data-filter-type') || 'city';
    var filterValue = btn.getAttribute('data-filter');

    // Update active state
    btn.closest('.ce-filter-group').querySelectorAll('[data-filter]').forEach(function (b) {
      b.classList.remove('active');
    });
    btn.classList.add('active');

    // Filter cards
    cards.forEach(function (card) {
      var match = filterValue === 'all' || card.getAttribute('data-' + filterType) === filterValue;
      card.style.display = match ? '' : 'none';
    });
  });
})();
```

- [ ] **Step 5: Create `js/faq-accordion.js`**

```js
/**
 * FAQ accordion: click to expand/collapse. One open at a time.
 * Uses aria-expanded for accessibility.
 */
(function () {
  'use strict';

  var items = document.querySelectorAll('.ce-faq__question');
  if (!items.length) return;

  items.forEach(function (question) {
    question.addEventListener('click', function () {
      var isOpen = this.getAttribute('aria-expanded') === 'true';

      // Close all
      items.forEach(function (q) {
        q.setAttribute('aria-expanded', 'false');
        q.nextElementSibling.style.maxHeight = null;
      });

      // Open clicked (if it was closed)
      if (!isOpen) {
        this.setAttribute('aria-expanded', 'true');
        this.nextElementSibling.style.maxHeight = this.nextElementSibling.scrollHeight + 'px';
      }
    });
  });
})();
```

- [ ] **Step 6: Create `js/ga4-events.js`**

```js
/**
 * GA4 custom event tracking via dataLayer.
 * Tracks: WhatsApp clicks, form submissions (handled in modal.js), brochure downloads.
 */
(function () {
  'use strict';

  window.dataLayer = window.dataLayer || [];

  // WhatsApp click
  document.addEventListener('click', function (e) {
    var wa = e.target.closest('[data-ga4-event="whatsapp_click"]');
    if (wa) {
      window.dataLayer.push({ event: 'whatsapp_click' });
    }
  });

  // Contact form (inline on contact page)
  document.addEventListener('submit', function (e) {
    var form = e.target.closest('#form-contact');
    if (form) {
      window.dataLayer.push({ event: 'contact_submit' });
    }
  });
})();
```

- [ ] **Step 7: Commit**

```bash
git add wp-content/themes/crowns-estates/js/
git commit -m "feat: JS modules — modal, currency toggle, calculator, city filter, FAQ accordion, GA4 events"
```

---

## Task 8: Refactor Page Templates to Use Dynamic Queries & Template Parts

**Files:**
- Modify: `wp-content/themes/crowns-estates/front-page.php`
- Modify: `wp-content/themes/crowns-estates/page-projects.php`
- Modify: `wp-content/themes/crowns-estates/page-contact.php`
- Modify: `wp-content/themes/crowns-estates/header.php`
- Modify: `wp-content/themes/crowns-estates/footer.php`

- [ ] **Step 1: Refactor `front-page.php` to use template parts and dynamic WP_Query**

Replace the static hero with `get_template_part('template-parts/hero')`, the static trust bar with `get_template_part('template-parts/trust-bar')`, the static property cards with a `WP_Query` loop over `ce_property` where `ce_featured == true` using `get_template_part('template-parts/property-card')`, the static testimonials with a `WP_Query` loop over `ce_testimonial` where `ce_testimonial_featured == true` using `get_template_part('template-parts/testimonial-card')`, and the static CTA with `get_template_part('template-parts/cta-banner')`.

```php
<?php get_header(); ?>
<main class="ce-main">

    <?php get_template_part('template-parts/hero', null, [
        'label'    => 'Saudi Arabia Property Investment',
        'title'    => 'Connecting Investors with Quality Property Opportunities',
        'subtitle' => 'British expat expertise, 20 years of local knowledge, and end-to-end investor support for the Saudi property market.',
        'cta_text' => 'View Opportunities',
        'cta_url'  => home_url('/projects'),
        'bg_image' => 'https://images.unsplash.com/photo-1578895101408-1a36b834405b?w=1920&q=80',
    ]); ?>

    <?php get_template_part('template-parts/trust-bar'); ?>

    <!-- FEATURED PROPERTIES -->
    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">Featured Opportunities</span>
                <h2>Premium Investment Properties</h2>
                <p class="ce-subtitle">Hand-picked developments offering strong returns and quality living in Saudi Arabia's most sought-after locations.</p>
            </div>

            <div class="ce-grid ce-grid--3">
                <?php
                $featured = new WP_Query([
                    'post_type'      => 'ce_property',
                    'posts_per_page' => 3,
                    'meta_key'       => 'ce_featured',
                    'meta_value'     => '1',
                ]);
                if ($featured->have_posts()):
                    while ($featured->have_posts()): $featured->the_post();
                        get_template_part('template-parts/property-card');
                    endwhile;
                    wp_reset_postdata();
                else:
                    echo '<p>Properties coming soon.</p>';
                endif;
                ?>
            </div>

            <div style="text-align: center; margin-top: var(--ce-space-xl);">
                <a href="<?php echo esc_url(home_url('/projects')); ?>" class="ce-btn ce-btn--outline">View All Properties</a>
            </div>
        </div>
    </section>

    <!-- WHY INVEST -->
    <section class="ce-section ce-section--grey">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">Why Saudi Arabia</span>
                <h2>A Market of Opportunity</h2>
                <p class="ce-subtitle">Saudi Arabia's Vision 2030 is transforming the Kingdom into one of the world's most exciting investment destinations.</p>
            </div>
            <div class="ce-grid ce-grid--3">
                <div class="ce-icon-block">
                    <div class="ce-icon-block__icon">&#8593;</div>
                    <h3>Market Growth</h3>
                    <p>Saudi Arabia's real estate sector is experiencing unprecedented growth driven by Vision 2030, mega-projects, and regulatory reform.</p>
                </div>
                <div class="ce-icon-block">
                    <div class="ce-icon-block__icon">&#9733;</div>
                    <h3>Golden Visa</h3>
                    <p>The Premium Residency programme offers investors long-term residency through qualifying real estate purchases.</p>
                </div>
                <div class="ce-icon-block">
                    <div class="ce-icon-block__icon">&#8962;</div>
                    <h3>Freehold Zones</h3>
                    <p>International investors can now own freehold property in designated zones across Riyadh, Jeddah, and NEOM.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-section__header">
                <span class="ce-label">Investor Testimonials</span>
                <h2>Trusted by Investors Worldwide</h2>
            </div>
            <div class="ce-grid ce-grid--3">
                <?php
                $testimonials = new WP_Query([
                    'post_type'      => 'ce_testimonial',
                    'posts_per_page' => 3,
                    'meta_key'       => 'ce_testimonial_featured',
                    'meta_value'     => '1',
                ]);
                if ($testimonials->have_posts()):
                    while ($testimonials->have_posts()): $testimonials->the_post();
                        get_template_part('template-parts/testimonial-card');
                    endwhile;
                    wp_reset_postdata();
                else:
                    echo '<p>Testimonials coming soon.</p>';
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- ABOUT SNIPPET -->
    <section class="ce-section ce-section--grey">
        <div class="ce-container">
            <div class="ce-two-col" style="align-items: center;">
                <div>
                    <span class="ce-label">About Crowns Estates</span>
                    <h2 style="margin-top: 8px;">Your Trusted Partner in Saudi Property Investment</h2>
                    <p>As British expats who have lived and worked in Saudi Arabia for over 20 years, we bring unparalleled local expertise to international property investors. Our focus is on sourcing high-quality investment opportunities, providing honest advice, and ensuring that every investor feels confident and supported.</p>
                    <a href="<?php echo esc_url(home_url('/about')); ?>" class="ce-btn ce-btn--outline">Learn More About Us</a>
                </div>
                <div style="background: var(--ce-grey-mid); height: 400px; border-radius: var(--ce-border-radius-lg); display: flex; align-items: center; justify-content: center; color: var(--ce-grey-dark);">
                    Team Photo Placeholder
                </div>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/cta-banner', null, [
        'cta_modal' => true,
    ]); ?>

</main>
<?php get_footer(); ?>
```

- [ ] **Step 2: Refactor `page-projects.php` for dynamic property queries with filter bar**

Replace static property cards with `WP_Query` loop. Add filter bar with city tabs from `get_terms('ce_city')`, developer filter, and status filter. Each card uses `get_template_part('template-parts/property-card')`.

```php
<?php get_header(); ?>
<main class="ce-main">

    <?php get_template_part('template-parts/hero', null, [
        'title'    => 'Investment Projects',
        'subtitle' => 'Explore our curated selection of Saudi Arabian property investment opportunities.',
        'label'    => 'Projects',
    ]); ?>

    <section class="ce-section">
        <div class="ce-container">
            <!-- Filter Bar -->
            <div class="ce-filter-bar">
                <div class="ce-filter-group">
                    <button class="ce-filter-btn active" data-filter="all" data-filter-type="city">All Cities</button>
                    <?php
                    $cities = get_terms(['taxonomy' => 'ce_city', 'hide_empty' => true]);
                    if ($cities && !is_wp_error($cities)):
                        foreach ($cities as $city):
                            printf(
                                '<button class="ce-filter-btn" data-filter="%s" data-filter-type="city">%s</button>',
                                esc_attr($city->slug),
                                esc_html($city->name)
                            );
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <!-- Property Grid -->
            <div class="ce-grid ce-grid--3">
                <?php
                $properties = new WP_Query([
                    'post_type'      => 'ce_property',
                    'posts_per_page' => -1,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ]);
                if ($properties->have_posts()):
                    while ($properties->have_posts()): $properties->the_post();
                        get_template_part('template-parts/property-card');
                    endwhile;
                    wp_reset_postdata();
                else:
                    echo '<p>Properties coming soon. Check back shortly.</p>';
                endif;
                ?>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/cta-banner', null, [
        'cta_modal' => true,
    ]); ?>

</main>
<?php get_footer(); ?>
```

- [ ] **Step 3: Refactor `footer.php` to use template parts for WhatsApp and modal**

Replace the hardcoded WhatsApp button with `get_template_part('template-parts/whatsapp-button')` and add the register interest modal include.

```php
<footer class="ce-footer">
    <div class="ce-container">
        <div class="ce-footer__grid">
            <div class="ce-footer__brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="ce-header__logo">
                    CROWNS <span>ESTATES</span>
                </a>
                <p>Connecting UK and global investors with quality property opportunities in Saudi Arabia. 20 years of local expertise and trusted guidance.</p>
            </div>

            <div>
                <h4>Quick Links</h4>
                <div class="ce-footer__links">
                    <a href="<?php echo esc_url(home_url('/projects')); ?>">Projects</a>
                    <a href="<?php echo esc_url(home_url('/how-it-works')); ?>">How It Works</a>
                    <a href="<?php echo esc_url(home_url('/blog')); ?>">Insights</a>
                    <a href="<?php echo esc_url(home_url('/about')); ?>">About Us</a>
                    <a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a>
                </div>
            </div>

            <div>
                <h4>Contact</h4>
                <div class="ce-footer__links">
                    <a href="mailto:info@crownsestates.co.uk">info@crownsestates.co.uk</a>
                    <?php
                    $wa = function_exists('get_field') ? get_field('ce_whatsapp_number', 'option') : '';
                    if ($wa): ?>
                        <a href="https://wa.me/<?php echo esc_attr($wa); ?>" target="_blank" rel="noopener">WhatsApp</a>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <h4>Legal</h4>
                <div class="ce-footer__links">
                    <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a>
                    <a href="<?php echo esc_url(home_url('/terms')); ?>">Terms of Service</a>
                    <a href="<?php echo esc_url(home_url('/disclaimer')); ?>">Disclaimer</a>
                    <a href="<?php echo esc_url(home_url('/cookie-policy')); ?>">Cookie Policy</a>
                </div>
            </div>
        </div>

        <div class="ce-footer__disclaimer">
            Crowns Estates is not regulated by the FCA. Information on this website does not constitute financial advice. Please seek independent advice before making investment decisions.
        </div>

        <div class="ce-footer__bottom">
            <span>&copy; <?php echo date('Y'); ?> Crowns Estates. All rights reserved.</span>
            <div class="ce-footer__legal">
                <span>A UK-registered company</span>
            </div>
        </div>
    </div>
</footer>

<?php get_template_part('template-parts/whatsapp-button'); ?>
<?php get_template_part('template-parts/modal-register-interest'); ?>

<?php wp_footer(); ?>
</body>
</html>
```

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/front-page.php wp-content/themes/crowns-estates/page-projects.php wp-content/themes/crowns-estates/footer.php
git commit -m "refactor: page templates now use dynamic WP_Query, template parts, and REST-driven forms"
```

---

## Task 9: Single Property Page

**Files:**
- Create: `wp-content/themes/crowns-estates/single-ce_property.php`
- Create: `wp-content/themes/crowns-estates/archive-ce_property.php`

- [ ] **Step 1: Create `single-ce_property.php`**

Full property detail page with: gallery, all ACF fields, developer badge, price display, status badge, freehold badge, brochure download (gated or direct), map embed, related testimonials, inline enquiry form, property disclaimer. Include brochure gate modal if `ce_brochure_gated` is true.

```php
<?php get_header(); ?>
<?php the_post(); ?>
<?php
$post_id     = get_the_ID();
$developer   = get_field('ce_developer') ?: '';
$badge       = get_field('ce_developer_badge') ?: 'none';
$type        = get_field('ce_property_type') ?: '';
$status      = get_field('ce_status') ?: '';
$completion  = get_field('ce_completion_date');
$bedrooms    = get_field('ce_bedrooms') ?: '';
$size        = get_field('ce_size_sqm') ?: '';
$freehold    = get_field('ce_is_freehold');
$description = get_field('ce_full_description') ?: '';
$gallery     = get_field('ce_gallery') ?: [];
$brochure    = get_field('ce_brochure_pdf') ?: '';
$gated       = get_field('ce_brochure_gated');
$map_embed   = get_field('ce_map_embed') ?: '';
$city_terms  = get_the_terms($post_id, 'ce_city');
$city_name   = $city_terms ? $city_terms[0]->name : '';

$status_labels = ['off-plan' => 'Off-Plan', 'under-construction' => 'Under Construction', 'ready' => 'Ready'];
$status_colors = ['off-plan' => '#3B82F6', 'under-construction' => '#F59E0B', 'ready' => '#10B981'];
$type_labels   = ['apartment' => 'Apartment', 'villa' => 'Villa', 'commercial' => 'Commercial'];
?>

<main class="ce-main">
    <!-- Gallery -->
    <section class="ce-property-gallery">
        <?php if ($gallery): ?>
            <div class="ce-property-gallery__hero" style="background-image: url('<?php echo esc_url($gallery[0]['url']); ?>');"></div>
            <?php if (count($gallery) > 1): ?>
                <div class="ce-property-gallery__thumbs">
                    <?php foreach (array_slice($gallery, 1, 4) as $img): ?>
                        <div class="ce-property-gallery__thumb" style="background-image: url('<?php echo esc_url($img['sizes']['medium_large'] ?? $img['url']); ?>');"></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="ce-property-gallery__hero" style="background: var(--ce-grey-light); display: flex; align-items: center; justify-content: center; min-height: 400px; color: var(--ce-grey-dark);">Image Coming Soon</div>
        <?php endif; ?>
    </section>

    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-property-detail">
                <!-- Left column: info -->
                <div class="ce-property-detail__main">
                    <h1><?php the_title(); ?></h1>

                    <div class="ce-property-detail__badges">
                        <?php if ($city_name): ?><span class="ce-property-card__badge"><?php echo esc_html($city_name); ?></span><?php endif; ?>
                        <?php if ($status): ?><span class="ce-property-card__status" style="background: <?php echo esc_attr($status_colors[$status] ?? '#666'); ?>;"><?php echo esc_html($status_labels[$status] ?? $status); ?></span><?php endif; ?>
                        <?php if ($freehold): ?><span class="ce-badge ce-badge--freehold">Freehold</span><?php endif; ?>
                    </div>

                    <div class="ce-property-detail__developer">
                        <?php echo esc_html($developer); ?>
                        <?php if ($badge !== 'none'): ?>
                            <?php get_template_part('template-parts/developer-badge', null, ['badge' => $badge]); ?>
                        <?php endif; ?>
                    </div>

                    <div class="ce-property-detail__price">
                        <?php echo ce_display_price($post_id); ?>
                    </div>

                    <!-- Specs table -->
                    <table class="ce-property-specs">
                        <?php if ($type): ?><tr><td>Type</td><td><?php echo esc_html($type_labels[$type] ?? $type); ?></td></tr><?php endif; ?>
                        <?php if ($bedrooms): ?><tr><td>Bedrooms</td><td><?php echo esc_html($bedrooms); ?></td></tr><?php endif; ?>
                        <?php if ($size): ?><tr><td>Size</td><td><?php echo esc_html($size); ?> Sq.M.</td></tr><?php endif; ?>
                        <?php if ($completion): ?><tr><td>Completion</td><td><?php echo esc_html(date('F Y', strtotime($completion))); ?></td></tr><?php endif; ?>
                        <?php if ($freehold !== null): ?><tr><td>Ownership</td><td><?php echo $freehold ? 'Freehold' : 'Leasehold'; ?></td></tr><?php endif; ?>
                    </table>

                    <?php if ($description): ?>
                        <div class="ce-property-detail__description">
                            <?php echo wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($brochure): ?>
                        <?php if ($gated): ?>
                            <button class="ce-btn ce-btn--outline" data-open-modal="brochure-gate" data-property-name="<?php echo esc_attr(get_the_title()); ?>" data-brochure-url="<?php echo esc_url($brochure); ?>">
                                Download Brochure
                            </button>
                        <?php else: ?>
                            <a href="<?php echo esc_url($brochure); ?>" class="ce-btn ce-btn--outline" target="_blank" rel="noopener">Download Brochure</a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($map_embed): ?>
                        <div class="ce-property-detail__map">
                            <h3>Location</h3>
                            <iframe src="<?php echo esc_url($map_embed); ?>" width="100%" height="400" style="border:0; border-radius: var(--ce-border-radius);" allowfullscreen loading="lazy"></iframe>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right column: enquiry form -->
                <div class="ce-property-detail__sidebar">
                    <div class="ce-property-detail__enquiry-card">
                        <h3>Interested in this property?</h3>
                        <p>Register your interest and our team will be in touch within 24 hours.</p>
                        <form class="ce-form" id="form-property-enquiry" data-endpoint="<?php echo esc_url(rest_url('ce/v1/enquiry')); ?>">
                            <input type="hidden" name="source" value="property_page">
                            <input type="hidden" name="property_interest" value="<?php echo esc_attr(get_the_title()); ?>">
                            <input type="hidden" name="source_url" value="<?php echo esc_url(get_permalink()); ?>">
                            <div class="ce-form__group">
                                <input class="ce-form__input" type="text" name="name" placeholder="Full Name *" required>
                            </div>
                            <div class="ce-form__group">
                                <input class="ce-form__input" type="email" name="email" placeholder="Email *" required>
                            </div>
                            <div class="ce-form__group">
                                <input class="ce-form__input" type="tel" name="phone" placeholder="Phone">
                            </div>
                            <div class="ce-form__group">
                                <textarea class="ce-form__input" name="message" rows="3" placeholder="Message"></textarea>
                            </div>
                            <div class="ce-form__group ce-form__group--checkbox">
                                <label>
                                    <input type="checkbox" name="gdpr_consent" required>
                                    I agree to the <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" target="_blank">Privacy Policy</a>.
                                </label>
                            </div>
                            <button type="submit" class="ce-btn ce-btn--gold" style="width: 100%;">Register Interest</button>
                            <div class="ce-form__status" aria-live="polite"></div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Related testimonials -->
            <?php if ($city_terms):
                $related_testimonials = new WP_Query([
                    'post_type'      => 'ce_testimonial',
                    'posts_per_page' => 3,
                    'tax_query'      => [
                        ['taxonomy' => 'ce_city', 'field' => 'term_id', 'terms' => $city_terms[0]->term_id],
                    ],
                ]);
                if ($related_testimonials->have_posts()): ?>
                    <div class="ce-section" style="margin-top: var(--ce-space-xl);">
                        <h2>What Investors Say About <?php echo esc_html($city_name); ?></h2>
                        <div class="ce-grid ce-grid--3">
                            <?php while ($related_testimonials->have_posts()): $related_testimonials->the_post();
                                get_template_part('template-parts/testimonial-card');
                            endwhile;
                            wp_reset_postdata(); ?>
                        </div>
                    </div>
                <?php endif;
            endif; ?>

            <!-- Property disclaimer -->
            <div class="ce-property-disclaimer">
                <p>Prices, specifications, and completion dates are indicative and subject to change. Please contact us for the latest information.</p>
            </div>
        </div>
    </section>
</main>

<?php if ($gated): ?>
    <?php get_template_part('template-parts/modal-brochure-gate'); ?>
<?php endif; ?>

<?php get_footer(); ?>
```

- [ ] **Step 2: Create `archive-ce_property.php`**

Redirects to the Projects page.

```php
<?php
/**
 * Property archive fallback — redirects to Projects page.
 */
wp_safe_redirect(home_url('/projects'), 301);
exit;
```

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/single-ce_property.php wp-content/themes/crowns-estates/archive-ce_property.php
git commit -m "feat: single property detail page with gallery, specs, brochure gate, related testimonials"
```

---

## Task 10: Blog Templates

**Files:**
- Create: `wp-content/themes/crowns-estates/archive.php`
- Create: `wp-content/themes/crowns-estates/single.php`
- Create: `wp-content/themes/crowns-estates/sidebar.php`

- [ ] **Step 1: Create `archive.php`**

Blog listing page with two-column layout (posts + sidebar), post cards with featured image, title, excerpt, date, category badge, "Read More" link, and WordPress pagination.

```php
<?php get_header(); ?>
<main class="ce-main">
    <?php get_template_part('template-parts/hero', null, [
        'title' => 'Insights & Guides',
        'subtitle' => 'Expert articles on Saudi Arabian property investment, market trends, and investor guides.',
        'label' => 'Blog',
    ]); ?>

    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-blog-layout">
                <div class="ce-blog-layout__main">
                    <?php if (have_posts()): ?>
                        <div class="ce-grid ce-grid--2">
                            <?php while (have_posts()): the_post(); ?>
                                <article class="ce-post-card">
                                    <?php if (has_post_thumbnail()): ?>
                                        <a href="<?php the_permalink(); ?>" class="ce-post-card__image" style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')); ?>');"></a>
                                    <?php endif; ?>
                                    <div class="ce-post-card__body">
                                        <?php $cats = get_the_category(); if ($cats): ?>
                                            <span class="ce-post-card__category"><?php echo esc_html($cats[0]->name); ?></span>
                                        <?php endif; ?>
                                        <h3 class="ce-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                        <p class="ce-post-card__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                        <div class="ce-post-card__meta">
                                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                            <a href="<?php the_permalink(); ?>" class="ce-post-card__link">Read More &rarr;</a>
                                        </div>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                        <div class="ce-pagination">
                            <?php the_posts_pagination(['mid_size' => 2, 'prev_text' => '&larr;', 'next_text' => '&rarr;']); ?>
                        </div>
                    <?php else: ?>
                        <p>No articles yet. Check back soon.</p>
                    <?php endif; ?>
                </div>
                <aside class="ce-blog-layout__sidebar">
                    <?php get_sidebar(); ?>
                </aside>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
```

- [ ] **Step 2: Create `single.php`**

Single blog post with two-column layout, post header (title, date, category, read time), content, related posts, and CTA banner.

```php
<?php get_header(); ?>
<?php the_post(); ?>
<main class="ce-main">
    <section class="ce-section">
        <div class="ce-container">
            <div class="ce-blog-layout">
                <article class="ce-blog-layout__main ce-single-post">
                    <header class="ce-single-post__header">
                        <?php $cats = get_the_category(); if ($cats): ?>
                            <span class="ce-post-card__category"><?php echo esc_html($cats[0]->name); ?></span>
                        <?php endif; ?>
                        <h1><?php the_title(); ?></h1>
                        <div class="ce-single-post__meta">
                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                            <span>&bull;</span>
                            <span><?php echo ceil(str_word_count(strip_tags(get_the_content())) / 200); ?> min read</span>
                        </div>
                    </header>

                    <?php if (has_post_thumbnail()): ?>
                        <div class="ce-single-post__featured">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="ce-single-post__content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Related posts -->
                    <?php
                    $related = new WP_Query([
                        'post_type'      => 'post',
                        'posts_per_page' => 3,
                        'post__not_in'   => [get_the_ID()],
                        'category__in'   => wp_get_post_categories(get_the_ID()),
                    ]);
                    if ($related->have_posts()): ?>
                        <div class="ce-related-posts">
                            <h3>Related Articles</h3>
                            <div class="ce-grid ce-grid--3">
                                <?php while ($related->have_posts()): $related->the_post(); ?>
                                    <a href="<?php the_permalink(); ?>" class="ce-related-post">
                                        <h4><?php the_title(); ?></h4>
                                        <time><?php echo get_the_date(); ?></time>
                                    </a>
                                <?php endwhile;
                                wp_reset_postdata(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </article>

                <aside class="ce-blog-layout__sidebar">
                    <?php get_sidebar(); ?>
                </aside>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/cta-banner', null, [
        'cta_modal' => true,
    ]); ?>
</main>
<?php get_footer(); ?>
```

- [ ] **Step 3: Create `sidebar.php`**

```php
<?php
/**
 * Blog sidebar: search, categories, recent posts, CTA card.
 */
?>
<div class="ce-sidebar">
    <!-- Search -->
    <div class="ce-sidebar__widget">
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input class="ce-form__input" type="search" placeholder="Search articles..." name="s" value="<?php echo get_search_query(); ?>">
        </form>
    </div>

    <!-- Categories -->
    <div class="ce-sidebar__widget">
        <h3 class="ce-widget__title">Categories</h3>
        <ul class="ce-sidebar__list">
            <?php wp_list_categories(['title_li' => '', 'show_count' => true]); ?>
        </ul>
    </div>

    <!-- Recent Posts -->
    <div class="ce-sidebar__widget">
        <h3 class="ce-widget__title">Recent Posts</h3>
        <ul class="ce-sidebar__list">
            <?php
            $recent = new WP_Query(['post_type' => 'post', 'posts_per_page' => 5]);
            while ($recent->have_posts()): $recent->the_post(); ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </ul>
    </div>

    <!-- CTA Card -->
    <div class="ce-sidebar__cta">
        <h3>Ready to invest?</h3>
        <p>Speak to our team about Saudi property opportunities.</p>
        <button class="ce-btn ce-btn--gold" data-open-modal="register-interest">Register Interest</button>
    </div>
</div>
```

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/archive.php wp-content/themes/crowns-estates/single.php wp-content/themes/crowns-estates/sidebar.php
git commit -m "feat: blog archive, single post, and sidebar templates"
```

---

## Task 11: Schema Markup & SEO

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/schema-markup.php`
- Create: `wp-content/themes/crowns-estates/inc/ga4-tracking.php`
- Modify: `wp-content/themes/crowns-estates/functions.php` (uncomment includes)

- [ ] **Step 1: Create `inc/schema-markup.php`**

Outputs JSON-LD in `<head>` via `wp_head` action. Includes `RealEstateListing` on property pages, `Review` on testimonial displays, `RealEstateAgent` sitewide, and `FAQPage` on How It Works.

```php
<?php
/**
 * Schema.org JSON-LD structured data.
 */
function ce_output_schema_markup() {
    // Sitewide: RealEstateAgent
    $org = [
        '@context' => 'https://schema.org',
        '@type'    => 'RealEstateAgent',
        'name'     => 'Crowns Estates',
        'url'      => home_url('/'),
        'email'    => 'info@crownsestates.co.uk',
        'description' => 'UK-registered real estate agency specialising in Saudi Arabian property investment.',
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($org, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";

    // Single property: RealEstateListing
    if (is_singular('ce_property')) {
        $post_id = get_the_ID();
        $price   = (float) get_field('ce_price_from', $post_id);
        $currency = get_field('ce_currency', $post_id) ?: 'SAR';
        $city_terms = get_the_terms($post_id, 'ce_city');
        $city = $city_terms ? $city_terms[0]->name : 'Saudi Arabia';
        $image = get_the_post_thumbnail_url($post_id, 'large') ?: '';

        $listing = [
            '@context'    => 'https://schema.org',
            '@type'       => 'RealEstateListing',
            'name'        => get_the_title($post_id),
            'description' => get_field('ce_short_description', $post_id) ?: '',
            'url'         => get_permalink($post_id),
            'datePosted'  => get_the_date('c', $post_id),
        ];
        if ($image) $listing['image'] = $image;
        if ($price) {
            $listing['offers'] = [
                '@type'         => 'Offer',
                'price'         => $price,
                'priceCurrency' => $currency,
            ];
        }
        $listing['contentLocation'] = [
            '@type' => 'Place',
            'name'  => $city . ', Saudi Arabia',
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($listing, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'ce_output_schema_markup', 1);
```

- [ ] **Step 2: Create `inc/ga4-tracking.php`**

Outputs GTM container snippet in head and body.

```php
<?php
/**
 * Google Tag Manager integration.
 * GTM container ID is hardcoded — replace with actual ID when GA4 property is set up.
 */
define('CE_GTM_ID', 'GTM-XXXXXXX'); // Replace with actual GTM container ID

function ce_gtm_head() {
    if (CE_GTM_ID === 'GTM-XXXXXXX') return; // Skip if not configured
    ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo esc_js(CE_GTM_ID); ?>');</script>
    <!-- End Google Tag Manager -->
    <?php
}
add_action('wp_head', 'ce_gtm_head', 1);

function ce_gtm_body() {
    if (CE_GTM_ID === 'GTM-XXXXXXX') return;
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr(CE_GTM_ID); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
}
add_action('wp_body_open', 'ce_gtm_body');
```

- [ ] **Step 3: Uncomment includes in `functions.php`**

```php
require get_template_directory() . '/inc/schema-markup.php';
require get_template_directory() . '/inc/ga4-tracking.php';
```

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/schema-markup.php wp-content/themes/crowns-estates/inc/ga4-tracking.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: Schema.org JSON-LD markup and GTM integration"
```

---

## Task 12: Custom Admin Dashboard

**Files:**
- Create: `wp-content/themes/crowns-estates/inc/admin-dashboard.php`
- Create: `wp-content/themes/crowns-estates/js/admin-dashboard.js`
- Modify: `wp-content/themes/crowns-estates/inc/enqueue.php` (add admin enqueue)
- Modify: `wp-content/themes/crowns-estates/functions.php` (uncomment include)

- [ ] **Step 1: Create `inc/admin-dashboard.php`**

Custom dashboard widget with stat cards, reorganised sidebar, admin branding.

```php
<?php
/**
 * Custom Admin Dashboard & Branded Backend.
 * Reference: griyakita admin screenshots.
 */

// Remove default dashboard widgets
function ce_remove_dashboard_widgets() {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'ce_remove_dashboard_widgets');

// Add custom dashboard widget
function ce_add_dashboard_widgets() {
    wp_add_dashboard_widget('ce_dashboard_overview', 'Crowns Estates Overview', 'ce_dashboard_overview_render');
}
add_action('wp_dashboard_setup', 'ce_add_dashboard_widgets');

function ce_dashboard_overview_render() {
    global $wpdb;
    $total_properties = wp_count_posts('ce_property')->publish ?? 0;
    $enquiries_table = $wpdb->prefix . 'ce_enquiries';
    $total_enquiries = (int) $wpdb->get_var("SELECT COUNT(*) FROM $enquiries_table");
    $total_users = count_users()['total_users'];

    // Calculate total property value
    $property_ids = get_posts(['post_type' => 'ce_property', 'posts_per_page' => -1, 'fields' => 'ids']);
    $total_value = 0;
    foreach ($property_ids as $pid) {
        $total_value += (float) (get_field('ce_price_from', $pid) ?: 0);
    }

    echo '<div class="ce-admin-stats">';
    printf('<div class="ce-admin-stat"><div class="ce-admin-stat__value">%d</div><div class="ce-admin-stat__label">Total Properties</div><canvas class="ce-admin-sparkline" data-type="properties"></canvas></div>', $total_properties);
    printf('<div class="ce-admin-stat"><div class="ce-admin-stat__value">%d</div><div class="ce-admin-stat__label">Enquiries</div><canvas class="ce-admin-sparkline" data-type="enquiries"></canvas></div>', $total_enquiries);
    printf('<div class="ce-admin-stat"><div class="ce-admin-stat__value">%d</div><div class="ce-admin-stat__label">Users</div></div>', $total_users);
    printf('<div class="ce-admin-stat"><div class="ce-admin-stat__value">SAR %s</div><div class="ce-admin-stat__label">Total Property Value</div></div>', number_format($total_value, 0));
    echo '</div>';
}

// Customise admin sidebar
function ce_custom_admin_menu() {
    remove_menu_page('edit-comments.php');
    remove_menu_page('tools.php');

    global $menu;
    foreach ($menu as $key => $item) {
        if (isset($item[2]) && $item[2] === 'edit.php') {
            $menu[$key][0] = 'Blog Posts';
        }
    }
}
add_action('admin_menu', 'ce_custom_admin_menu');

// Admin branding: colour scheme
function ce_admin_styles() {
    echo '<style>
        #adminmenuback, #adminmenuwrap { background: #1a1a1a; }
        #adminmenu .wp-has-current-submenu .wp-submenu-head,
        #adminmenu a.wp-has-current-submenu { background: #C4973A !important; }
        #wpadminbar { background: #0A0A0A; }
        .ce-admin-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin: 16px 0; }
        .ce-admin-stat { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center; }
        .ce-admin-stat__value { font-size: 28px; font-weight: 700; color: #0A0A0A; }
        .ce-admin-stat__label { font-size: 13px; color: #666; margin-top: 4px; }
        .ce-admin-sparkline { width: 100%; height: 40px; margin-top: 8px; }
    </style>';
}
add_action('admin_head', 'ce_admin_styles');

// Custom admin footer
function ce_admin_footer_text() {
    return 'Crowns Estates Admin Panel &mdash; Powered by WordPress';
}
add_filter('admin_footer_text', 'ce_admin_footer_text');
```

- [ ] **Step 2: Create `js/admin-dashboard.js`**

```js
/**
 * Admin dashboard sparkline charts via Chart.js.
 */
(function () {
  'use strict';

  var sparklines = document.querySelectorAll('.ce-admin-sparkline');
  if (!sparklines.length || typeof Chart === 'undefined') return;

  sparklines.forEach(function (canvas) {
    var ctx = canvas.getContext('2d');
    // Placeholder data — in production, pass real data via wp_localize_script
    var data = [3, 5, 2, 8, 6, 4, 7, 9, 5, 11, 8, 12];

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map(function (_, i) { return ''; }),
        datasets: [{
          data: data,
          borderColor: '#C4973A',
          borderWidth: 2,
          fill: true,
          backgroundColor: 'rgba(196, 151, 58, 0.1)',
          tension: 0.4,
          pointRadius: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { display: false }, y: { display: false } },
      }
    });
  });
})();
```

- [ ] **Step 3: Add admin enqueue to `inc/enqueue.php`**

Add at the bottom of `inc/enqueue.php`:

```php
/**
 * Admin scripts — dashboard only.
 */
function ce_admin_enqueue($hook) {
    if ($hook !== 'index.php') return;

    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js', [], '4.0', true);
    wp_enqueue_script('ce-admin-dashboard', get_template_directory_uri() . '/js/admin-dashboard.js', ['chartjs'], '1.0', true);
}
add_action('admin_enqueue_scripts', 'ce_admin_enqueue');
```

- [ ] **Step 4: Uncomment include in `functions.php`**

```php
require get_template_directory() . '/inc/admin-dashboard.php';
```

- [ ] **Step 5: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/admin-dashboard.php wp-content/themes/crowns-estates/js/admin-dashboard.js wp-content/themes/crowns-estates/inc/enqueue.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: custom admin dashboard with stat cards, branded sidebar, sparkline charts"
```

---

## Task 13: 3D Immersive Layer — Core Infrastructure

**Files:**
- Create: `wp-content/themes/crowns-estates/js/3d/scene-manager.js`
- Create: `wp-content/themes/crowns-estates/js/3d/scroll-controller.js`
- Create: `wp-content/themes/crowns-estates/js/3d/particles.js`
- Create: `wp-content/themes/crowns-estates/js/3d/fallback.js`
- Modify: `wp-content/themes/crowns-estates/inc/enqueue.php` (add 3D script enqueue)

- [ ] **Step 1: Create `js/3d/fallback.js`**

WebGL detection. If unsupported or low-power device, sets a CSS class that triggers static fallback styles.

```js
/**
 * WebGL detection and fallback.
 * Sets body class 'ce-no-webgl' if WebGL is unavailable.
 * Sets body class 'ce-low-power' if on mobile/low-memory device.
 */
(function () {
  'use strict';

  function hasWebGL() {
    try {
      var canvas = document.createElement('canvas');
      return !!(window.WebGLRenderingContext && (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
    } catch (e) {
      return false;
    }
  }

  function isLowPower() {
    var nav = navigator;
    if (nav.deviceMemory && nav.deviceMemory < 4) return true;
    if (nav.hardwareConcurrency && nav.hardwareConcurrency < 4) return true;
    if (/Mobi|Android|iPhone/i.test(nav.userAgent) && window.innerWidth < 768) return true;
    return false;
  }

  if (!hasWebGL()) {
    document.body.classList.add('ce-no-webgl');
  }
  if (isLowPower()) {
    document.body.classList.add('ce-low-power');
  }

  window.ceCanUse3D = hasWebGL() && !isLowPower();
  window.ceCanUse3DReduced = hasWebGL() && isLowPower();
})();
```

- [ ] **Step 2: Create `js/3d/scene-manager.js`**

Core Three.js scene setup: renderer, camera, resize handler, render loop. Exports a factory function that page-specific scenes call.

```js
/**
 * Three.js Scene Manager.
 * Creates and manages a WebGL renderer, camera, and render loop for a given container.
 */
window.CeSceneManager = (function () {
  'use strict';

  function create(containerId, options) {
    var container = document.getElementById(containerId);
    if (!container || !window.ceCanUse3D && !window.ceCanUse3DReduced) return null;

    var THREE = window.THREE;
    if (!THREE) return null;

    var width = container.clientWidth;
    var height = container.clientHeight || window.innerHeight;

    // Scene
    var scene = new THREE.Scene();
    scene.background = null; // Transparent — CSS background shows through

    // Camera
    var fov = options.fov || 60;
    var camera = new THREE.PerspectiveCamera(fov, width / height, 0.1, 1000);
    camera.position.set(
      options.cameraX || 0,
      options.cameraY || 2,
      options.cameraZ || 10
    );

    // Renderer
    var renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    container.appendChild(renderer.domElement);

    // Resize
    function onResize() {
      width = container.clientWidth;
      height = container.clientHeight || window.innerHeight;
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      renderer.setSize(width, height);
    }
    window.addEventListener('resize', onResize);

    // Render loop
    var animateCallbacks = [];
    var disposed = false;

    function animate() {
      if (disposed) return;
      requestAnimationFrame(animate);
      animateCallbacks.forEach(function (cb) { cb(); });
      renderer.render(scene, camera);
    }
    animate();

    return {
      scene: scene,
      camera: camera,
      renderer: renderer,
      container: container,
      onAnimate: function (cb) { animateCallbacks.push(cb); },
      dispose: function () {
        disposed = true;
        renderer.dispose();
        window.removeEventListener('resize', onResize);
      }
    };
  }

  return { create: create };
})();
```

- [ ] **Step 3: Create `js/3d/scroll-controller.js`**

GSAP ScrollTrigger + Lenis integration. Initialises smooth scrolling and provides a helper to bind scroll progress to 3D scene properties.

```js
/**
 * Scroll Controller: GSAP ScrollTrigger + Lenis smooth scrolling.
 */
window.CeScrollController = (function () {
  'use strict';

  var lenis = null;

  function init() {
    if (typeof Lenis === 'undefined' || typeof gsap === 'undefined') return;

    lenis = new Lenis({ duration: 1.2, easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); } });

    lenis.on('scroll', function () {
      if (window.ScrollTrigger) ScrollTrigger.update();
    });

    gsap.ticker.add(function (time) {
      lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);
  }

  /**
   * Bind scroll progress to a callback.
   * @param {string} trigger - CSS selector for the trigger element
   * @param {function} onUpdate - callback receiving progress (0-1)
   * @param {object} options - ScrollTrigger options override
   */
  function bind(trigger, onUpdate, options) {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);

    var defaults = {
      trigger: trigger,
      start: 'top bottom',
      end: 'bottom top',
      scrub: 1,
      onUpdate: function (self) { onUpdate(self.progress); },
    };

    return ScrollTrigger.create(Object.assign(defaults, options || {}));
  }

  return { init: init, bind: bind, getLenis: function () { return lenis; } };
})();

// Auto-init on DOM ready
document.addEventListener('DOMContentLoaded', function () {
  CeScrollController.init();
});
```

- [ ] **Step 4: Create `js/3d/particles.js`**

Reusable gold particle system.

```js
/**
 * Gold Particle System.
 * Adds floating gold particles to a Three.js scene.
 */
window.CeParticles = (function () {
  'use strict';

  function create(scene, options) {
    var THREE = window.THREE;
    if (!THREE) return null;

    var count = options.count || 200;
    var spread = options.spread || 20;
    var color = options.color || 0xC4973A;

    var geometry = new THREE.BufferGeometry();
    var positions = new Float32Array(count * 3);
    var velocities = new Float32Array(count * 3);

    for (var i = 0; i < count * 3; i += 3) {
      positions[i] = (Math.random() - 0.5) * spread;
      positions[i + 1] = (Math.random() - 0.5) * spread;
      positions[i + 2] = (Math.random() - 0.5) * spread;
      velocities[i] = (Math.random() - 0.5) * 0.005;
      velocities[i + 1] = Math.random() * 0.005 + 0.002;
      velocities[i + 2] = (Math.random() - 0.5) * 0.005;
    }

    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

    var material = new THREE.PointsMaterial({
      color: color,
      size: options.size || 0.05,
      transparent: true,
      opacity: options.opacity || 0.6,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
    });

    var points = new THREE.Points(geometry, material);
    scene.add(points);

    return {
      mesh: points,
      update: function () {
        var pos = geometry.attributes.position.array;
        for (var i = 0; i < count * 3; i += 3) {
          pos[i] += velocities[i];
          pos[i + 1] += velocities[i + 1];
          pos[i + 2] += velocities[i + 2];
          if (pos[i + 1] > spread / 2) pos[i + 1] = -spread / 2;
        }
        geometry.attributes.position.needsUpdate = true;
      },
      dispose: function () {
        geometry.dispose();
        material.dispose();
        scene.remove(points);
      }
    };
  }

  return { create: create };
})();
```

- [ ] **Step 5: Update `inc/enqueue.php` to load 3D scripts**

Add 3D library enqueuing. Three.js, GSAP, ScrollTrigger, and Lenis loaded from CDN. Page-specific scene scripts loaded conditionally.

Add this block to the `ce_enqueue_assets()` function in `inc/enqueue.php`:

```php
    // 3D Libraries — loaded on all pages (lightweight until scene-specific scripts activate them)
    wp_enqueue_script('threejs', 'https://cdn.jsdelivr.net/npm/three@0.162.0/build/three.min.js', [], '0.162.0', true);
    wp_enqueue_script('gsap', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js', [], '3.12.5', true);
    wp_enqueue_script('gsap-scrolltrigger', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js', ['gsap'], '3.12.5', true);
    wp_enqueue_script('lenis', 'https://cdn.jsdelivr.net/npm/lenis@1.1.1/dist/lenis.min.js', [], '1.1.1', true);

    // 3D Core
    wp_enqueue_script('ce-3d-fallback', get_template_directory_uri() . '/js/3d/fallback.js', [], '1.0', true);
    wp_enqueue_script('ce-3d-scene-manager', get_template_directory_uri() . '/js/3d/scene-manager.js', ['threejs'], '1.0', true);
    wp_enqueue_script('ce-3d-scroll-controller', get_template_directory_uri() . '/js/3d/scroll-controller.js', ['gsap', 'gsap-scrolltrigger', 'lenis'], '1.0', true);
    wp_enqueue_script('ce-3d-particles', get_template_directory_uri() . '/js/3d/particles.js', ['threejs'], '1.0', true);

    // Page-specific 3D scenes
    if (is_front_page()) {
        wp_enqueue_script('ce-3d-hero-scene', get_template_directory_uri() . '/js/3d/hero-scene.js', ['ce-3d-scene-manager', 'ce-3d-scroll-controller', 'ce-3d-particles'], '1.0', true);
    }
    if (is_page('projects')) {
        wp_enqueue_script('ce-3d-projects-map', get_template_directory_uri() . '/js/3d/projects-map.js', ['ce-3d-scene-manager', 'ce-3d-scroll-controller'], '1.0', true);
    }
    if (is_singular('ce_property')) {
        wp_enqueue_script('ce-3d-property-viewer', get_template_directory_uri() . '/js/3d/property-viewer.js', ['ce-3d-scene-manager'], '1.0', true);
    }
    if (is_page('how-it-works')) {
        wp_enqueue_script('ce-3d-journey-scene', get_template_directory_uri() . '/js/3d/journey-scene.js', ['ce-3d-scene-manager', 'ce-3d-scroll-controller', 'ce-3d-particles'], '1.0', true);
    }
```

- [ ] **Step 6: Commit**

```bash
git add wp-content/themes/crowns-estates/js/3d/fallback.js wp-content/themes/crowns-estates/js/3d/scene-manager.js wp-content/themes/crowns-estates/js/3d/scroll-controller.js wp-content/themes/crowns-estates/js/3d/particles.js wp-content/themes/crowns-estates/inc/enqueue.php
git commit -m "feat: 3D infrastructure — scene manager, scroll controller, particles, fallback, CDN enqueue"
```

---

## Task 14: 3D Page Scenes — Hero, Projects Map, Journey, Property Viewer

**Files:**
- Create: `wp-content/themes/crowns-estates/js/3d/hero-scene.js`
- Create: `wp-content/themes/crowns-estates/js/3d/projects-map.js`
- Create: `wp-content/themes/crowns-estates/js/3d/journey-scene.js`
- Create: `wp-content/themes/crowns-estates/js/3d/property-viewer.js`
- Modify: `wp-content/themes/crowns-estates/front-page.php` (add 3D canvas container)
- Modify: `wp-content/themes/crowns-estates/page-projects.php` (add 3D canvas container)
- Modify: `wp-content/themes/crowns-estates/page-how-it-works.php` (add 3D canvas container)

- [ ] **Step 1: Create `js/3d/hero-scene.js`**

Homepage hero 3D scene: procedural cityscape silhouette with gold ambient lighting. Camera orbits on scroll. Gold particle overlay. Until real GLTF models are provided, uses procedural geometry (boxes as building outlines).

```js
/**
 * Homepage Hero 3D Scene.
 * Procedural cityscape with camera orbit on scroll and gold particles.
 * Replaces with GLTF model when skyline.glb is available.
 */
(function () {
  'use strict';
  if (!window.ceCanUse3D && !window.ceCanUse3DReduced) return;

  document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('ce-3d-hero');
    if (!container) return;

    var mgr = CeSceneManager.create('ce-3d-hero', {
      fov: 50, cameraX: 0, cameraY: 3, cameraZ: 12
    });
    if (!mgr) return;

    var THREE = window.THREE;

    // Ambient light
    mgr.scene.add(new THREE.AmbientLight(0xffffff, 0.4));
    var gold = new THREE.DirectionalLight(0xC4973A, 0.8);
    gold.position.set(5, 10, 5);
    mgr.scene.add(gold);

    // Procedural buildings
    var buildingMat = new THREE.MeshStandardMaterial({ color: 0x1a1a1a, metalness: 0.3, roughness: 0.7 });
    for (var i = 0; i < 30; i++) {
      var w = 0.3 + Math.random() * 0.7;
      var h = 1 + Math.random() * 5;
      var d = 0.3 + Math.random() * 0.7;
      var geo = new THREE.BoxGeometry(w, h, d);
      var mesh = new THREE.Mesh(geo, buildingMat);
      mesh.position.set((Math.random() - 0.5) * 20, h / 2, (Math.random() - 0.5) * 10 - 5);
      mgr.scene.add(mesh);
    }

    // Gold particles
    var particles = null;
    if (!window.ceCanUse3DReduced) {
      particles = CeParticles.create(mgr.scene, { count: 150, spread: 25, size: 0.04, opacity: 0.4 });
    }

    // Scroll-driven camera orbit
    CeScrollController.bind('#ce-3d-hero', function (progress) {
      var angle = progress * Math.PI * 0.5;
      mgr.camera.position.x = Math.sin(angle) * 12;
      mgr.camera.position.y = 3 + progress * 2;
      mgr.camera.position.z = Math.cos(angle) * 12;
      mgr.camera.lookAt(0, 2, 0);
    }, { start: 'top top', end: 'bottom top' });

    // Animation
    mgr.onAnimate(function () {
      if (particles) particles.update();
    });
  });
})();
```

- [ ] **Step 2: Create `js/3d/projects-map.js`**

Projects page: 3D plane with city pin markers. Pins pulse with gold glow. Click pin → highlight/filter.

```js
/**
 * Projects Page 3D City Map.
 * Simple 3D plane with pin markers for each city.
 */
(function () {
  'use strict';
  if (!window.ceCanUse3D && !window.ceCanUse3DReduced) return;

  document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('ce-3d-projects-map');
    if (!container) return;

    var mgr = CeSceneManager.create('ce-3d-projects-map', {
      fov: 45, cameraX: 0, cameraY: 8, cameraZ: 8
    });
    if (!mgr) return;

    var THREE = window.THREE;

    // Lights
    mgr.scene.add(new THREE.AmbientLight(0xffffff, 0.5));
    var dirLight = new THREE.DirectionalLight(0xC4973A, 0.6);
    dirLight.position.set(3, 10, 5);
    mgr.scene.add(dirLight);

    // Ground plane
    var ground = new THREE.Mesh(
      new THREE.PlaneGeometry(16, 10),
      new THREE.MeshStandardMaterial({ color: 0x1a1a1a, roughness: 0.9 })
    );
    ground.rotation.x = -Math.PI / 2;
    mgr.scene.add(ground);

    // City pins — positions are approximate relative coords
    var cityData = [
      { name: 'Riyadh', x: 1, z: -1 },
      { name: 'Jeddah', x: -4, z: 1 },
      { name: 'NEOM', x: -5, z: -3 },
      { name: 'AlUla', x: -3, z: -2 },
    ];

    var pinMat = new THREE.MeshStandardMaterial({ color: 0xC4973A, emissive: 0xC4973A, emissiveIntensity: 0.5 });

    cityData.forEach(function (city) {
      var pin = new THREE.Mesh(new THREE.SphereGeometry(0.15, 16, 16), pinMat);
      pin.position.set(city.x, 0.15, city.z);
      pin.userData.city = city.name;
      mgr.scene.add(pin);

      // Pin stem
      var stem = new THREE.Mesh(
        new THREE.CylinderGeometry(0.03, 0.03, 0.3),
        new THREE.MeshStandardMaterial({ color: 0xC4973A })
      );
      stem.position.set(city.x, 0.15, city.z);
      mgr.scene.add(stem);
    });

    // Gentle rotation
    var time = 0;
    mgr.onAnimate(function () {
      time += 0.005;
      mgr.camera.position.x = Math.sin(time * 0.3) * 2;
      mgr.camera.lookAt(0, 0, 0);
    });
  });
})();
```

- [ ] **Step 3: Create `js/3d/journey-scene.js`**

How It Works: 5 floating step cards in 3D space, camera follows a golden path line as user scrolls.

```js
/**
 * How It Works — 3D Journey Path.
 * Camera follows a golden line through 5 floating step cards.
 */
(function () {
  'use strict';
  if (!window.ceCanUse3D && !window.ceCanUse3DReduced) return;

  document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('ce-3d-journey');
    if (!container) return;

    var mgr = CeSceneManager.create('ce-3d-journey', {
      fov: 50, cameraX: 0, cameraY: 2, cameraZ: 8
    });
    if (!mgr) return;

    var THREE = window.THREE;

    mgr.scene.add(new THREE.AmbientLight(0xffffff, 0.6));
    var light = new THREE.PointLight(0xC4973A, 1, 50);
    light.position.set(0, 5, 5);
    mgr.scene.add(light);

    // Golden path curve
    var pathPoints = [
      new THREE.Vector3(-6, 0, 0),
      new THREE.Vector3(-3, 1, -2),
      new THREE.Vector3(0, 2, 0),
      new THREE.Vector3(3, 1, -2),
      new THREE.Vector3(6, 0, 0),
    ];

    var curve = new THREE.CatmullRomCurve3(pathPoints);
    var pathGeo = new THREE.TubeGeometry(curve, 100, 0.02, 8, false);
    var pathMat = new THREE.MeshBasicMaterial({ color: 0xC4973A });
    mgr.scene.add(new THREE.Mesh(pathGeo, pathMat));

    // Step markers
    var markerMat = new THREE.MeshStandardMaterial({ color: 0xC4973A, emissive: 0xC4973A, emissiveIntensity: 0.3 });
    pathPoints.forEach(function (pt) {
      var marker = new THREE.Mesh(new THREE.SphereGeometry(0.15, 16, 16), markerMat);
      marker.position.copy(pt);
      mgr.scene.add(marker);
    });

    // Gold particles
    var particles = null;
    if (!window.ceCanUse3DReduced) {
      particles = CeParticles.create(mgr.scene, { count: 80, spread: 15, size: 0.03, opacity: 0.3 });
    }

    // Scroll-driven camera along path
    CeScrollController.bind('#ce-3d-journey', function (progress) {
      var point = curve.getPoint(progress);
      var lookAhead = curve.getPoint(Math.min(progress + 0.05, 1));
      mgr.camera.position.set(point.x, point.y + 2, point.z + 6);
      mgr.camera.lookAt(lookAhead);
    }, { start: 'top center', end: 'bottom center' });

    mgr.onAnimate(function () {
      if (particles) particles.update();
    });
  });
})();
```

- [ ] **Step 4: Create `js/3d/property-viewer.js`**

Interactive 3D model viewer for single property pages. Loads GLTF model if available, otherwise does nothing. User can rotate/zoom via mouse drag.

```js
/**
 * Single Property 3D Model Viewer.
 * Loads GLTF model from data attribute. User can orbit with mouse.
 * Falls back gracefully if no model URL is provided.
 */
(function () {
  'use strict';
  if (!window.ceCanUse3D) return;

  document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('ce-3d-property-viewer');
    if (!container) return;

    var modelUrl = container.getAttribute('data-model-url');
    if (!modelUrl) return; // No 3D model for this property

    var mgr = CeSceneManager.create('ce-3d-property-viewer', {
      fov: 45, cameraX: 0, cameraY: 2, cameraZ: 5
    });
    if (!mgr) return;

    var THREE = window.THREE;

    mgr.scene.add(new THREE.AmbientLight(0xffffff, 0.6));
    var dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
    dirLight.position.set(5, 10, 5);
    mgr.scene.add(dirLight);

    // Load GLTF
    var loader = new THREE.GLTFLoader();
    loader.load(modelUrl, function (gltf) {
      var model = gltf.scene;
      model.scale.set(1, 1, 1);
      mgr.scene.add(model);
    });

    // Simple orbit on mouse drag
    var isDragging = false;
    var prevX = 0;
    var angle = 0;

    container.addEventListener('mousedown', function (e) { isDragging = true; prevX = e.clientX; });
    window.addEventListener('mouseup', function () { isDragging = false; });
    container.addEventListener('mousemove', function (e) {
      if (!isDragging) return;
      var delta = e.clientX - prevX;
      prevX = e.clientX;
      angle += delta * 0.005;
      mgr.camera.position.x = Math.sin(angle) * 5;
      mgr.camera.position.z = Math.cos(angle) * 5;
      mgr.camera.lookAt(0, 1, 0);
    });
  });
})();
```

- [ ] **Step 5: Add 3D canvas containers to page templates**

Add `<div id="ce-3d-hero" class="ce-3d-canvas"></div>` inside the hero section of `front-page.php`, `<div id="ce-3d-projects-map" class="ce-3d-canvas ce-3d-canvas--map"></div>` to `page-projects.php`, and `<div id="ce-3d-journey" class="ce-3d-canvas ce-3d-canvas--journey"></div>` to `page-how-it-works.php`. The canvas containers sit behind content with `position: absolute` and `z-index: 0`, while content floats above at `z-index: 1`.

Add CSS to `style.css`:

```css
/* 3D Canvas containers */
.ce-3d-canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
}
.ce-3d-canvas canvas {
    pointer-events: auto;
}
.ce-hero {
    position: relative;
}
.ce-hero__content {
    position: relative;
    z-index: 1;
}

/* Fallback: hide 3D canvas if no WebGL */
.ce-no-webgl .ce-3d-canvas {
    display: none;
}
```

- [ ] **Step 6: Commit**

```bash
git add wp-content/themes/crowns-estates/js/3d/hero-scene.js wp-content/themes/crowns-estates/js/3d/projects-map.js wp-content/themes/crowns-estates/js/3d/journey-scene.js wp-content/themes/crowns-estates/js/3d/property-viewer.js wp-content/themes/crowns-estates/front-page.php wp-content/themes/crowns-estates/page-projects.php wp-content/themes/crowns-estates/page-how-it-works.php wp-content/themes/crowns-estates/style.css
git commit -m "feat: 3D page scenes — hero cityscape, projects map, journey path, property viewer"
```

---

## Task 15: Sample Content, Placeholder Images & Screenshot

**Files:**
- Create: `wp-content/themes/crowns-estates/img/placeholder-property.jpg`
- Create: `wp-content/themes/crowns-estates/screenshot.png`
- Create: `wp-content/themes/crowns-estates/sample-content/5-things-uk-investors.md`
- Create: `wp-content/themes/crowns-estates/sample-content/golden-visa-guide.md`
- Create: `wp-content/themes/crowns-estates/sample-content/neom-investment-guide.md`
- Create: `wp-content/themes/crowns-estates/sample-content/understanding-off-plan.md`
- Create: `wp-content/themes/crowns-estates/sample-content/riyadh-vs-jeddah.md`

- [ ] **Step 1: Create placeholder property image**

Generate a 1200x800 SVG placeholder (solid grey with "Crowns Estates" text). Save as `img/placeholder-property.jpg`. Use a simple script or manual creation.

- [ ] **Step 2: Create theme screenshot**

Generate a 1200x900 branded placeholder image for WordPress theme preview. Gold/black/white with "Crowns Estates" text.

- [ ] **Step 3: Write 5 sample blog posts as markdown**

Each ~300 words covering the topic. These are reference content for manual entry into WordPress:

1. `5-things-uk-investors.md` — "5 Things UK Investors Need to Know About Saudi Property in 2026"
2. `golden-visa-guide.md` — "Golden Visa Through Real Estate — A Step-by-Step Guide"
3. `neom-investment-guide.md` — "NEOM Investment Guide — What's Available and What to Expect"
4. `understanding-off-plan.md` — "Understanding Off-Plan Property in Saudi Arabia"
5. `riyadh-vs-jeddah.md` — "Riyadh vs Jeddah — Where Should You Invest?"

- [ ] **Step 4: Commit**

```bash
git add wp-content/themes/crowns-estates/img/ wp-content/themes/crowns-estates/screenshot.png wp-content/themes/crowns-estates/sample-content/
git commit -m "feat: placeholder images, screenshot, and sample blog post content"
```

---

## Task 16: Final Integration, Verification & Push

**Files:**
- Modify: `wp-content/themes/crowns-estates/functions.php` (verify all includes)
- All files from previous tasks

- [ ] **Step 1: Verify all includes in `functions.php` are uncommented**

Final `functions.php` should have these requires (all uncommented):

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
require get_template_directory() . '/inc/admin-dashboard.php';
require get_template_directory() . '/inc/ga4-tracking.php';
```

- [ ] **Step 2: Verify all template parts are referenced correctly**

Check that all `get_template_part()` calls in page templates match the file paths in `template-parts/`.

- [ ] **Step 3: Verify footer includes WhatsApp button and modal**

```php
<?php get_template_part('template-parts/whatsapp-button'); ?>
<?php get_template_part('template-parts/modal-register-interest'); ?>
<?php wp_footer(); ?>
```

- [ ] **Step 4: Update HANDOVER.md with final status**

- [ ] **Step 5: Final commit and push**

```bash
git add -A
git commit -m "feat: final integration — all includes verified, templates connected, ready for WordPress deployment"
git push -u origin main
```

---

## Summary

| Task | Description | Key Files |
|------|-------------|-----------|
| 1 | Refactor functions.php + enqueue module | `functions.php`, `inc/enqueue.php` |
| 2 | Custom post types & taxonomy | `inc/cpt-*.php`, `inc/taxonomy-city.php` |
| 3 | ACF field groups & options | `inc/acf-*.php` |
| 4 | Multi-currency helpers | `inc/currency-helpers.php` |
| 5 | Enquiry handler + brochure gate | `inc/enquiry-handler.php` |
| 6 | Template parts (9 components) | `template-parts/*.php` |
| 7 | JavaScript modules (6 files) | `js/*.js` |
| 8 | Refactor page templates for dynamic content | `front-page.php`, `page-projects.php`, `footer.php` |
| 9 | Single property page | `single-ce_property.php`, `archive-ce_property.php` |
| 10 | Blog templates | `archive.php`, `single.php`, `sidebar.php` |
| 11 | Schema markup + GA4/GTM | `inc/schema-markup.php`, `inc/ga4-tracking.php` |
| 12 | Custom admin dashboard | `inc/admin-dashboard.php`, `js/admin-dashboard.js` |
| 13 | 3D infrastructure (core) | `js/3d/scene-manager.js`, `scroll-controller.js`, `particles.js`, `fallback.js` |
| 14 | 3D page scenes | `js/3d/hero-scene.js`, `projects-map.js`, `journey-scene.js`, `property-viewer.js` |
| 15 | Sample content & assets | `img/`, `screenshot.png`, `sample-content/` |
| 16 | Final integration & push | All files verified |
| **Total** | **16 tasks** | |
