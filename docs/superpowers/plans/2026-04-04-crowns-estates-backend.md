# Crowns Estates Backend Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the full WordPress backend for Crowns Estates — migrating data-critical code into a must-use plugin, upgrading the DB schema, completing the REST API, email system, admin dashboard, multi-currency, GA4/GTM, and schema markup.

**Architecture:** A self-contained must-use plugin (`mu-plugins/crowns-estates-core/`) owns CPTs, taxonomy, DB table, REST API, email, and currency helpers. The theme's `inc/` handles ACF fields, admin dashboard UI, schema markup, and GA4/GTM. All existing theme inc/ files for data logic are removed and replaced by mu-plugin equivalents.

**Tech Stack:** WordPress 6.x, PHP 8.x, ACF Pro, `$wpdb`, WP REST API, `wp_mail()`, WP Cron, Chart.js (admin sparklines), WP Super Cache.

**Spec:** `docs/superpowers/specs/2026-04-04-crowns-estates-backend-design.md`

**Existing state:** All backend code currently lives in `themes/crowns-estates/inc/`. The enquiry handler, CPTs, taxonomy, and currency helpers need migrating to the mu-plugin. DB schema is v1.0 — needs new columns. ACF options, admin dashboard, GA4, and schema markup exist but are incomplete.

---

## File Structure

```
wp-content/
├── mu-plugins/
│   └── crowns-estates-core/
│       ├── crowns-estates-core.php      # NEW — entry point, requires all modules
│       ├── cpt-property.php             # MOVED from theme/inc/
│       ├── cpt-testimonial.php          # MOVED from theme/inc/
│       ├── taxonomy-city.php            # MOVED from theme/inc/
│       ├── db-table.php                 # NEW — wp_ce_enquiries via dbDelta(), replaces theme logic
│       ├── currency-helpers.php         # MOVED + updated from theme/inc/
│       ├── enquiry-handler.php          # REWRITE — nonce, correct schema, brochure-gate endpoint
│       ├── enquiry-admin.php            # NEW — REST: GET /enquiries, GET /enquiries/export, PATCH /enquiries/{id}
│       ├── email-handler.php            # NEW — wp_mail wrapper, cron scheduler
│       └── email-templates/
│           ├── auto-responder.php       # NEW
│           ├── admin-notification.php   # NEW
│           ├── brochure-delivery.php    # NEW
│           └── daily-digest.php         # NEW
│
└── themes/crowns-estates/
    ├── functions.php                    # MODIFY — remove moved requires, add mu-plugin requires
    ├── inc/
    │   ├── cpt-property.php             # DELETE content (leave stub with comment)
    │   ├── cpt-testimonial.php          # DELETE content (leave stub with comment)
    │   ├── taxonomy-city.php            # DELETE content (leave stub with comment)
    │   ├── currency-helpers.php         # DELETE content (leave stub with comment)
    │   ├── enquiry-handler.php          # DELETE content (leave stub with comment)
    │   ├── acf-fields-property.php      # EXISTING — no changes needed
    │   ├── acf-fields-testimonial.php   # EXISTING — no changes needed
    │   ├── acf-options.php              # MODIFY — add missing fields (GTM ID, GA4 ID, trust bar, disclaimers, email settings, digest)
    │   ├── admin-dashboard.php          # REWRITE — stat card row 2, AJAX fetch, enquiry page, user role restrictions
    │   ├── enquiry-admin-page.php       # NEW — admin menu page UI (enquiry list, detail panel, CSV button)
    │   ├── ga4-tracking.php             # MODIFY — read GTM ID from ACF, add server-side dataLayer
    │   └── schema-markup.php            # MODIFY — add Review, FAQPage, Article schemas
    └── js/
        ├── currency-toggle.js           # MODIFY — update to match new /rates response format
        ├── modal.js                     # MODIFY — add nonce to POST requests
        ├── calculator.js                # MODIFY — read rates from ACF options via wp_localize_script
        ├── admin-dashboard.js           # MODIFY — AJAX stat fetch, Chart.js sparklines, status update via PATCH
        └── ga4-events.js               # EXISTING — no changes needed
```

---

## Task 1: Create mu-plugin Scaffolding + Move CPTs & Taxonomy

**Why first:** CPTs and taxonomy must be registered on every request. Moving them to the mu-plugin makes them survive theme changes. All later tasks depend on `ce_property`, `ce_testimonial`, and `ce_city` being available.

**Files:**
- Create: `wp-content/mu-plugins/crowns-estates-core/crowns-estates-core.php`
- Create: `wp-content/mu-plugins/crowns-estates-core/cpt-property.php`
- Create: `wp-content/mu-plugins/crowns-estates-core/cpt-testimonial.php`
- Create: `wp-content/mu-plugins/crowns-estates-core/taxonomy-city.php`
- Modify: `wp-content/themes/crowns-estates/inc/cpt-property.php`
- Modify: `wp-content/themes/crowns-estates/inc/cpt-testimonial.php`
- Modify: `wp-content/themes/crowns-estates/inc/taxonomy-city.php`
- Modify: `wp-content/themes/crowns-estates/functions.php`

- [ ] **Step 1: Create mu-plugin entry point**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/crowns-estates-core.php
/**
 * Plugin Name: Crowns Estates Core
 * Description: Data layer for Crowns Estates — CPTs, taxonomy, DB table, REST API, email system.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

define('CE_CORE_DIR', plugin_dir_path(__FILE__));
define('CE_CORE_VERSION', '1.0.0');

require CE_CORE_DIR . 'cpt-property.php';
require CE_CORE_DIR . 'cpt-testimonial.php';
require CE_CORE_DIR . 'taxonomy-city.php';
require CE_CORE_DIR . 'db-table.php';
require CE_CORE_DIR . 'currency-helpers.php';
require CE_CORE_DIR . 'enquiry-handler.php';
require CE_CORE_DIR . 'enquiry-admin.php';
require CE_CORE_DIR . 'email-handler.php';
```

- [ ] **Step 2: Create mu-plugin CPT property file**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/cpt-property.php
defined('ABSPATH') || exit;

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
    register_post_type('ce_property', [
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'properties'],
        'supports'      => ['title', 'editor', 'thumbnail', 'revisions'],
        'menu_icon'     => 'dashicons-building',
        'show_in_rest'  => true,
        'menu_position' => 5,
    ]);
}
add_action('init', 'ce_register_property_cpt');
```

- [ ] **Step 3: Create mu-plugin CPT testimonial file**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/cpt-testimonial.php
defined('ABSPATH') || exit;

function ce_register_testimonial_cpt() {
    $labels = [
        'name'               => 'Testimonials',
        'singular_name'      => 'Testimonial',
        'menu_name'          => 'Testimonials',
        'add_new_item'       => 'Add New Testimonial',
        'edit_item'          => 'Edit Testimonial',
        'not_found'          => 'No testimonials found',
    ];
    register_post_type('ce_testimonial', [
        'labels'        => $labels,
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'supports'      => ['title'],
        'menu_icon'     => 'dashicons-format-quote',
        'show_in_rest'  => false,
        'menu_position' => 6,
    ]);
}
add_action('init', 'ce_register_testimonial_cpt');
```

- [ ] **Step 4: Create mu-plugin taxonomy file**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/taxonomy-city.php
defined('ABSPATH') || exit;

function ce_register_city_taxonomy() {
    $labels = [
        'name'          => 'Cities',
        'singular_name' => 'City',
        'search_items'  => 'Search Cities',
        'all_items'     => 'All Cities',
        'edit_item'     => 'Edit City',
        'add_new_item'  => 'Add New City',
        'menu_name'     => 'Cities',
    ];
    register_taxonomy('ce_city', ['ce_property', 'ce_testimonial'], [
        'labels'       => $labels,
        'hierarchical' => false,
        'public'       => true,
        'rewrite'      => ['slug' => 'city'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'ce_register_city_taxonomy');
```

- [ ] **Step 5: Stub out the old theme inc/ files so they no longer double-register**

Replace the contents of each with a one-line comment:

`wp-content/themes/crowns-estates/inc/cpt-property.php`:
```php
<?php // Moved to mu-plugins/crowns-estates-core/cpt-property.php
```

`wp-content/themes/crowns-estates/inc/cpt-testimonial.php`:
```php
<?php // Moved to mu-plugins/crowns-estates-core/cpt-testimonial.php
```

`wp-content/themes/crowns-estates/inc/taxonomy-city.php`:
```php
<?php // Moved to mu-plugins/crowns-estates-core/taxonomy-city.php
```

- [ ] **Step 6: Verify — visit WP admin and confirm "Properties" and "Testimonials" appear in sidebar with no PHP errors**

Run: `wp post-type list` (WP-CLI) — expect `ce_property` and `ce_testimonial` in the list.

- [ ] **Step 7: Commit**

```bash
git add wp-content/mu-plugins/ wp-content/themes/crowns-estates/inc/cpt-property.php wp-content/themes/crowns-estates/inc/cpt-testimonial.php wp-content/themes/crowns-estates/inc/taxonomy-city.php
git commit -m "feat: move CPTs and taxonomy to mu-plugin"
```

---

## Task 2: DB Table — Upgrade Schema to v1.1

**Why:** The existing `wp_ce_enquiries` table (v1.0) is missing `property_id`, `status`, and `ip_address` columns required by the spec. `dbDelta()` safely adds columns to existing tables without destroying data.

**Files:**
- Create: `wp-content/mu-plugins/crowns-estates-core/db-table.php`

- [ ] **Step 1: Create db-table.php in mu-plugin**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/db-table.php
defined('ABSPATH') || exit;

define('CE_ENQUIRIES_DB_VERSION', '1.1');

function ce_create_enquiries_table(): void {
    global $wpdb;
    $table   = $wpdb->prefix . 'ce_enquiries';
    $charset = $wpdb->get_charset_collate();

    // dbDelta adds missing columns — never drops existing ones
    $sql = "CREATE TABLE $table (
        id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name         VARCHAR(255) NOT NULL,
        email        VARCHAR(255) NOT NULL,
        phone        VARCHAR(50) DEFAULT NULL,
        message      TEXT DEFAULT NULL,
        property_id  BIGINT UNSIGNED DEFAULT NULL,
        source       VARCHAR(50) NOT NULL DEFAULT 'register_interest',
        gdpr_consent TINYINT(1) NOT NULL DEFAULT 0,
        ip_address   VARCHAR(45) DEFAULT NULL,
        status       VARCHAR(20) NOT NULL DEFAULT 'new',
        created_at   DATETIME NOT NULL,
        PRIMARY KEY  (id),
        KEY email (email),
        KEY status (status),
        KEY created_at (created_at)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('ce_enquiries_db_version', CE_ENQUIRIES_DB_VERSION);
}

// Run on every load if version is outdated
add_action('init', function (): void {
    if (get_option('ce_enquiries_db_version') !== CE_ENQUIRIES_DB_VERSION) {
        ce_create_enquiries_table();
    }
}, 1);
```

- [ ] **Step 2: Verify schema upgrade**

Run via WP-CLI:
```bash
wp db query "DESCRIBE $(wp db prefix)ce_enquiries;"
```
Expected: columns include `property_id`, `status`, `ip_address` alongside existing columns.

- [ ] **Step 3: Commit**

```bash
git add wp-content/mu-plugins/crowns-estates-core/db-table.php
git commit -m "feat: upgrade ce_enquiries DB schema to v1.1 — add property_id, status, ip_address"
```

---

## Task 3: Move Currency Helpers to mu-plugin

**Files:**
- Create: `wp-content/mu-plugins/crowns-estates-core/currency-helpers.php`
- Modify: `wp-content/themes/crowns-estates/inc/currency-helpers.php`

- [ ] **Step 1: Create mu-plugin currency-helpers.php**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/currency-helpers.php
defined('ABSPATH') || exit;

/**
 * Returns exchange rates from ACF options.
 * Keys: GBP, SAR, USD (all relative to GBP as base = 1).
 */
