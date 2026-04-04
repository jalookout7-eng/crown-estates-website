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

/**
 * Admin scripts — dashboard only.
 */
function ce_admin_enqueue($hook) {
    if ($hook !== 'index.php') return;

    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js', [], '4.0', true);
    wp_enqueue_script('ce-admin-dashboard', get_template_directory_uri() . '/js/admin-dashboard.js', ['chartjs'], '1.0', true);
}
add_action('admin_enqueue_scripts', 'ce_admin_enqueue');
