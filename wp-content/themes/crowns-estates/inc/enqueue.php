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