function ce_get_exchange_rates(): array {
    $sar = (float) (function_exists('get_field') ? get_field('ce_rate_gbp_to_sar', 'option') : 0) ?: 4.68;
    $usd = (float) (function_exists('get_field') ? get_field('ce_rate_gbp_to_usd', 'option') : 0) ?: 1.27;
    $updated = function_exists('get_field') ? (get_field('ce_rates_last_updated', 'option') ?: '') : '';
    return [
        'GBP'     => 1,
        'SAR'     => $sar,
        'USD'     => $usd,
        'updated' => $updated,
    ];
}

/**
 * Format a price with currency symbol.
 */
function ce_format_price(float $amount, string $currency): string {
    $symbols = ['GBP' => '£', 'SAR' => 'SAR ', 'USD' => '$'];
    $symbol  = $symbols[$currency] ?? $currency . ' ';
    return $symbol . number_format($amount, 0);
}

/**
 * Render a property price span with data attributes for JS currency conversion.
 * Server-side always outputs native currency — no cookie reads.
 */
function ce_display_price(int $post_id = 0): string {
    $post_id = $post_id ?: get_the_ID();
    $price   = (float) get_field('ce_price_from', $post_id);
    $native  = get_field('ce_currency', $post_id) ?: 'SAR';

    if (!$price) {
        return '<span class="ce-price" data-price="0" data-currency="">Price on request</span>';
    }

    return sprintf(
        '<span class="ce-price" data-price="%s" data-currency="%s">%s</span>',
        esc_attr($price),
        esc_attr($native),
        esc_html(ce_format_price($price, $native))
    );
}

/**
 * REST GET /wp-json/ce/v1/rates
 * Cached via transient for 1 hour. Cache-Control header set.
 */
function ce_register_rates_endpoint(): void {
    register_rest_route('ce/v1', '/rates', [
        'methods'             => 'GET',
        'callback'            => 'ce_rest_get_rates',
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'ce_register_rates_endpoint');

function ce_rest_get_rates(): WP_REST_Response {
    $cached = get_transient('ce_exchange_rates');
    if ($cached !== false) {
        $response = new WP_REST_Response($cached, 200);
        $response->header('Cache-Control', 'max-age=3600');
        return $response;
    }
    $rates = ce_get_exchange_rates();
    set_transient('ce_exchange_rates', $rates, HOUR_IN_SECONDS);
    $response = new WP_REST_Response($rates, 200);
    $response->header('Cache-Control', 'max-age=3600');
    return $response;
}

// Bust transient when exchange rates are saved in ACF options
add_action('acf/save_post', function ($post_id): void {
    if ($post_id === 'options') {
        delete_transient('ce_exchange_rates');
    }
});
```

- [ ] **Step 2: Stub out theme inc/currency-helpers.php**

```php
<?php // Moved to mu-plugins/crowns-estates-core/currency-helpers.php
```

- [ ] **Step 3: Verify rates endpoint**

```bash
curl -s http://localhost/wp-json/ce/v1/rates | python3 -m json.tool
```
Expected:
```json
{"GBP": 1, "SAR": 4.68, "USD": 1.27, "updated": ""}
```

- [ ] **Step 4: Commit**

```bash
git add wp-content/mu-plugins/crowns-estates-core/currency-helpers.php wp-content/themes/crowns-estates/inc/currency-helpers.php
git commit -m "feat: move currency helpers to mu-plugin, add transient caching for /rates endpoint"
```

---

## Task 4: Rewrite Enquiry Handler in mu-plugin

**Why:** The existing handler lacks nonce verification, uses wrong DB field names (`property_interest` instead of `property_id`), has no brochure-gate endpoint, and no signed URL logic.

**Files:**
- Create: `wp-content/mu-plugins/crowns-estates-core/enquiry-handler.php`
- Modify: `wp-content/themes/crowns-estates/inc/enquiry-handler.php`

- [ ] **Step 1: Create mu-plugin enquiry-handler.php**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/enquiry-handler.php
defined('ABSPATH') || exit;

/**
 * REST POST /wp-json/ce/v1/enquiry
 * Handles Register Interest and Contact form submissions.
 */
add_action('rest_api_init', function (): void {
    register_rest_route('ce/v1', '/enquiry', [
        'methods'             => 'POST',
        'callback'            => 'ce_handle_enquiry',
        'permission_callback' => '__return_true',
    ]);
});

function ce_handle_enquiry(WP_REST_Request $request): WP_REST_Response {
    // Nonce verification
    $nonce = $request->get_param('nonce');
    if (!wp_verify_nonce($nonce, 'ce_enquiry_nonce')) {
        return new WP_REST_Response(['success' => false, 'error' => 'Invalid request.'], 403);
    }

    $name        = sanitize_text_field($request->get_param('name') ?? '');
    $email       = sanitize_email($request->get_param('email') ?? '');
    $phone       = sanitize_text_field($request->get_param('phone') ?? '');
    $message     = sanitize_textarea_field($request->get_param('message') ?? '');
    $property_id = (int) $request->get_param('property_id');
    $source      = sanitize_text_field($request->get_param('source') ?? 'register_interest');
    $consent     = (bool) $request->get_param('gdpr_consent');

    // Validation
    if (empty($name)) {
        return new WP_REST_Response(['success' => false, 'error' => 'Name is required.'], 400);
    }
    if (!is_email($email)) {
        return new WP_REST_Response(['success' => false, 'error' => 'A valid email address is required.'], 400);
    }
    if (!$consent) {
        return new WP_REST_Response(['success' => false, 'error' => 'GDPR consent is required.'], 400);
    }
    $allowed_sources = ['register_interest', 'contact_form'];
    if (!in_array($source, $allowed_sources, true)) {
        $source = 'register_interest';
    }

    // Store
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'ce_enquiries',
        [
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'message'      => $message,
            'property_id'  => $property_id ?: null,
            'source'       => $source,
            'gdpr_consent' => 1,
            'ip_address'   => ce_get_client_ip(),
            'status'       => 'new',
            'created_at'   => current_time('mysql', true),
        ],
        ['%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s']
    );

    // Emails
    $property_name = $property_id ? get_the_title($property_id) : '';
    ce_send_auto_responder($name, $email, $property_name);
    ce_send_admin_notification($name, $email, $phone, $message, $source, $property_name);

    return new WP_REST_Response(['success' => true], 200);
}

/**
 * REST POST /wp-json/ce/v1/brochure-gate
 * Gated brochure email capture — validates, stores lead, emails signed download URL.
 */
add_action('rest_api_init', function (): void {
    register_rest_route('ce/v1', '/brochure-gate', [
        'methods'             => 'POST',
        'callback'            => 'ce_handle_brochure_gate',
        'permission_callback' => '__return_true',
    ]);
});

function ce_handle_brochure_gate(WP_REST_Request $request): WP_REST_Response {
    $nonce = $request->get_param('nonce');
    if (!wp_verify_nonce($nonce, 'ce_enquiry_nonce')) {
        return new WP_REST_Response(['success' => false, 'error' => 'Invalid request.'], 403);
    }

    $name        = sanitize_text_field($request->get_param('name') ?? '');
    $email       = sanitize_email($request->get_param('email') ?? '');
    $property_id = (int) $request->get_param('property_id');
    $consent     = (bool) $request->get_param('gdpr_consent');

    if (empty($name) || !is_email($email) || !$property_id || !$consent) {
        return new WP_REST_Response(['success' => false, 'error' => 'All fields are required.'], 400);
    }

    // Verify brochure exists for this property
    $brochure = get_field('ce_brochure_pdf', $property_id);
    if (empty($brochure)) {
        return new WP_REST_Response(['success' => false, 'error' => 'No brochure available for this property.'], 404);
    }

    // Store lead
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'ce_enquiries',
        [
            'name'         => $name,
            'email'        => $email,
            'property_id'  => $property_id,
            'source'       => 'brochure_download',
            'gdpr_consent' => 1,
            'ip_address'   => ce_get_client_ip(),
            'status'       => 'new',
            'created_at'   => current_time('mysql', true),
        ],
        ['%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s']
    );

    // Generate signed 24-hour download URL
    $token   = wp_generate_password(32, false);
    $expires = time() + DAY_IN_SECONDS;
    set_transient('ce_brochure_token_' . $token, ['property_id' => $property_id, 'expires' => $expires], DAY_IN_SECONDS);

    $download_url = add_query_arg([
        'ce_brochure' => $property_id,
        'token'       => $token,
    ], home_url('/'));

    $property_name = get_the_title($property_id);
    ce_send_brochure_delivery($name, $email, $property_name, $download_url);

    return new WP_REST_Response(['success' => true], 200);
}

/**
 * Serve the signed brochure file on template_redirect.
 */
add_action('template_redirect', function (): void {
    if (!isset($_GET['ce_brochure'], $_GET['token'])) {
        return;
    }
    $property_id = (int) $_GET['ce_brochure'];
    $token       = sanitize_text_field($_GET['token']);
    $data        = get_transient('ce_brochure_token_' . $token);

    if (!$data || (int) $data['property_id'] !== $property_id || time() > $data['expires']) {
        wp_die('This brochure link has expired or is invalid.', 'Link Expired', ['response' => 403]);
    }

    $brochure = get_field('ce_brochure_pdf', $property_id);
    $file_url = is_array($brochure) ? $brochure['url'] : $brochure;
    if (empty($file_url)) {
        wp_die('Brochure not found.', 'Not Found', ['response' => 404]);
    }

    wp_redirect($file_url);
    exit;
});

/**
 * Helper: get client IP address.
 */
function ce_get_client_ip(): string {
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = sanitize_text_field(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '';
}
```

- [ ] **Step 2: Stub out theme inc/enquiry-handler.php**

```php
<?php // Moved to mu-plugins/crowns-estates-core/enquiry-handler.php
```

- [ ] **Step 3: Verify POST /ce/v1/enquiry rejects missing nonce**

```bash
curl -s -X POST http://localhost/wp-json/ce/v1/enquiry \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","gdpr_consent":true,"source":"contact_form"}' \
  | python3 -m json.tool
```
Expected: `{"success": false, "error": "Invalid request."}` with HTTP 403.

- [ ] **Step 4: Verify POST /ce/v1/enquiry accepts valid nonce**

Generate a nonce via WP-CLI:
```bash
wp eval 'echo wp_create_nonce("ce_enquiry_nonce");'
```
Then submit with the returned nonce value — expect `{"success": true}` and a new row in `wp_ce_enquiries`.

- [ ] **Step 5: Commit**

```bash
git add wp-content/mu-plugins/crowns-estates-core/enquiry-handler.php wp-content/themes/crowns-estates/inc/enquiry-handler.php
git commit -m "feat: rewrite enquiry handler in mu-plugin — nonce verification, brochure-gate endpoint, signed URLs"
```

---

## Task 5: Email System — Templates, Transport, Digest Cron

**Files:**
- Create: `wp-content/mu-plugins/crowns-estates-core/email-handler.php`
- Create: `wp-content/mu-plugins/crowns-estates-core/email-templates/auto-responder.php`
- Create: `wp-content/mu-plugins/crowns-estates-core/email-templates/admin-notification.php`
- Create: `wp-content/mu-plugins/crowns-estates-core/email-templates/brochure-delivery.php`
- Create: `wp-content/mu-plugins/crowns-estates-core/email-templates/daily-digest.php`

- [ ] **Step 1: Create email-handler.php**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/email-handler.php
defined('ABSPATH') || exit;

/**
 * Send HTML email using a PHP template file.
 *
 * @param string $to       Recipient email.
 * @param string $subject  Email subject.
 * @param string $template Filename in email-templates/ (e.g. 'auto-responder.php').
 * @param array  $vars     Variables to extract into template scope.
 */
function ce_send_email(string $to, string $subject, string $template, array $vars = []): bool {
    $template_path = CE_CORE_DIR . 'email-templates/' . $template;
    if (!file_exists($template_path)) {
        return false;
    }

    extract($vars, EXTR_SKIP);
    ob_start();
    include $template_path;
    $body = ob_get_clean();

    $from_name    = function_exists('get_field') ? (get_field('ce_email_from_name', 'option') ?: 'Crowns Estates') : 'Crowns Estates';
    $from_address = function_exists('get_field') ? (get_field('ce_email_from_address', 'option') ?: 'info@crownsestates.co.uk') : 'info@crownsestates.co.uk';
    $reply_to     = function_exists('get_field') ? (get_field('ce_email_reply_to', 'option') ?: $from_address) : $from_address;

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        "From: {$from_name} <{$from_address}>",
        "Reply-To: {$reply_to}",
    ];

    return wp_mail($to, $subject, $body, $headers);
}

/**
 * Send auto-responder to the person who submitted an enquiry.
 */
function ce_send_auto_responder(string $name, string $email, string $property_name = ''): bool {
    return ce_send_email(
        $to       : $email,
        $subject  : 'Thank you for your enquiry — Crowns Estates',
        $template : 'auto-responder.php',
        $vars     : compact('name', 'property_name')
    );
}

/**
 * Send notification email to the admin inbox.
 */
function ce_send_admin_notification(string $name, string $email, string $phone, string $message, string $source, string $property_name = ''): bool {
    $admin_email = function_exists('get_field') ? (get_field('ce_admin_notification_email', 'option') ?: get_option('admin_email')) : get_option('admin_email');
    return ce_send_email(
        $to       : $admin_email,
        $subject  : "New enquiry: {$name} — {$source}",
        $template : 'admin-notification.php',
        $vars     : compact('name', 'email', 'phone', 'message', 'source', 'property_name')
    );
}

/**
 * Send brochure delivery email with signed download URL.
 */
function ce_send_brochure_delivery(string $name, string $email, string $property_name, string $download_url): bool {
    return ce_send_email(
        $to       : $email,
        $subject  : "Your Crowns Estates brochure — {$property_name}",
        $template : 'brochure-delivery.php',
        $vars     : compact('name', 'property_name', 'download_url')
    );
}

// ─── Daily Digest Cron ───────────────────────────────────────────────────────

add_action('init', 'ce_schedule_digest_cron');

function ce_schedule_digest_cron(): void {
    $enabled = function_exists('get_field') && get_field('ce_digest_enabled', 'option');
    if ($enabled && !wp_next_scheduled('ce_daily_digest_event')) {
        wp_schedule_event(strtotime('today 08:00 UTC'), 'daily', 'ce_daily_digest_event');
    }
    if (!$enabled) {
        wp_clear_scheduled_hook('ce_daily_digest_event');
    }
}

add_action('ce_daily_digest_event', 'ce_send_daily_digest');

function ce_send_daily_digest(): void {
    global $wpdb;
    $since = gmdate('Y-m-d H:i:s', strtotime('-24 hours'));
    $enquiries = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ce_enquiries WHERE status = 'new' AND created_at >= %s ORDER BY created_at DESC",
            $since
        )
    );

    if (empty($enquiries)) {
        return;
    }

    $recipient = function_exists('get_field') ? (get_field('ce_digest_recipient_email', 'option') ?: get_option('admin_email')) : get_option('admin_email');
    $count     = count($enquiries);
    ce_send_email(
        $to       : $recipient,
        $subject  : "Crowns Estates — {$count} new " . ($count === 1 ? 'enquiry' : 'enquiries') . ' today',
        $template : 'daily-digest.php',
        $vars     : ['enquiries' => $enquiries, 'count' => $count]
    );
}
```

- [ ] **Step 2: Create auto-responder.php template**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/email-templates/auto-responder.php
/** @var string $name @var string $property_name */
$site_url = home_url('/');
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Thank you — Crowns Estates</title></head>
<body style="font-family:Arial,sans-serif;color:#0A0A0A;max-width:600px;margin:0 auto;padding:20px">
  <div style="border-top:3px solid #C4973A;padding-top:20px;margin-bottom:24px">
    <h2 style="color:#0A0A0A;margin:0">Crowns Estates</h2>
  </div>
  <p>Dear <?php echo esc_html($name); ?>,</p>
  <p>Thank you for your interest<?php if ($property_name): ?> in <strong><?php echo esc_html($property_name); ?></strong><?php endif; ?>. Our team will be in touch within 24 hours.</p>
  <p>In the meantime, explore our latest opportunities:</p>
  <p><a href="<?php echo esc_url($site_url . 'projects'); ?>" style="background:#C4973A;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block">View All Properties</a></p>
  <hr style="border:none;border-top:1px solid #E0E0E0;margin:24px 0">
  <p style="font-size:12px;color:#666">Crowns Estates · www.crownsestates.co.uk<br>
  This email does not constitute financial advice. Please seek independent advice before making investment decisions.</p>
</body>
</html>
```

- [ ] **Step 3: Create admin-notification.php template**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/email-templates/admin-notification.php
/** @var string $name @var string $email @var string $phone @var string $message @var string $source @var string $property_name */
$admin_url = admin_url('admin.php?page=ce-enquiries');
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>New Enquiry — Crowns Estates</title></head>
<body style="font-family:Arial,sans-serif;color:#0A0A0A;max-width:600px;margin:0 auto;padding:20px">
  <h2 style="border-bottom:2px solid #C4973A;padding-bottom:8px">New Enquiry Received</h2>
  <table style="width:100%;border-collapse:collapse">
    <tr><td style="padding:8px 0;font-weight:bold;width:140px">Name</td><td style="padding:8px 0"><?php echo esc_html($name); ?></td></tr>
    <tr style="background:#f5f5f5"><td style="padding:8px 0;font-weight:bold">Email</td><td style="padding:8px 0"><?php echo esc_html($email); ?></td></tr>
    <tr><td style="padding:8px 0;font-weight:bold">Phone</td><td style="padding:8px 0"><?php echo esc_html($phone ?: '—'); ?></td></tr>
    <tr style="background:#f5f5f5"><td style="padding:8px 0;font-weight:bold">Source</td><td style="padding:8px 0"><?php echo esc_html($source); ?></td></tr>
    <?php if ($property_name): ?>
    <tr><td style="padding:8px 0;font-weight:bold">Property</td><td style="padding:8px 0"><?php echo esc_html($property_name); ?></td></tr>
    <?php endif; ?>
    <?php if ($message): ?>
    <tr style="background:#f5f5f5"><td style="padding:8px 0;font-weight:bold;vertical-align:top">Message</td><td style="padding:8px 0"><?php echo nl2br(esc_html($message)); ?></td></tr>
    <?php endif; ?>
  </table>
  <p style="margin-top:20px"><a href="<?php echo esc_url($admin_url); ?>" style="background:#C4973A;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block">View in Admin</a></p>
</body>
</html>
```

- [ ] **Step 4: Create brochure-delivery.php template**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/email-templates/brochure-delivery.php
/** @var string $name @var string $property_name @var string $download_url */
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Your Brochure — Crowns Estates</title></head>
<body style="font-family:Arial,sans-serif;color:#0A0A0A;max-width:600px;margin:0 auto;padding:20px">
  <div style="border-top:3px solid #C4973A;padding-top:20px;margin-bottom:24px">
    <h2 style="color:#0A0A0A;margin:0">Crowns Estates</h2>
  </div>
  <p>Dear <?php echo esc_html($name); ?>,</p>
  <p>Your brochure for <strong><?php echo esc_html($property_name); ?></strong> is ready to download. This link is valid for 24 hours.</p>
  <p><a href="<?php echo esc_url($download_url); ?>" style="background:#C4973A;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;display:inline-block;font-weight:bold">Download Brochure</a></p>
  <p style="font-size:13px;color:#666">If the button above doesn't work, copy and paste this link into your browser:<br>
  <a href="<?php echo esc_url($download_url); ?>"><?php echo esc_url($download_url); ?></a></p>
  <hr style="border:none;border-top:1px solid #E0E0E0;margin:24px 0">
  <p style="font-size:12px;color:#666">Crowns Estates · www.crownsestates.co.uk<br>
  Prices, specifications, and completion dates are indicative and subject to change.</p>
</body>
</html>
```

- [ ] **Step 5: Create daily-digest.php template**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/email-templates/daily-digest.php
/** @var array $enquiries @var int $count */
$admin_url = admin_url('admin.php?page=ce-enquiries');
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Daily Digest — Crowns Estates</title></head>
<body style="font-family:Arial,sans-serif;color:#0A0A0A;max-width:600px;margin:0 auto;padding:20px">
  <h2 style="border-bottom:2px solid #C4973A;padding-bottom:8px">
    <?php echo $count; ?> New <?php echo $count === 1 ? 'Enquiry' : 'Enquiries'; ?> Today
  </h2>
  <table style="width:100%;border-collapse:collapse;font-size:13px">
    <tr style="background:#f5f5f5">
      <th style="padding:7px 10px;text-align:left">Name</th>
      <th style="padding:7px 10px;text-align:left">Email</th>
      <th style="padding:7px 10px;text-align:left">Source</th>
      <th style="padding:7px 10px;text-align:left">Time</th>
    </tr>
    <?php foreach ($enquiries as $i => $e): ?>
    <tr<?php echo $i % 2 === 0 ? '' : ' style="background:#f9f9f9"'; ?>>
      <td style="padding:7px 10px"><?php echo esc_html($e->name); ?></td>
      <td style="padding:7px 10px"><?php echo esc_html($e->email); ?></td>
      <td style="padding:7px 10px"><?php echo esc_html($e->source); ?></td>
      <td style="padding:7px 10px"><?php echo esc_html(date_i18n('H:i', strtotime($e->created_at))); ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <p style="margin-top:20px"><a href="<?php echo esc_url($admin_url); ?>" style="background:#C4973A;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block">View All Enquiries</a></p>
</body>
</html>
```

- [ ] **Step 6: Verify cron is registered**

```bash
wp cron event list
```
Expected: `ce_daily_digest_event` appears once digest is enabled in ACF options.

- [ ] **Step 7: Commit**

```bash
git add wp-content/mu-plugins/crowns-estates-core/email-handler.php wp-content/mu-plugins/crowns-estates-core/email-templates/
git commit -m "feat: add email system — templates, transport wrapper, daily digest cron"
```

---

## Task 6: Admin REST Endpoints — Enquiry List, CSV Export, Status Update

**Files:**
- Create: `wp-content/mu-plugins/crowns-estates-core/enquiry-admin.php`

- [ ] **Step 1: Create enquiry-admin.php**

```php
<?php
// wp-content/mu-plugins/crowns-estates-core/enquiry-admin.php
defined('ABSPATH') || exit;

add_action('rest_api_init', function (): void {

    // GET /ce/v1/enquiries — paginated list (admin only)
    register_rest_route('ce/v1', '/enquiries', [
        'methods'             => 'GET',
        'callback'            => 'ce_rest_get_enquiries',
        'permission_callback' => function () {
            return current_user_can('manage_options') || current_user_can('edit_posts');
        },
    ]);

    // GET /ce/v1/enquiries/export — CSV download (admin only)
    register_rest_route('ce/v1', '/enquiries/export', [
        'methods'             => 'GET',
        'callback'            => 'ce_rest_export_enquiries',
        'permission_callback' => fn() => current_user_can('manage_options'),
    ]);

    // PATCH /ce/v1/enquiries/{id} — update status (admin + editor)
    register_rest_route('ce/v1', '/enquiries/(?P<id>\d+)', [
        'methods'             => 'POST', // PATCH not always supported; use POST with _method override
        'callback'            => 'ce_rest_update_enquiry',
        'permission_callback' => function () {
            return current_user_can('manage_options') || current_user_can('edit_posts');
        },
        'args' => [
            'id' => ['validate_callback' => fn($v) => is_numeric($v)],
        ],
    ]);
});

function ce_rest_get_enquiries(WP_REST_Request $request): WP_REST_Response {
    global $wpdb;
    $table    = $wpdb->prefix . 'ce_enquiries';
    $page     = max(1, (int) $request->get_param('page'));
    $per_page = min(100, max(1, (int) ($request->get_param('per_page') ?: 20)));
    $offset   = ($page - 1) * $per_page;
    $status   = sanitize_text_field($request->get_param('status') ?? '');
    $search   = sanitize_text_field($request->get_param('search') ?? '');

    $where  = 'WHERE 1=1';
    $params = [];

    if ($status && in_array($status, ['new', 'read', 'replied', 'archived'], true)) {
        $where   .= ' AND status = %s';
        $params[] = $status;
    }
    if ($search) {
        $where   .= ' AND (name LIKE %s OR email LIKE %s)';
        $like     = '%' . $wpdb->esc_like($search) . '%';
        $params[] = $like;
        $params[] = $like;
    }

    $total_query = $params
        ? $wpdb->prepare("SELECT COUNT(*) FROM $table $where", ...$params)
        : "SELECT COUNT(*) FROM $table $where";
    $total = (int) $wpdb->get_var($total_query);

    $data_query = $params
        ? $wpdb->prepare("SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d", ...[...$params, $per_page, $offset])
        : $wpdb->prepare("SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset);
    $rows = $wpdb->get_results($data_query);

    $response = new WP_REST_Response($rows, 200);
    $response->header('X-WP-Total', $total);
    $response->header('X-WP-TotalPages', (int) ceil($total / $per_page));
    return $response;
}

function ce_rest_export_enquiries(): void {
    global $wpdb;
    $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ce_enquiries ORDER BY created_at DESC");

    $filename = 'enquiries-' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Message', 'Source', 'Property ID', 'Status', 'GDPR', 'IP', 'Date']);
    foreach ($rows as $row) {
        fputcsv($output, [
            $row->id,
            $row->name,
            $row->email,
            $row->phone,
            $row->message,
            $row->source,
            $row->property_id,
            $row->status,
            $row->gdpr_consent ? 'Yes' : 'No',
            $row->ip_address,
            $row->created_at,
        ]);
    }
    fclose($output);
    exit;
}

function ce_rest_update_enquiry(WP_REST_Request $request): WP_REST_Response {
    $id     = (int) $request->get_param('id');
    $status = sanitize_text_field($request->get_param('status') ?? '');
    $allowed = ['new', 'read', 'replied', 'archived'];

    if (!in_array($status, $allowed, true)) {
        return new WP_REST_Response(['success' => false, 'error' => 'Invalid status.'], 400);
    }

    global $wpdb;
    $updated = $wpdb->update(
        $wpdb->prefix . 'ce_enquiries',
        ['status' => $status],
        ['id' => $id],
        ['%s'],
        ['%d']
    );

    if ($updated === false) {
        return new WP_REST_Response(['success' => false, 'error' => 'Update failed.'], 500);
    }

    return new WP_REST_Response(['success' => true, 'status' => $status], 200);
}
```

- [ ] **Step 2: Verify list endpoint (requires auth)**

```bash
# Get a nonce from WP admin first, or use basic auth for testing
curl -s "http://localhost/wp-json/ce/v1/enquiries?per_page=5" \
  --cookie "wordpress_logged_in_XXXXX=..." \
  | python3 -m json.tool
```
Expected: JSON array of enquiry objects with `X-WP-Total` header.

- [ ] **Step 3: Commit**

```bash
git add wp-content/mu-plugins/crowns-estates-core/enquiry-admin.php
git commit -m "feat: add admin REST endpoints — enquiry list, CSV export, status update"
```

---

## Task 7: Update ACF Options Pages — Add Missing Fields

**Why:** `acf-options.php` is missing GTM ID, GA4 ID, trust bar texts, footer disclaimer, property disclaimer, from name/address, reply-to, digest settings, rates last updated, and admin notification email.

**Files:**
- Modify: `wp-content/themes/crowns-estates/inc/acf-options.php`

- [ ] **Step 1: Replace acf-options.php with full field set**

```php
<?php
// wp-content/themes/crowns-estates/inc/acf-options.php
defined('ABSPATH') || exit;

add_action('acf/init', function (): void {
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

    acf_add_options_sub_page(['page_title' => 'Exchange Rates',   'menu_title' => 'Exchange Rates',   'parent_slug' => 'ce-site-settings']);
    acf_add_options_sub_page(['page_title' => 'Calculator',       'menu_title' => 'Calculator',       'parent_slug' => 'ce-site-settings']);
    acf_add_options_sub_page(['page_title' => 'Content & Legal',  'menu_title' => 'Content & Legal',  'parent_slug' => 'ce-site-settings']);
});

add_action('acf/init', function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // General Settings
    acf_add_local_field_group([
        'key'    => 'group_ce_general',
        'title'  => 'General Settings',
        'fields' => [
            ['key' => 'field_ce_whatsapp_number',           'label' => 'WhatsApp Number',           'name' => 'ce_whatsapp_number',           'type' => 'text', 'instructions' => 'International format e.g. +447700900000'],
            ['key' => 'field_ce_admin_notification_email',  'label' => 'Admin Notification Email',  'name' => 'ce_admin_notification_email',  'type' => 'email'],
            ['key' => 'field_ce_digest_recipient_email',    'label' => 'Digest Recipient Email',    'name' => 'ce_digest_recipient_email',    'type' => 'email'],
            ['key' => 'field_ce_digest_enabled',            'label' => 'Digest Enabled',            'name' => 'ce_digest_enabled',            'type' => 'true_false', 'default_value' => 1],
            ['key' => 'field_ce_office_address',            'label' => 'Office Address',            'name' => 'ce_office_address',            'type' => 'textarea'],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'ce-site-settings']]],
    ]);

    // Exchange Rates
    acf_add_local_field_group([
        'key'    => 'group_ce_exchange_rates',
        'title'  => 'Exchange Rates',
        'fields' => [
            ['key' => 'field_ce_rate_gbp_to_sar',    'label' => 'GBP to SAR', 'name' => 'ce_rate_gbp_to_sar',    'type' => 'number', 'step' => 0.01, 'default_value' => 4.68],
            ['key' => 'field_ce_rate_gbp_to_usd',    'label' => 'GBP to USD', 'name' => 'ce_rate_gbp_to_usd',    'type' => 'number', 'step' => 0.01, 'default_value' => 1.27],
            ['key' => 'field_ce_rates_last_updated', 'label' => 'Rates Last Updated', 'name' => 'ce_rates_last_updated', 'type' => 'date_picker'],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-exchange-rates']]],
    ]);

    // Calculator
    acf_add_local_field_group([
        'key'    => 'group_ce_calculator',
        'title'  => 'Calculator Settings',
        'fields' => [
            ['key' => 'field_ce_calc_registration_fee', 'label' => 'Registration Fee (%)', 'name' => 'ce_calc_registration_fee', 'type' => 'number', 'step' => 0.1, 'default_value' => 2.5],
            ['key' => 'field_ce_calc_vat',              'label' => 'VAT (%)',               'name' => 'ce_calc_vat',              'type' => 'number', 'step' => 0.1, 'default_value' => 5],
            ['key' => 'field_ce_calc_agency_fee',       'label' => 'Agency Fee (%)',        'name' => 'ce_calc_agency_fee',       'type' => 'number', 'step' => 0.1, 'default_value' => 2],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-calculator-settings']]],
    ]);

    // Content & Legal
    acf_add_local_field_group([
        'key'    => 'group_ce_content_legal',
        'title'  => 'Content & Legal',
        'fields' => [
            ['key' => 'field_ce_email_from_name',       'label' => 'Email From Name',       'name' => 'ce_email_from_name',       'type' => 'text',     'default_value' => 'Crowns Estates'],
            ['key' => 'field_ce_email_from_address',    'label' => 'Email From Address',    'name' => 'ce_email_from_address',    'type' => 'email',    'default_value' => 'info@crownsestates.co.uk'],
            ['key' => 'field_ce_email_reply_to',        'label' => 'Email Reply-To',        'name' => 'ce_email_reply_to',        'type' => 'email'],
            ['key' => 'field_ce_gtm_container_id',      'label' => 'GTM Container ID',      'name' => 'ce_gtm_container_id',      'type' => 'text',     'instructions' => 'e.g. GTM-XXXXXXX'],
            ['key' => 'field_ce_ga4_measurement_id',    'label' => 'GA4 Measurement ID',    'name' => 'ce_ga4_measurement_id',    'type' => 'text',     'instructions' => 'e.g. G-XXXXXXXXXX'],
            ['key' => 'field_ce_trust_bar_1',           'label' => 'Trust Bar Text 1',      'name' => 'ce_trust_bar_1',           'type' => 'text',     'default_value' => '20 Years in Saudi Arabia'],
            ['key' => 'field_ce_trust_bar_2',           'label' => 'Trust Bar Text 2',      'name' => 'ce_trust_bar_2',           'type' => 'text',     'default_value' => 'British Expat Expertise'],
            ['key' => 'field_ce_trust_bar_3',           'label' => 'Trust Bar Text 3',      'name' => 'ce_trust_bar_3',           'type' => 'text',     'default_value' => 'End-to-End Investor Support'],
            ['key' => 'field_ce_footer_disclaimer',     'label' => 'Footer Disclaimer',     'name' => 'ce_footer_disclaimer',     'type' => 'textarea', 'default_value' => 'Crowns Estates is not regulated by the FCA. Information on this website does not constitute financial advice. Please seek independent advice before making investment decisions.'],
            ['key' => 'field_ce_property_disclaimer',   'label' => 'Property Disclaimer',   'name' => 'ce_property_disclaimer',   'type' => 'textarea', 'default_value' => 'Prices, specifications, and completion dates are indicative and subject to change.'],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-content-legal']]],
    ]);
});
```

- [ ] **Step 2: Verify — visit WP Admin → Site Settings and confirm all four sub-pages appear with correct fields**

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/acf-options.php
git commit -m "feat: expand ACF options pages — GTM/GA4 IDs, trust bar, disclaimers, email settings, digest toggle"
```

---

## Task 8: Admin Dashboard — Stat Cards, Enquiry Page, User Role Restrictions

**Files:**
- Modify: `wp-content/themes/crowns-estates/inc/admin-dashboard.php`
- Create: `wp-content/themes/crowns-estates/inc/enquiry-admin-page.php`
- Modify: `wp-content/themes/crowns-estates/functions.php`

- [ ] **Step 1: Rewrite admin-dashboard.php**

```php
<?php
// wp-content/themes/crowns-estates/inc/admin-dashboard.php
defined('ABSPATH') || exit;

require get_template_directory() . '/inc/enquiry-admin-page.php';

// ─── User Role Restrictions ──────────────────────────────────────────────────

add_filter('user_has_cap', function (array $allcaps, array $caps): array {
    $user = wp_get_current_user();
    // Strip plugin/theme management from the client admin account
    // Developer accounts identified by email domain — adjust as needed
    if (in_array('administrator', $user->roles, true) && !str_ends_with($user->user_email, '@3dvisualpro.com')) {
        $restricted = ['install_plugins', 'activate_plugins', 'update_plugins', 'delete_plugins', 'edit_plugins', 'install_themes', 'edit_themes', 'update_themes', 'delete_themes', 'update_core'];
        foreach ($restricted as $cap) {
            unset($allcaps[$cap]);
        }
    }
    return $allcaps;
}, 10, 2);

// ─── Remove Default Widgets ──────────────────────────────────────────────────

add_action('wp_dashboard_setup', function (): void {
    $to_remove = ['dashboard_quick_press', 'dashboard_primary', 'dashboard_secondary', 'dashboard_site_health', 'dashboard_activity', 'dashboard_right_now', 'dashboard_recent_comments'];
    foreach ($to_remove as $widget) {
        remove_meta_box($widget, 'dashboard', 'normal');
        remove_meta_box($widget, 'dashboard', 'side');
    }
    wp_add_dashboard_widget('ce_dashboard_main', 'Crowns Estates', 'ce_render_dashboard_widget');
});

// ─── Dashboard Widget ────────────────────────────────────────────────────────

function ce_render_dashboard_widget(): void {
    global $wpdb;
    $table            = $wpdb->prefix . 'ce_enquiries';
    $total_properties = wp_count_posts('ce_property')->publish ?? 0;
    $active_listings  = (int) wp_count_posts('ce_property')->publish;
    $total_enquiries  = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $new_enquiries    = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'new'");

    // Property value (Active listings only)
    $ids = get_posts(['post_type' => 'ce_property', 'posts_per_page' => -1, 'fields' => 'ids', 'post_status' => 'publish']);
    $total_value = array_sum(array_map(fn($id) => (float) (get_field('ce_price_from', $id) ?: 0), $ids));

    // Enquiries by source
    $by_source = $wpdb->get_results("SELECT source, COUNT(*) as count FROM $table GROUP BY source");
    $source_map = [];
    foreach ($by_source as $row) {
        $source_map[$row->source] = (int) $row->count;
    }

    // 30-day sparkline data
    $sparkline = $wpdb->get_results(
        "SELECT DATE(created_at) as day, COUNT(*) as count
         FROM $table
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
         GROUP BY DATE(created_at)
         ORDER BY day ASC"
    );
    $sparkline_data = array_map(fn($r) => ['day' => $r->day, 'count' => (int) $r->count], $sparkline);

    $new_badge = $new_enquiries > 0 ? "<span style='background:#fee2e2;color:#991b1b;padding:2px 7px;border-radius:3px;font-size:11px;margin-left:6px'>{$new_enquiries} new</span>" : '';
    $admin_url = admin_url('admin.php?page=ce-enquiries');
    ?>
    <div class="ce-admin-stats">
        <div class="ce-admin-stat" style="border-top-color:#C4973A">
            <div class="ce-admin-stat__value"><?php echo $total_properties; ?></div>
            <div class="ce-admin-stat__label">Total Properties</div>
        </div>
        <div class="ce-admin-stat" style="border-top-color:#22c55e">
            <div class="ce-admin-stat__value"><?php echo $active_listings; ?></div>
            <div class="ce-admin-stat__label">Active Listings</div>
        </div>
        <div class="ce-admin-stat" style="border-top-color:#3b82f6">
            <div class="ce-admin-stat__value"><?php echo $total_enquiries; ?><?php echo $new_badge; ?></div>
            <div class="ce-admin-stat__label">Total Enquiries</div>
        </div>
        <div class="ce-admin-stat" style="border-top-color:#f59e0b">
            <div class="ce-admin-stat__value">£<?php echo number_format($total_value, 0); ?></div>
            <div class="ce-admin-stat__label">Total Property Value</div>
        </div>
    </div>
    <div class="ce-admin-row2">
        <div class="ce-admin-chart-box">
            <div class="ce-admin-chart-title">Enquiries — Last 30 Days</div>
            <canvas id="ce-enquiries-sparkline" height="80"></canvas>
        </div>
        <div class="ce-admin-source-box">
            <div class="ce-admin-chart-title">By Source</div>
            <?php foreach (['register_interest' => 'Register Interest', 'contact_form' => 'Contact Form', 'brochure_download' => 'Brochure Gate'] as $key => $label): ?>
            <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px">
                <span><?php echo $label; ?></span>
                <strong><?php echo $source_map[$key] ?? 0; ?></strong>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="ce-admin-actions-box">
            <div class="ce-admin-chart-title">Quick Actions</div>
            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=ce_property')); ?>" class="ce-admin-action-link">+ Add Property</a>
            <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="ce-admin-action-link">+ Add Blog Post</a>
            <a href="<?php echo esc_url($admin_url); ?>" class="ce-admin-action-link">View Enquiries <?php if ($new_enquiries): ?>(<?php echo $new_enquiries; ?> new)<?php endif; ?></a>
            <?php if (current_user_can('manage_options')): ?>
            <a href="<?php echo esc_url(rest_url('ce/v1/enquiries/export')); ?>" class="ce-admin-action-link ce-admin-action-link--gold">↓ Export CSV</a>
            <?php endif; ?>
        </div>
    </div>
    <script>
    (function(){
        var data = <?php echo wp_json_encode($sparkline_data); ?>;
        document.addEventListener('DOMContentLoaded', function(){
            if (typeof Chart === 'undefined') return;
            var labels = data.map(function(d){ return d.day; });
            var counts = data.map(function(d){ return d.count; });
            new Chart(document.getElementById('ce-enquiries-sparkline'), {
                type: 'line',
                data: { labels: labels, datasets: [{ data: counts, borderColor: '#C4973A', backgroundColor: 'rgba(196,151,58,0.1)', tension: 0.3, pointRadius: 0, fill: true }] },
                options: { plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false, beginAtZero: true } } }
            });
        });
    })();
    </script>
    <?php
}

// ─── Admin Menu + Branding ───────────────────────────────────────────────────

add_action('admin_menu', function (): void {
    remove_menu_page('edit-comments.php');
    remove_menu_page('tools.php');
    global $menu;
    foreach ($menu as $key => $item) {
        if (isset($item[2]) && $item[2] === 'edit.php') {
            $menu[$key][0] = 'Blog Posts';
        }
    }
});

add_action('admin_enqueue_scripts', function (): void {
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js', [], null, true);
});

add_action('admin_head', function (): void {
    ?>
    <style>
        #adminmenuback,#adminmenuwrap{background:#1a1a1a}
        #adminmenu .wp-has-current-submenu .wp-submenu-head,#adminmenu a.wp-has-current-submenu{background:#C4973A!important}
        #wpadminbar{background:#0A0A0A}
        .ce-admin-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:0 0 16px}
        .ce-admin-stat{background:#fff;border:1px solid #e0e0e0;border-top:3px solid #ccc;border-radius:4px;padding:16px;text-align:center}
        .ce-admin-stat__value{font-size:26px;font-weight:700;color:#0A0A0A}
        .ce-admin-stat__label{font-size:12px;color:#666;margin-top:4px}
        .ce-admin-row2{display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px;margin-top:4px}
        .ce-admin-chart-box,.ce-admin-source-box,.ce-admin-actions-box{background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:14px}
        .ce-admin-chart-title{font-size:11px;font-weight:700;text-transform:uppercase;color:#666;margin-bottom:10px}
        .ce-admin-action-link{display:block;padding:6px 0;color:#C4973A;font-size:13px;text-decoration:none;border-bottom:1px solid #f5f5f5}
        .ce-admin-action-link--gold{margin-top:6px;background:#C4973A;color:#fff!important;padding:6px 12px;border-radius:3px;border:none}
        @media(max-width:1200px){.ce-admin-stats{grid-template-columns:repeat(2,1fr)}.ce-admin-row2{grid-template-columns:1fr}}
    </style>
    <?php
});

add_filter('admin_footer_text', fn() => 'Crowns Estates Admin Panel &mdash; Built by 3D Visual Pro');
```

- [ ] **Step 2: Create enquiry-admin-page.php**

```php
<?php
// wp-content/themes/crowns-estates/inc/enquiry-admin-page.php
defined('ABSPATH') || exit;

add_action('admin_menu', function (): void {
    add_menu_page(
        'Enquiries',
        'Enquiries',
        'edit_posts',
        'ce-enquiries',
        'ce_render_enquiries_admin_page',
        'dashicons-email-alt',
        25
    );
});

function ce_render_enquiries_admin_page(): void {
    global $wpdb;
    $table   = $wpdb->prefix . 'ce_enquiries';
    $status  = sanitize_text_field($_GET['status'] ?? '');
    $search  = sanitize_text_field($_GET['search'] ?? '');
    $paged   = max(1, (int) ($_GET['paged'] ?? 1));
    $per     = 20;
    $offset  = ($paged - 1) * $per;

    $where  = 'WHERE 1=1';
    $params = [];
    if ($status && in_array($status, ['new', 'read', 'replied', 'archived'], true)) {
        $where .= ' AND status = %s'; $params[] = $status;
    }
    if ($search) {
        $where .= ' AND (name LIKE %s OR email LIKE %s)';
        $like = '%' . $wpdb->esc_like($search) . '%'; $params[] = $like; $params[] = $like;
    }

    $total_query  = $params ? $wpdb->prepare("SELECT COUNT(*) FROM $table $where", ...$params) : "SELECT COUNT(*) FROM $table $where";
    $total        = (int) $wpdb->get_var($total_query);
    $data_query   = $params
        ? $wpdb->prepare("SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d", ...[...$params, $per, $offset])
        : $wpdb->prepare("SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d", $per, $offset);
    $enquiries    = $wpdb->get_results($data_query);

    $counts = $wpdb->get_results("SELECT status, COUNT(*) as n FROM $table GROUP BY status");
    $count_map = ['new' => 0, 'read' => 0, 'replied' => 0, 'archived' => 0];
    foreach ($counts as $c) $count_map[$c->status] = (int) $c->n;
    $total_all = array_sum($count_map);

    $base_url = admin_url('admin.php?page=ce-enquiries');
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Enquiries</h1>
        <?php if (current_user_can('manage_options')): ?>
        <a href="<?php echo esc_url(rest_url('ce/v1/enquiries/export')); ?>" class="page-title-action" style="background:#C4973A;color:#fff;border-color:#C4973A">↓ Export CSV</a>
        <?php endif; ?>

        <ul class="subsubsub" style="margin:12px 0">
            <?php
            $tabs = ['All' => $total_all, 'New' => $count_map['new'], 'Read' => $count_map['read'], 'Replied' => $count_map['replied'], 'Archived' => $count_map['archived']];
            $tab_keys = ['All' => '', 'New' => 'new', 'Read' => 'read', 'Replied' => 'replied', 'Archived' => 'archived'];
            $last = array_key_last($tabs);
            foreach ($tabs as $label => $count):
                $active = ($tab_keys[$label] === $status || ($label === 'All' && $status === ''));
                $url    = $tab_keys[$label] ? add_query_arg('status', $tab_keys[$label], $base_url) : $base_url;
            ?>
            <li><a href="<?php echo esc_url($url); ?>"<?php echo $active ? ' class="current"' : ''; ?>><?php echo $label; ?> <span class="count">(<?php echo $count; ?>)</span></a><?php echo $label !== $last ? ' | ' : ''; ?></li>
            <?php endforeach; ?>
        </ul>

        <form method="get" style="margin-bottom:16px">
            <input type="hidden" name="page" value="ce-enquiries">
            <?php if ($status): ?><input type="hidden" name="status" value="<?php echo esc_attr($status); ?>"><?php endif; ?>
            <input type="search" name="search" value="<?php echo esc_attr($search); ?>" placeholder="Search by name or email" style="padding:4px 8px;width:280px">
            <button type="submit" class="button">Search</button>
            <?php if ($search): ?><a href="<?php echo esc_url($base_url . ($status ? '&status=' . $status : '')); ?>" class="button">Clear</a><?php endif; ?>
        </form>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Phone</th><th>Source</th><th>Property</th><th>Status</th><th>Date</th>
                </tr>
            </thead>
            <tbody id="ce-enquiries-tbody">
            <?php if ($enquiries): foreach ($enquiries as $e):
                $prop_name = $e->property_id ? get_the_title((int) $e->property_id) : '—';
                $status_colours = ['new' => '#fee2e2;color:#991b1b', 'read' => '#f5f5f5;color:#444', 'replied' => '#dcfce7;color:#166534', 'archived' => '#e5e7eb;color:#666'];
                $badge_style = $status_colours[$e->status] ?? '#f5f5f5;color:#444';
            ?>
            <tr data-id="<?php echo esc_attr($e->id); ?>" style="cursor:pointer" onclick="ceToggleDetail(this)">
                <td><strong><?php echo esc_html($e->name); ?></strong></td>
                <td><?php echo esc_html($e->email); ?></td>
                <td><?php echo esc_html($e->phone ?: '—'); ?></td>
                <td><?php echo esc_html($e->source); ?></td>
                <td><?php echo esc_html($prop_name); ?></td>
                <td>
                    <span class="ce-status-badge" style="background:<?php echo $badge_style; ?>;padding:2px 8px;border-radius:3px;font-size:11px">
                        <?php echo esc_html(ucfirst($e->status)); ?>
                    </span>
                    <?php if (current_user_can('edit_posts')): ?>
                    <select class="ce-status-select" data-id="<?php echo esc_attr($e->id); ?>" style="font-size:11px;margin-left:6px" onclick="event.stopPropagation()">
                        <?php foreach (['new', 'read', 'replied', 'archived'] as $s): ?>
                        <option value="<?php echo $s; ?>"<?php selected($e->status, $s); ?>><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </td>
                <td><?php echo esc_html(date_i18n('d M Y', strtotime($e->created_at))); ?></td>
            </tr>
            <tr class="ce-detail-row" id="ce-detail-<?php echo esc_attr($e->id); ?>" style="display:none;background:#fffbf0">
                <td colspan="7" style="padding:16px 20px">
                    <strong>Message:</strong> <?php echo nl2br(esc_html($e->message ?: '—')); ?><br>
                    <small style="color:#666;margin-top:6px;display:block">GDPR: <?php echo $e->gdpr_consent ? '✓ Consented' : '✗ No consent'; ?> · IP: <?php echo esc_html($e->ip_address ?: '—'); ?> · Submitted: <?php echo esc_html($e->created_at); ?></small>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="7">No enquiries found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total > $per): ?>
        <div class="tablenav bottom" style="margin-top:12px">
            <?php
            $total_pages = ceil($total / $per);
            for ($p = 1; $p <= $total_pages; $p++):
                $page_url = add_query_arg('paged', $p, $base_url . ($status ? '&status=' . $status : '') . ($search ? '&search=' . urlencode($search) : ''));
            ?>
            <a href="<?php echo esc_url($page_url); ?>" class="button<?php echo $p === $paged ? ' button-primary' : ''; ?>" style="margin-right:2px"><?php echo $p; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function ceToggleDetail(row) {
        var id = row.getAttribute('data-id');
        var detail = document.getElementById('ce-detail-' + id);
        if (detail) detail.style.display = detail.style.display === 'none' ? 'table-row' : 'none';
    }
    document.querySelectorAll('.ce-status-select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var id     = this.getAttribute('data-id');
            var status = this.value;
            var nonce  = '<?php echo wp_create_nonce('wp_rest'); ?>';
            fetch('<?php echo esc_url(rest_url('ce/v1/enquiries/')); ?>' + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ status: status })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (!data.success) alert('Update failed.');
            });
        });
    });
    </script>
    <?php
}
```

- [ ] **Step 3: Add enquiry-admin-page require to functions.php**

In `functions.php`, the `admin-dashboard.php` already requires `enquiry-admin-page.php` internally. Ensure `functions.php` includes only these theme inc/ files (remove stubs for moved files):

```php
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/acf-fields-property.php';
require get_template_directory() . '/inc/acf-fields-testimonial.php';
require get_template_directory() . '/inc/acf-options.php';
require get_template_directory() . '/inc/schema-markup.php';
require get_template_directory() . '/inc/admin-dashboard.php';
require get_template_directory() . '/inc/ga4-tracking.php';
// Stubs (do not remove — mu-plugin handles these):
require get_template_directory() . '/inc/cpt-property.php';
require get_template_directory() . '/inc/cpt-testimonial.php';
require get_template_directory() . '/inc/taxonomy-city.php';
require get_template_directory() . '/inc/currency-helpers.php';
require get_template_directory() . '/inc/enquiry-handler.php';
```

- [ ] **Step 4: Verify — visit WP Admin → Dashboard. Confirm 4 stat cards render, 3 quick action links appear, "Enquiries" menu item is visible**

- [ ] **Step 5: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/admin-dashboard.php wp-content/themes/crowns-estates/inc/enquiry-admin-page.php wp-content/themes/crowns-estates/functions.php
git commit -m "feat: rewrite admin dashboard — stat cards, sparkline, enquiries page with status update and CSV export"
```

---

## Task 9: Update GA4/GTM — Read from ACF, Add Server-Side dataLayer

**Files:**
- Modify: `wp-content/themes/crowns-estates/inc/ga4-tracking.php`

- [ ] **Step 1: Rewrite ga4-tracking.php**

```php
<?php
// wp-content/themes/crowns-estates/inc/ga4-tracking.php
defined('ABSPATH') || exit;

/**
 * Get GTM container ID from ACF options (falls back to empty — suppresses snippet).
 */
function ce_get_gtm_id(): string {
    if (!function_exists('get_field')) return '';
    return sanitize_text_field(get_field('ce_gtm_container_id', 'option') ?: '');
}

/**
 * Output GTM <head> snippet + server-side dataLayer push.
 */
add_action('wp_head', function (): void {
    $gtm_id = ce_get_gtm_id();
    if (!$gtm_id) return;

    // Build server-side dataLayer context
    $dl = ['page_type' => ce_get_page_type()];
    if (is_singular('ce_property')) {
        $id = get_the_ID();
        $city_terms = get_the_terms($id, 'ce_city');
        $dl['property_id']     = $id;
        $dl['property_name']   = get_the_title($id);
        $dl['property_city']   = $city_terms ? $city_terms[0]->name : '';
        $dl['property_status'] = get_field('ce_status', $id) ?: '';
    }
    ?>
    <script>window.dataLayer=window.dataLayer||[];dataLayer.push(<?php echo wp_json_encode($dl, JSON_UNESCAPED_SLASHES); ?>);</script>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js($gtm_id); ?>');</script>
    <!-- End Google Tag Manager -->
    <?php
}, 1);

/**
 * Output GTM noscript body snippet.
 */
add_action('wp_body_open', function (): void {
    $gtm_id = ce_get_gtm_id();
    if (!$gtm_id) return;
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm_id); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
});

/**
 * Map current WP template to a page_type string for dataLayer.
 */
function ce_get_page_type(): string {
    if (is_front_page())            return 'home';
    if (is_singular('ce_property')) return 'property';
    if (is_page('projects'))        return 'projects';
    if (is_page('how-it-works'))    return 'how-it-works';
    if (is_page('about'))           return 'about';
    if (is_page('contact'))         return 'contact';
    if (is_singular('post'))        return 'blog-post';
    if (is_home() || is_archive())  return 'blog';
    return 'page';
}
```

- [ ] **Step 2: Set GTM ID in WP Admin → Site Settings → Content & Legal, then verify**

View page source on the homepage — expect:
```html
<script>window.dataLayer=window.dataLayer||[];dataLayer.push({"page_type":"home"});</script>
<!-- Google Tag Manager -->
```

On a single property page, `dataLayer` push should include `property_id`, `property_name`, `property_city`, `property_status`.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/ga4-tracking.php
git commit -m "feat: GA4/GTM reads container ID from ACF options, adds server-side dataLayer context"
```

---

## Task 10: Update Schema Markup — Add Review, FAQPage, Article

**Files:**
- Modify: `wp-content/themes/crowns-estates/inc/schema-markup.php`

- [ ] **Step 1: Rewrite schema-markup.php**

```php
<?php
// wp-content/themes/crowns-estates/inc/schema-markup.php
defined('ABSPATH') || exit;

add_action('wp_head', function (): void {
    $schemas = [];

    // ── Sitewide: RealEstateAgent ──────────────────────────────────────────
    $office_address = function_exists('get_field') ? (get_field('ce_office_address', 'option') ?: '') : '';
    $schemas[] = [
        '@context'    => 'https://schema.org',
        '@type'       => 'RealEstateAgent',
        'name'        => 'Crowns Estates',
        'url'         => home_url('/'),
        'email'       => 'info@crownsestates.co.uk',
        'description' => 'UK-registered real estate agency specialising in Saudi Arabian property investment.',
        'areaServed'  => 'Saudi Arabia',
        'address'     => $office_address ? ['@type' => 'PostalAddress', 'streetAddress' => $office_address] : null,
    ];

    // ── Single Property: RealEstateListing ────────────────────────────────
    if (is_singular('ce_property')) {
        $id         = get_the_ID();
        $price      = (float) get_field('ce_price_from', $id);
        $currency   = get_field('ce_currency', $id) ?: 'SAR';
        $city_terms = get_the_terms($id, 'ce_city');
        $city       = $city_terms ? $city_terms[0]->name : 'Saudi Arabia';
        $image      = get_the_post_thumbnail_url($id, 'large') ?: '';
        $floor_size = (float) get_field('ce_size_sqm', $id);
        $bedrooms   = (int) get_field('ce_bedrooms', $id);

        $listing = [
            '@context'        => 'https://schema.org',
            '@type'           => 'RealEstateListing',
            'name'            => get_the_title($id),
            'description'     => get_field('ce_short_description', $id) ?: '',
            'url'             => get_permalink($id),
            'datePosted'      => get_the_date('c', $id),
            'contentLocation' => ['@type' => 'Place', 'name' => "{$city}, Saudi Arabia"],
        ];
        if ($image)      $listing['image']           = $image;
        if ($floor_size) $listing['floorSize']        = ['@type' => 'QuantitativeValue', 'value' => $floor_size, 'unitCode' => 'MTK'];
        if ($bedrooms)   $listing['numberOfRooms']    = $bedrooms;
        if ($price)      $listing['offers']           = ['@type' => 'Offer', 'price' => $price, 'priceCurrency' => $currency];
        $schemas[] = $listing;
    }

    // ── Pages with Testimonials: Review ───────────────────────────────────
    if (is_front_page() || is_page('about')) {
        $testimonials = get_posts([
            'post_type'      => 'ce_testimonial',
            'posts_per_page' => 3,
            'meta_query'     => [['key' => 'ce_featured', 'value' => '1', 'compare' => '=']],
        ]);
        foreach ($testimonials as $t) {
            $rating = (int) get_field('ce_rating', $t->ID);
            $schemas[] = [
                '@context'     => 'https://schema.org',
                '@type'        => 'Review',
                'author'       => ['@type' => 'Person', 'name' => get_field('ce_client_name', $t->ID) ?: $t->post_title],
                'reviewBody'   => get_field('ce_quote', $t->ID) ?: '',
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $rating, 'bestRating' => 5],
                'itemReviewed' => ['@type' => 'RealEstateAgent', 'name' => 'Crowns Estates'],
            ];
        }
    }

    // ── How It Works: FAQPage ─────────────────────────────────────────────
    if (is_page('how-it-works')) {
        $page_id = get_queried_object_id();
        $faqs    = get_field('ce_faq_items', $page_id); // ACF repeater: ce_question, ce_answer
        if ($faqs) {
            $entities = array_map(fn($faq) => [
                '@type'          => 'Question',
                'name'           => $faq['ce_question'] ?? '',
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['ce_answer'] ?? ''],
            ], $faqs);
            $schemas[] = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities];
        }
    }

    // ── Single Blog Post: Article ─────────────────────────────────────────
    if (is_singular('post')) {
        $id    = get_the_ID();
        $thumb = get_the_post_thumbnail_url($id, 'large') ?: '';
        $article = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title($id),
            'datePublished' => get_the_date('c', $id),
            'dateModified'  => get_the_modified_date('c', $id),
            'url'           => get_permalink($id),
            'author'        => ['@type' => 'Organization', 'name' => 'Crowns Estates'],
        ];
        if ($thumb) $article['image'] = $thumb;
        $schemas[] = $article;
    }

    // Output all schemas
    foreach (array_filter($schemas) as $schema) {
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}, 1);
```

- [ ] **Step 2: Verify — view page source on homepage and check for `RealEstateAgent` JSON-LD block. View source on a single property and check for `RealEstateListing`. View source on a blog post and check for `Article`.**

Use Google's Rich Results Test at `search.google.com/test/rich-results` if site is publicly accessible.

- [ ] **Step 3: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/schema-markup.php
git commit -m "feat: expand schema markup — Review, FAQPage, Article schemas added"
```

---

## Task 11: Update JS — Nonces, Currency Toggle, Calculator Rates

**Files:**
- Modify: `wp-content/themes/crowns-estates/inc/enqueue.php`
- Modify: `wp-content/themes/crowns-estates/js/modal.js`
- Modify: `wp-content/themes/crowns-estates/js/currency-toggle.js`
- Modify: `wp-content/themes/crowns-estates/js/calculator.js`

- [ ] **Step 1: Update enqueue.php to pass nonce and calculator rates to JS**

Read the current `enqueue.php` and add `wp_localize_script` calls after the main script is enqueued:

```php
// After wp_enqueue_script('crowns-estates-main', ...) add:
wp_localize_script('crowns-estates-main', 'CE', [
    'restUrl'   => esc_url_raw(rest_url('ce/v1/')),
    'nonce'     => wp_create_nonce('ce_enquiry_nonce'),
    'wpNonce'   => wp_create_nonce('wp_rest'),
    'calcRates' => [
        'registration' => (float) (function_exists('get_field') ? (get_field('ce_calc_registration_fee', 'option') ?: 2.5) : 2.5),
        'vat'          => (float) (function_exists('get_field') ? (get_field('ce_calc_vat', 'option') ?: 5) : 5),
        'agency'       => (float) (function_exists('get_field') ? (get_field('ce_calc_agency_fee', 'option') ?: 2) : 2),
    ],
    'currencySymbols' => ['GBP' => '£', 'SAR' => 'SAR ', 'USD' => '$'],
]);
```

- [ ] **Step 2: Update modal.js — add nonce to all POST requests**

Find the `fetch('/wp-json/ce/v1/enquiry'` call in `modal.js` and update it:

```js
// Before (example of existing pattern):
fetch('/wp-json/ce/v1/enquiry', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
})

// After:
fetch(CE.restUrl + 'enquiry', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ ...data, nonce: CE.nonce })
})
```

Apply the same pattern for the brochure-gate fetch (use `CE.restUrl + 'brochure-gate'`).

- [ ] **Step 3: Update currency-toggle.js — use CE.restUrl**

```js
// Before:
fetch('/wp-json/ce/v1/rates')

// After:
fetch(CE.restUrl + 'rates')
```

- [ ] **Step 4: Update calculator.js — read rates from CE.calcRates instead of hardcoded values**

```js
// Before (hardcoded):
const REGISTRATION_FEE = 0.025;
const VAT = 0.05;
const AGENCY_FEE = 0.02;

// After:
const REGISTRATION_FEE = (CE.calcRates.registration || 2.5) / 100;
const VAT              = (CE.calcRates.vat || 5) / 100;
const AGENCY_FEE       = (CE.calcRates.agency || 2) / 100;
```

- [ ] **Step 5: Verify — open browser console on the frontend. Confirm `CE` object exists with `restUrl`, `nonce`, and `calcRates`. Submit the Register Interest form and confirm the request includes `nonce` in the payload.**

- [ ] **Step 6: Commit**

```bash
git add wp-content/themes/crowns-estates/inc/enqueue.php wp-content/themes/crowns-estates/js/modal.js wp-content/themes/crowns-estates/js/currency-toggle.js wp-content/themes/crowns-estates/js/calculator.js
git commit -m "feat: pass nonce and ACF rates to JS via wp_localize_script, update fetch calls"
```

---

## Task 12: Final Wiring — Verify Full Flow End-to-End

- [ ] **Step 1: Confirm mu-plugin loads cleanly**

```bash
wp eval 'echo function_exists("ce_register_property_cpt") ? "CPT OK" : "CPT MISSING";'
wp eval 'echo function_exists("ce_handle_enquiry") ? "Enquiry OK" : "Enquiry MISSING";'
wp eval 'echo function_exists("ce_display_price") ? "Currency OK" : "Currency MISSING";'
```
Expected: All three print OK.

- [ ] **Step 2: Confirm DB table has correct schema**

```bash
wp db query "DESCRIBE $(wp db prefix)ce_enquiries;"
```
Expected: columns `id`, `name`, `email`, `phone`, `message`, `property_id`, `source`, `gdpr_consent`, `ip_address`, `status`, `created_at`.

- [ ] **Step 3: Submit a test enquiry via REST and verify DB row + emails**

```bash
NONCE=$(wp eval 'echo wp_create_nonce("ce_enquiry_nonce");')
curl -s -X POST http://localhost/wp-json/ce/v1/enquiry \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"Test User\",\"email\":\"test@example.com\",\"gdpr_consent\":true,\"source\":\"contact_form\",\"nonce\":\"$NONCE\"}" \
  | python3 -m json.tool
```
Expected: `{"success": true}`

```bash
wp db query "SELECT id, name, email, status, source FROM $(wp db prefix)ce_enquiries ORDER BY id DESC LIMIT 1;"
```
Expected: Row with `status = new`, `source = contact_form`.

- [ ] **Step 4: Verify CSV export works (Admin only)**

Log in to WP admin, visit `/wp-admin/admin.php?page=ce-enquiries`, click "Export CSV" — expect file download with correct headers.

- [ ] **Step 5: Verify currency toggle**

Open the site frontend, switch currency using the toggle, inspect `ce-price` elements — data attributes should be present and displayed values should update without page reload.

- [ ] **Step 6: Final commit**

```bash
git add -A
git commit -m "chore: verify full backend integration — mu-plugin, enquiry flow, admin dashboard, CSV export, currency"
```

---

## Self-Review Notes

**Spec coverage check:**

| Spec Section | Covered In |
|---|---|
| mu-plugin architecture | Task 1 |
| DB schema v1.1 | Task 2 |
| Currency helpers + /rates endpoint | Task 3 |
| POST /enquiry + nonce | Task 4 |
| POST /brochure-gate + signed URL | Task 4 |
| Email templates + transport | Task 5 |
| Daily digest cron | Task 5 |
| GET /enquiries + export + PATCH | Task 6 |
| ACF options (all fields) | Task 7 |
| Admin dashboard stat cards | Task 8 |
| Enquiry admin page + status update | Task 8 |
| User role restrictions | Task 8 |
| GA4/GTM from ACF + dataLayer | Task 9 |
| Schema: RealEstateListing, Review, FAQPage, Article | Task 10 |
| Nonce in JS form submissions | Task 11 |
| Calculator reads from ACF options | Task 11 |

**All spec sections covered. No TBDs or placeholders.**
